<?php

namespace Tests\Feature;

use App\Models\Data_umkm;
use App\Models\history;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakeFirebaseDatabase;
use Tests\TestCase;

/**
 * Halaman kurir. Firebase diganti FakeFirebaseDatabase (hanya baca); alur yang menulis ke
 * Firebase/Telegram (ambil, selesaikan, batalkan order) sengaja tidak diuji di sini.
 */
class CourierPagesTest extends TestCase
{
    use RefreshDatabase;

    private function courier(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'courier', 'nama_user' => 'Kurir Satu'], $attributes));
    }

    private function historyFor(User $courier, User $customer, array $overrides = []): history
    {
        return history::create(array_merge([
            'user_id' => $customer->id,
            'cour_id' => $courier->id,
            'umkm_id' => 0,
            'item' => '1 Mie Ayam (-)',
            'harga' => 10000,
            'ongkir' => 3000,
            'jarak' => 1.5,
            'status' => 'completed',
            'bukti' => null,
            'bukti_akhir' => 'bukti.png',
            'alasan' => null,
            'total_driver' => 13000,
            'kategori' => 'jastip',
            'order_id' => hash('sha256', uniqid('', true)),
        ], $overrides));
    }

    private function fakeFirebase(array $tree): void
    {
        $this->app->instance('firebase.database', new FakeFirebaseDatabase($tree));
    }

    private function orderNode(string $umkmName, mixed $umkmId, string $receiver = 'Budi'): array
    {
        return [
            'nama_umkm' => $umkmName,
            'umkm_link_address' => 'https://maps.example/umkm',
            'umkm_address' => 'Jl. UMKM No. 1',
            'orders' => [
                'item1' => ['umkm_id' => $umkmId, 'jumlah' => 2, 'nama' => 'Mie Ayam', 'catatan' => 'pedas'],
            ],
            'nama_penerima' => $receiver,
            'cust_link_address' => 'https://maps.example/cust',
            'cust_address' => 'Jl. Pelanggan No. 2',
            'total' => 30000,
            'ongkir' => 5000,
        ];
    }

    // ---- akses -------------------------------------------------------------------------

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/courier/dashboard')->assertRedirect();
        $this->assertGuest();
    }

    public function test_customer_cannot_open_courier_pages(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/courier/dashboard')->assertRedirect(route('homepage'));
        $this->actingAs($customer)->get('/courier/history')->assertRedirect(route('homepage'));
    }

    public function test_removed_orphan_order_detail_route_is_gone(): void
    {
        // Dulu mengarah ke view yang tidak ada (500); route-nya sudah dihapus
        $this->actingAs($this->courier())->get('/courier/order/5')->assertNotFound();
    }

    // ---- dashboard ---------------------------------------------------------------------

    public function test_dashboard_renders_empty_state_when_firebase_is_unavailable(): void
    {
        // Firebase dimatikan oleh phpunit.xml: dulu ini redirect()->back() (bisa berputar tanpa akhir)
        $response = $this->actingAs($this->courier())->get('/courier/dashboard');

        $response->assertOk();
        $response->assertSee('belum mengambil pesanan');
    }

    public function test_dashboard_renders_empty_state_when_there_is_no_active_order(): void
    {
        $this->fakeFirebase(['needToDeliver' => []]);

        $this->actingAs($this->courier())->get('/courier/dashboard')
            ->assertOk()
            ->assertSee('belum mengambil pesanan');
    }

    public function test_dashboard_lists_only_own_orders_with_a_phone_number_per_order(): void
    {
        $courier = $this->courier();
        $umkm = Data_umkm::factory()->create(['nama_umkm' => 'Warung Uji', 'no_telp_umkm' => '021555000']);
        $custA = User::factory()->create(['nama_user' => 'Cust A', 'no_telp' => '0811111111']);
        $custB = User::factory()->create(['nama_user' => 'Cust B', 'no_telp' => '0822222222']);
        $custOther = User::factory()->create(['no_telp' => '0899999999']);

        $this->fakeFirebase(['onProgress' => [
            "{$custA->id}-{$courier->id}" => $this->orderNode('Warung Uji', $umkm->id, 'Penerima A'),
            "{$custB->id}-{$courier->id}" => $this->orderNode('Jastip', 'Jastip', 'Penerima B'),
            "{$custOther->id}-9999" => $this->orderNode('Warung Lain', $umkm->id, 'Penerima Lain'),
        ]]);

        $response = $this->actingAs($courier)->get('/courier/dashboard');

        $response->assertOk();
        $response->assertSee('Pesanan yang harus diantar');
        $response->assertSee('Penerima A');
        $response->assertSee('Penerima B');
        $response->assertDontSee('Penerima Lain');
        // Telepon pelanggan per order (dulu semua order memakai nomor pelanggan terakhir)
        $response->assertSee('0811111111');
        $response->assertSee('0822222222');
        $response->assertDontSee('0899999999');
        // Telepon UMKM: nomor UMKM untuk order biasa, "-" untuk Jastip
        $response->assertSee('021555000');
    }

    public function test_dashboard_tolerates_a_deleted_umkm_and_customer(): void
    {
        $courier = $this->courier();

        $this->fakeFirebase(['onProgress' => [
            "777-{$courier->id}" => $this->orderNode('UMKM Hilang', 4242, 'Penerima X'),
        ]]);

        $this->actingAs($courier)->get('/courier/dashboard')
            ->assertOk()
            ->assertSee('Penerima X');
    }

    // ---- riwayat -----------------------------------------------------------------------

    public function test_history_page_works_for_a_courier_without_history(): void
    {
        // Dulu: variabel tidak terdefinisi -> view menerima null -> 500
        $this->actingAs($this->courier())->get('/courier/history')
            ->assertOk()
            ->assertSee('Belum ada riwayat pesanan');
    }

    public function test_history_lists_only_own_entries_newest_first(): void
    {
        $courier = $this->courier();
        $other = $this->courier();
        $customer = User::factory()->create();
        $umkm = Data_umkm::factory()->create(['nama_umkm' => 'Warung Riwayat']);

        $old = $this->historyFor($courier, $customer, ['umkm_id' => $umkm->id, 'item' => 'ITEM-LAMA']);
        $old->forceFill(['created_at' => now()->subDays(2)])->save();
        $this->historyFor($courier, $customer, ['umkm_id' => 0, 'item' => 'ITEM-BARU-JASTIP']);
        $this->historyFor($courier, $customer, ['umkm_id' => 987654, 'item' => 'ITEM-UMKM-HILANG']);
        $this->historyFor($other, $customer, ['item' => 'ITEM-KURIR-LAIN']);

        $response = $this->actingAs($courier)->get('/courier/history');

        $response->assertOk();
        $response->assertSee('Warung Riwayat');
        $response->assertSee('Jastip');
        $response->assertSee('UMKM tidak tersedia');
        $response->assertDontSee('ITEM-KURIR-LAIN');
        $response->assertSeeInOrder(['ITEM-UMKM-HILANG', 'ITEM-LAMA']);
    }

    public function test_history_detail_shows_the_real_date_and_amounts(): void
    {
        $courier = $this->courier();
        $customer = User::factory()->create(['nama_user' => 'Pelanggan Detail']);
        $history = $this->historyFor($courier, $customer, ['harga' => 12000, 'ongkir' => 3000]);
        $history->forceFill(['created_at' => '2024-06-15 08:30:00'])->save();

        $response = $this->actingAs($courier)->post('/courier/detail-history', [
            'id' => $history->id,
            'custId' => $customer->id,
        ]);

        $response->assertOk();
        $response->assertSee('Pelanggan Detail');
        $response->assertSee('2024-06-15 08:30:00');
        $response->assertDontSee('2024-12-12 12:12:12'); // placeholder lama
        $response->assertSee('Rp 15.000');
    }

    public function test_history_detail_is_404_for_unknown_or_foreign_entries(): void
    {
        $courier = $this->courier();
        $other = $this->courier();
        $customer = User::factory()->create();
        $foreign = $this->historyFor($other, $customer);

        $this->actingAs($courier)->post('/courier/detail-history', [
            'id' => $foreign->id,
            'custId' => $customer->id,
        ])->assertNotFound();

        $this->actingAs($courier)->post('/courier/detail-history', [
            'id' => 123456,
            'custId' => $customer->id,
        ])->assertNotFound();
    }

    // ---- halaman lain -------------------------------------------------------------------

    public function test_simple_courier_pages_render(): void
    {
        $courier = $this->courier();

        foreach (['/courier/profile', '/courier/order', '/courier/chats'] as $uri) {
            $this->actingAs($courier)->get($uri)->assertOk();
        }
    }

    public function test_navbar_links_use_named_courier_routes(): void
    {
        $html = $this->actingAs($this->courier())->get('/courier/history')->getContent();

        $this->assertStringContainsString('href="' . route('cour.history') . '"', $html);
        $this->assertStringContainsString('href="' . route('profile') . '"', $html);
        $this->assertStringContainsString('href="' . route('dashboardCourier') . '"', $html);
    }

    public function test_dashboard_and_profile_start_with_the_doctype(): void
    {
        // Navbar & script dulu berada di luar @section sehingga tercetak SEBELUM <!DOCTYPE>
        $courier = $this->courier();

        foreach (['/courier/dashboard', '/courier/profile'] as $uri) {
            $html = $this->actingAs($courier)->get($uri)->getContent();

            $this->assertStringStartsWith('<!DOCTYPE', ltrim($html), "{$uri} mencetak sesuatu sebelum <!DOCTYPE>");
            $this->assertStringContainsString('<nav', $html, "{$uri} kehilangan navbar kurir");
        }
    }

    public function test_chat_rooms_do_not_use_the_missing_mix_manifest(): void
    {
        // mix('js/performanceFirebase.js') membuat chat room kurir & pelanggan 500
        foreach (['pages/Courier/chatroom', 'pages/Users/ChatRoomPage'] as $view) {
            $this->assertStringNotContainsString("mix(", file_get_contents(resource_path("views/{$view}.blade.php")));
        }
    }

    public function test_chat_room_renders_with_and_without_messages(): void
    {
        $courier = $this->courier();
        $customer = User::factory()->create(['no_telp' => '08123456789']);
        $key = "{$customer->id}-{$courier->id}";

        $this->fakeFirebase(['chats' => [$key => [
            'cust_name' => 'Pelanggan Chat',
            '-Nabc' => ['msgs' => ['timestamp' => '2024-06-15 08:30:00', 'msg' => 'halo']],
        ]]]);
        $this->actingAs($courier)->post('/courier/room-chat', ['custId' => $customer->id])
            ->assertOk()
            ->assertSee('Pelanggan Chat')
            ->assertSee(route('chatpage'), false); // tombol kembali ke daftar chat kurir

        // Chat tanpa pesan bertimestamp: $date dulu tidak terdefinisi
        $this->fakeFirebase(['chats' => [$key => ['cust_name' => 'Pelanggan Chat']]]);
        $this->actingAs($courier)->post('/courier/room-chat', ['custId' => $customer->id])->assertOk();
    }

    // ---- profil ------------------------------------------------------------------------

    public function test_courier_can_change_phone_number(): void
    {
        $courier = $this->courier(['no_telp' => '0811000111']);

        $this->actingAs($courier)->patch('/courier/profile/phone', ['no_telp' => '0812999888'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'phone-updated');

        $this->assertSame('0812999888', $courier->refresh()->no_telp);
    }

    public function test_phone_number_must_be_valid_and_unique(): void
    {
        $courier = $this->courier(['no_telp' => '0811000111']);
        User::factory()->create(['no_telp' => '0855555555']);

        $this->actingAs($courier)->patch('/courier/profile/phone', ['no_telp' => '0855555555'])
            ->assertSessionHasErrorsIn('updatePhone', 'no_telp');
        $this->actingAs($courier)->patch('/courier/profile/phone', ['no_telp' => ''])
            ->assertSessionHasErrorsIn('updatePhone', 'no_telp');
        $this->actingAs($courier)->patch('/courier/profile/phone', ['no_telp' => '081234567890123'])
            ->assertSessionHasErrorsIn('updatePhone', 'no_telp');

        $this->assertSame('0811000111', $courier->refresh()->no_telp);
    }

    public function test_keeping_the_same_phone_number_is_allowed(): void
    {
        $courier = $this->courier(['no_telp' => '0811000111']);

        $this->actingAs($courier)->patch('/courier/profile/phone', ['no_telp' => '0811000111'])
            ->assertSessionHasNoErrors();
    }

    public function test_courier_profile_password_form_posts_to_the_password_endpoint(): void
    {
        $courier = $this->courier();

        $this->actingAs($courier)->get('/courier/profile')
            ->assertSee('action="' . route('password.update') . '"', false)
            ->assertSee('name="current_password"', false)
            ->assertSee('name="password_confirmation"', false);

        $this->actingAs($courier)->from('/courier/profile')->put('/password', [
            'current_password' => '12345',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertSessionHasNoErrors()->assertRedirect('/courier/profile');
    }
}
