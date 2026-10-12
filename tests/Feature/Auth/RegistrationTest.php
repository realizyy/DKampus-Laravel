<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'no_telp' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_new_users_can_register_as_customer(): void
    {
        Mail::fake();

        $response = $this->post('/register', $this->payload());

        // Setelah daftar user menunggu verifikasi kode OTP (tidak otomatis login)
        $response->assertRedirect(route('otp.show'));
        $response->assertSessionHas('otp.email', 'test@example.com');
        $this->assertGuest();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Test User', $user->nama_user);
        $this->assertSame('customer', $user->role);
        $this->assertNotSame('password123', $user->password);
        $this->assertNull($user->email_verified_at);

        Mail::assertSent(OtpCodeMail::class, fn (OtpCodeMail $mail) => $mail->hasTo('test@example.com') && $mail->purpose === 'register');
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', $this->payload(['no_telp' => '081200000001']));

        $response->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'test@example.com')->count());
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', $this->payload(['password_confirmation' => 'different']));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
