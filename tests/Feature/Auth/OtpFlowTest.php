<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Verifikasi akun dan atur ulang kata sandi lewat kode OTP email (Figma). */
class OtpFlowTest extends TestCase
{
    use RefreshDatabase;

    private function otp(): OtpService
    {
        return app(OtpService::class);
    }

    private function pendingUser(string $email = 'baru@example.com'): array
    {
        Mail::fake();
        $user = User::factory()->create(['email' => $email]);
        $code = $this->otp()->issue($email, EmailOtp::PURPOSE_REGISTER);

        return [$user, $code];
    }

    private function wrongCode(string $right): string
    {
        return $right === '000000' ? '111111' : '000000';
    }

    private function sessionFor(string $email, string $purpose = 'register'): array
    {
        return ['otp.email' => $email, 'otp.purpose' => $purpose];
    }

    // ---- verifikasi akun -----------------------------------------------------------------

    public function test_verify_page_needs_a_pending_email_in_the_session(): void
    {
        $this->get('/verifikasi-akun')->assertRedirect(route('login'));
    }

    public function test_verify_page_shows_the_email_and_resend_countdown(): void
    {
        [$user] = $this->pendingUser();

        $this->withSession($this->sessionFor($user->email))->get('/verifikasi-akun')
            ->assertOk()
            ->assertSee('Verifikasi Akun')
            ->assertSee('baru@example.com')
            ->assertSee('Kirim Ulang Kode');
    }

    public function test_the_code_is_stored_hashed_not_in_plain_text(): void
    {
        [, $code] = $this->pendingUser();

        $row = EmailOtp::first();
        $this->assertNotSame($code, $row->code_hash);
        $this->assertSame(64, strlen($row->code_hash));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function test_correct_code_verifies_the_account_and_consumes_the_code(): void
    {
        [$user, $code] = $this->pendingUser();

        $this->withSession($this->sessionFor($user->email))
            ->post('/verifikasi-akun', ['code' => $code])
            ->assertRedirect(route('account.created'))
            ->assertSessionHas('account_created');

        $this->assertNotNull($user->refresh()->email_verified_at);
        $this->assertDatabaseMissing('email_otps', ['email' => $user->email]);
    }

    public function test_wrong_code_is_rejected_and_counts_as_an_attempt(): void
    {
        [$user, $code] = $this->pendingUser();

        $this->withSession($this->sessionFor($user->email))
            ->from('/verifikasi-akun')
            ->post('/verifikasi-akun', ['code' => $this->wrongCode($code)])
            ->assertRedirect('/verifikasi-akun')
            ->assertSessionHasErrors('code');

        $this->assertNull($user->refresh()->email_verified_at);
        $this->assertSame(1, EmailOtp::first()->attempts);
    }

    public function test_code_must_be_six_digits(): void
    {
        [$user] = $this->pendingUser();

        $this->withSession($this->sessionFor($user->email))->post('/verifikasi-akun', ['code' => '12ab'])
            ->assertSessionHasErrors('code');
    }

    public function test_too_many_wrong_attempts_lock_the_code_even_for_the_right_one(): void
    {
        [$user, $code] = $this->pendingUser();

        for ($i = 0; $i < OtpService::MAX_ATTEMPTS; $i++) {
            $this->withSession($this->sessionFor($user->email))->post('/verifikasi-akun', ['code' => $this->wrongCode($code)]);
        }

        $this->withSession($this->sessionFor($user->email))->post('/verifikasi-akun', ['code' => $code])
            ->assertSessionHasErrors('code');
        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_expired_code_is_rejected(): void
    {
        [$user, $code] = $this->pendingUser();

        $this->travel(OtpService::TTL_MINUTES + 1)->minutes();

        $this->withSession($this->sessionFor($user->email))->post('/verifikasi-akun', ['code' => $code])
            ->assertSessionHasErrors('code');
        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_resend_is_blocked_during_the_cooldown_and_allowed_after(): void
    {
        [$user] = $this->pendingUser();
        $this->assertGreaterThan(0, $this->otp()->resendIn($user->email, 'register'));

        $this->withSession($this->sessionFor($user->email))->from('/verifikasi-akun')
            ->post('/verifikasi-akun/kirim-ulang')
            ->assertSessionHasErrors('code');
        Mail::assertSent(OtpCodeMail::class, 1); // hanya pengiriman awal

        $this->travel(OtpService::RESEND_SECONDS + 1)->seconds();

        $this->withSession($this->sessionFor($user->email))->from('/verifikasi-akun')
            ->post('/verifikasi-akun/kirim-ulang')
            ->assertSessionHas('status');
        Mail::assertSent(OtpCodeMail::class, 2);
    }

    public function test_a_new_code_replaces_the_old_one(): void
    {
        [$user, $old] = $this->pendingUser();
        $this->travel(OtpService::RESEND_SECONDS + 1)->seconds();
        $new = $this->otp()->issue($user->email, 'register');

        $this->assertSame(1, EmailOtp::count());
        if ($old !== $new) {
            $this->withSession($this->sessionFor($user->email))->post('/verifikasi-akun', ['code' => $old])
                ->assertSessionHasErrors('code');
        }
    }

    public function test_account_created_page_is_only_reachable_after_verification(): void
    {
        $this->get('/akun-berhasil')->assertRedirect(route('login'));
        $this->withSession(['account_created' => true])->get('/akun-berhasil')->assertOk()->assertSee('Akun Berhasil Dibuat');
    }

    // ---- login dengan akun yang menunggu verifikasi ---------------------------------------

    public function test_login_of_an_unverified_new_account_is_sent_to_verification(): void
    {
        [$user] = $this->pendingUser();

        $this->post('/login', ['email' => $user->email, 'password' => '12345'])
            ->assertRedirect(route('otp.show'));

        $this->assertGuest();
    }

    public function test_legacy_unverified_accounts_without_a_pending_code_can_still_log_in(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $this->post('/login', ['email' => $user->email, 'password' => '12345'])
            ->assertRedirect(route('homepage'));

        $this->assertAuthenticatedAs($user);
    }

    // ---- atur ulang kata sandi --------------------------------------------------------------

    public function test_reset_request_page_renders(): void
    {
        $this->get('/atur-ulang-kata-sandi')->assertOk()->assertSee('Atur Ulang Kata Sandi');
    }

    public function test_reset_request_does_not_reveal_whether_the_email_exists(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'ada@example.com']);

        $known = $this->post('/atur-ulang-kata-sandi', ['email' => 'ada@example.com']);
        $unknown = $this->post('/atur-ulang-kata-sandi', ['email' => 'tidakada@example.com']);

        $known->assertRedirect(route('otp.show'));
        $unknown->assertRedirect(route('otp.show'));
        Mail::assertSent(OtpCodeMail::class, 1);
        Mail::assertSent(OtpCodeMail::class, fn (OtpCodeMail $m) => $m->hasTo('ada@example.com') && $m->purpose === 'reset');
    }

    public function test_full_reset_flow_changes_the_password(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'lupa@example.com']);
        $this->post('/atur-ulang-kata-sandi', ['email' => 'lupa@example.com'])->assertRedirect(route('otp.show'));
        $code = null;
        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $m) use (&$code) {
            $code = $m->code;

            return true;
        });

        // belum diverifikasi: halaman kata sandi baru tertutup
        $this->get('/kata-sandi-baru')->assertRedirect(route('reset.request'));

        $this->post('/verifikasi-akun', ['code' => $code])->assertRedirect(route('reset.new'));
        $this->get('/kata-sandi-baru')->assertOk()->assertSee('Kata Sandi Baru');

        $this->post('/kata-sandi-baru', ['password' => 'sandi-baru-123', 'password_confirmation' => 'sandi-baru-123'])
            ->assertRedirect(route('reset.done'));

        $this->assertTrue(Hash::check('sandi-baru-123', $user->refresh()->password));
        $this->get('/kata-sandi-berhasil')->assertOk()->assertSee('Kata Sandi Berhasil Diubah');

        // izin reset sekali pakai
        $this->get('/kata-sandi-baru')->assertRedirect(route('reset.request'));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $code = $this->otp()->issue($user->email, 'reset');
        $this->withSession($this->sessionFor($user->email, 'reset'))->post('/verifikasi-akun', ['code' => $code]);

        $this->post('/kata-sandi-baru', ['password' => 'sandi-baru-123', 'password_confirmation' => 'beda'])
            ->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('12345', $user->refresh()->password));
    }

    // ---- halaman statis ------------------------------------------------------------------------

    public function test_static_pages_render(): void
    {
        foreach (['/mulai' => 'Selamat Datang di Dkampus', '/tentang-kami' => 'Tentang Kami', '/syarat-ketentuan' => 'Syarat dan Ketentuan', '/kebijakan-privasi' => 'Kebijakan Privasi'] as $uri => $text) {
            $this->get($uri)->assertOk()->assertSee($text);
        }
    }

    public function test_old_prototype_urls_redirect_to_the_real_flow(): void
    {
        $this->get('/code-verification')->assertRedirect('/verifikasi-akun');
        $this->get('/input-registrasi')->assertRedirect('/daftar');
    }
}
