<?php

namespace Tests\Feature;

use App\Models\Data_umkm;
use App\Models\Favorit;
use App\Models\Menu;
use App\Models\User;
use App\Models\history;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakeFirebaseDatabase;
use Tests\TestCase;

/** Halaman admin setelah redesain: dashboard, akun, resto, produk, transaksi, chat, profil. */
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $attrs = []): User
    {
        return User::factory()->create(['role' => 'admin', 'nama_user' => 'Rina Pratama'] + $attrs);
    }

    private function fakeFirebase(array $tree = []): void
    {
        $this->app->instance('firebase.database', new FakeFirebaseDatabase($tree));
    }

    private function umkmPayload(array $overrides = []): array
    {
        return array_merge([
            'nama_umkm' => 'Ayam Bude',
            'alamat' => 'Jl. Sukabirus No. 11',
            'open_time' => '09:00',
            'close_time' => '21:30',
            'no_telp_umkm' => '081234567890',
            'vip' => '1',
            'geo' => '-6.97,107.63',
            'link' => '',
            'logo_umkm' => UploadedFile::fake()->image('logo.png'),
            'products' => [
                ['nama_makanan' => 'Ayam Bakar', 'deskripsi' => 'Bakar kecap', 'harga' => 18000, 'promo' => 30, 'category' => 'ayam & bebek', 'image' => UploadedFile::fake()->image('ayam.jpg')],
                ['nama_makanan' => 'Es Teh', 'deskripsi' => 'Manis', 'harga' => 5000, 'promo' => '', 'category' => 'minuman', 'image' => UploadedFile::fake()->image('teh.jpg')],
            ],
        ], $overrides);
    }

    // ---- dashboard -----------------------------------------------------------------------

    public function test_dashboard_greets_the_admin_and_counts_firebase_orders(): void
    {
        $this->fakeFirebase([
            'needToDeliver' => ['a' => ['orderID' => 'A1'], 'b' => ['orderID' => 'B1']],
            'onProgress' => ['c' => ['orderID' => 'C1']],
        ]);
        Menu::factory()->create(['data_umkm_id' => Data_umkm::factory()->create()->id]);

        $this->actingAs($this->admin())->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Halo, Rina')
            ->assertSee('Menunggu driver')
            ->assertSee('Kelola Akun')
            ->assertSee('Kelola Resto');
    }

    public function test_dashboard_works_when_firebase_is_empty(): void
    {
        $this->fakeFirebase();

        $this->actingAs($this->admin())->get('/admin/dashboard')->assertOk()->assertSee('Halo, Rina');
    }

    // ---- akun ----------------------------------------------------------------------------

    public function test_account_list_shows_users_with_role_and_status(): void
    {
        User::factory()->create(['nama_user' => 'Budi Santoso', 'role' => 'courier', 'restriction' => 1]);

        $this->actingAs($this->admin())->get('/admin/account')
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Driver')
            ->assertSee('Non Aktif')
            ->assertSee('data-modal-open="user"', false);
    }

    public function test_admin_can_delete_an_account_and_its_personal_data(): void
    {
        $target = User::factory()->create(['role' => 'customer']);
        Favorit::create(['user_id' => $target->id, 'menu_id' => 1, 'data_umkm_id' => 1]);

        $this->actingAs($this->admin())->delete("/admin/account/{$target->id}")
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertNull($target->fresh());
        $this->assertSame(0, Favorit::where('user_id', $target->id)->count());
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete("/admin/account/{$admin->id}")->assertSessionHas('error2');

        $this->assertNotNull($admin->fresh());
    }

    public function test_the_last_admin_cannot_be_deleted_or_demoted(): void
    {
        $solo = $this->admin();
        // Admin lain yang sedang login tidak ada: kunci lewat dua admin lalu hapus satu, sisanya terakhir
        $other = $this->admin(['email' => 'other@example.test']);

        $this->actingAs($solo)->delete("/admin/account/{$other->id}")->assertSessionHas('success');
        $this->assertNotNull($solo->fresh());

        $this->actingAs($solo)->post('/admin/updateUser', ['id_user' => $solo->id, 'role' => 'customer', 'restriction' => 0])
            ->assertSessionHas('error2');
        $this->assertSame('admin', $solo->refresh()->role);
    }

    public function test_an_admin_cannot_block_their_own_account(): void
    {
        $admin = $this->admin();
        $this->admin(['email' => 'second@example.test']);

        $this->actingAs($admin)->post('/admin/updateUser', ['id_user' => $admin->id, 'role' => 'admin', 'restriction' => 1])
            ->assertSessionHas('error2');

        $this->assertFalse((bool) $admin->refresh()->restriction);
    }

    public function test_accounts_with_transaction_history_cannot_be_deleted(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        history::create([
            'user_id' => $customer->id, 'cour_id' => 99, 'umkm_id' => 0, 'item' => '[]', 'harga' => 10000,
            'ongkir' => 5000, 'status' => 'completed', 'order_id' => 'ORD-TEST-1',
        ]);

        $this->actingAs($this->admin())->delete("/admin/account/{$customer->id}")->assertSessionHas('error2');

        $this->assertNotNull($customer->fresh());
    }

    public function test_non_admins_cannot_delete_accounts(): void
    {
        $target = User::factory()->create(['role' => 'customer']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->delete("/admin/account/{$target->id}")->assertRedirect();

        $this->assertNotNull($target->fresh());
    }

    // ---- resto ---------------------------------------------------------------------------

    public function test_umkm_list_can_be_searched_and_filtered_by_category(): void
    {
        $ayam = Data_umkm::factory()->create(['nama_umkm' => 'Warung Ayam Sedap']);
        $teh = Data_umkm::factory()->create(['nama_umkm' => 'Kedai Teh Poci']);
        Menu::factory()->create(['data_umkm_id' => $ayam->id, 'category' => 'ayam & bebek']);
        Menu::factory()->create(['data_umkm_id' => $teh->id, 'category' => 'minuman']);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/umkm')->assertOk()->assertSee('Warung Ayam Sedap')->assertSee('Kedai Teh Poci');
        $this->actingAs($admin)->get('/admin/umkm?kategori='.urlencode('minuman'))->assertOk()->assertSee('Kedai Teh Poci')->assertDontSee('Warung Ayam Sedap');
        $this->actingAs($admin)->get('/admin/umkm?q=ayam')->assertOk()->assertSee('Warung Ayam Sedap')->assertDontSee('Kedai Teh Poci');
    }

    public function test_the_create_umkm_wizard_renders_the_form_hooks(): void
    {
        $this->actingAs($this->admin())->get('/admin/umkm/create')
            ->assertOk()
            ->assertSee('Informasi Resto')
            ->assertSee('name="alamat"', false)
            ->assertSee('id="geo"', false)
            ->assertSee('id="link"', false)
            ->assertSee('name="logo_umkm"', false);
    }

    public function test_the_create_umkm_wizard_does_not_load_google_maps(): void
    {
        // Alamat dan koordinat diisi manual; Places Autocomplete tidak dipakai lagi di Tambah Resto
        $this->actingAs($this->admin())->get('/admin/umkm/create')
            ->assertOk()
            ->assertDontSee('maps.googleapis.com', false)
            ->assertDontSee('data-places', false);
    }

    public function test_admin_can_create_an_umkm_with_its_first_products(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $response = $this->actingAs($this->admin())->post('/admin/umkm-save', $this->umkmPayload());

        $umkm = Data_umkm::where('nama_umkm', 'Ayam Bude')->firstOrFail();
        $response->assertRedirect(route('umkmEdit', $umkm))->assertSessionHas('success');
        $this->assertSame('1', (string) $umkm->vip);
        $this->assertSame('09:00', date('H:i', strtotime($umkm->open_time)));
        // Tanpa tautan dari Google Places, tautan peta dibuat dari alamat
        $this->assertStringContainsString('google.com/maps', $umkm->link);
        $this->assertSame(2, Menu::where('data_umkm_id', $umkm->id)->count());
        $this->assertEquals(30, Menu::where('nama_makanan', 'Ayam Bakar')->value('diskon'));
        $this->assertEquals(0, Menu::where('nama_makanan', 'Es Teh')->value('diskon'));
    }

    public function test_creating_an_umkm_is_validated_and_nothing_is_saved_on_error(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $bad = $this->umkmPayload(['geo' => '', 'open_time' => '25:99']);
        $bad['products'][1]['category'] = 'bukan-kategori';

        $this->actingAs($this->admin())->post('/admin/umkm-save', $bad)
            ->assertSessionHasErrors(['geo', 'open_time', 'products.1.category']);

        $this->assertSame(0, Data_umkm::count());
        $this->assertSame(0, Menu::count());
    }

    public function test_manually_typed_coordinates_are_checked_and_normalised(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $admin = $this->admin();

        foreach (['Jl. Sukabirus', '-6.97', '-6.97;107.63', '91x,107.63'] as $bad) {
            $this->actingAs($admin)->post('/admin/umkm-save', $this->umkmPayload(['geo' => $bad]))->assertSessionHasErrors('geo');
        }
        $this->assertSame(0, Data_umkm::count());

        $this->actingAs($admin)->post('/admin/umkm-save', $this->umkmPayload(['geo' => ' -6.9738 , 107.6301 ']))->assertSessionHasNoErrors();

        $this->assertSame('-6.9738,107.6301', Data_umkm::firstOrFail()->geo);
    }

    public function test_the_maps_link_is_optional_but_must_be_a_url(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->actingAs($this->admin())->post('/admin/umkm-save', $this->umkmPayload(['link' => 'bukan url']))->assertSessionHasErrors('link');

        $this->actingAs($this->admin())->post('/admin/umkm-save', $this->umkmPayload(['link' => 'https://maps.app.goo.gl/abc123']))->assertSessionHasNoErrors();
        $this->assertSame('https://maps.app.goo.gl/abc123', Data_umkm::firstOrFail()->link);
    }

    public function test_creating_an_umkm_requires_at_least_one_product(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/umkm-save', $this->umkmPayload(['products' => []]))
            ->assertSessionHasErrors('products');

        $this->assertSame(0, Data_umkm::count());
    }

    public function test_umkm_detail_lists_products_with_discount_and_edit_hooks(): void
    {
        $umkm = Data_umkm::factory()->create(['nama_umkm' => 'Ayam Bude']);
        Menu::factory()->create(['data_umkm_id' => $umkm->id, 'nama_makanan' => 'Ayam Bakar Signature', 'harga' => 18000, 'diskon' => 30, 'category' => 'ayam & bebek']);

        $this->actingAs($this->admin())->get("/admin/umkm/{$umkm->id}/edit")
            ->assertOk()
            ->assertSee('Ayam Bakar Signature')
            ->assertSee('Diskon 30%')
            ->assertSee('Rp 12.600')
            ->assertSee('data-modal-open="edit-product"', false)
            ->assertSee('name="edit_nama_umkm"', false);
    }

    public function test_umkm_detail_preselects_the_current_vip_choice(): void
    {
        $vip = Data_umkm::factory()->create(['vip' => '1']);
        $regular = Data_umkm::factory()->create(['vip' => '0']);
        $admin = $this->admin();

        // Kunci array numerik ('1' => ...) menjadi int di PHP; perbandingan ketat dulu membuat tidak ada radio yang tercentang
        $this->actingAs($admin)->get("/admin/umkm/{$vip->id}/edit")
            ->assertSeeInOrder(['name="edit_vip" value="1" id="vip-ya" class="peer sr-only" checked'], false);
        $this->actingAs($admin)->get("/admin/umkm/{$regular->id}/edit")
            ->assertSeeInOrder(['name="edit_vip" value="0" id="vip-tidak" class="peer sr-only" checked'], false);
    }

    public function test_deleting_an_umkm_redirects_to_the_list_with_a_message(): void
    {
        $umkm = Data_umkm::factory()->create();

        $this->actingAs($this->admin())->delete("/admin/umkm/{$umkm->id}")
            ->assertRedirect(route('umkm'))
            ->assertSessionHas('success');
    }

    // ---- produk --------------------------------------------------------------------------

    public function test_product_list_filters_by_umkm_category_and_search(): void
    {
        $a = Data_umkm::factory()->create(['nama_umkm' => 'Resto A']);
        $b = Data_umkm::factory()->create(['nama_umkm' => 'Resto B']);
        Menu::factory()->create(['data_umkm_id' => $a->id, 'nama_makanan' => 'Nasi Goreng', 'category' => 'aneka nasi']);
        Menu::factory()->create(['data_umkm_id' => $b->id, 'nama_makanan' => 'Es Jeruk', 'category' => 'minuman']);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/product')->assertOk()->assertSee('Nasi Goreng')->assertSee('Es Jeruk');
        $this->actingAs($admin)->get("/admin/product?umkm={$b->id}")->assertOk()->assertSee('Es Jeruk')->assertDontSee('Nasi Goreng');
        $this->actingAs($admin)->get('/admin/product?kategori=minuman')->assertOk()->assertSee('Es Jeruk')->assertDontSee('Nasi Goreng');
        $this->actingAs($admin)->get('/admin/product?q=nasi')->assertOk()->assertSee('Nasi Goreng')->assertDontSee('Es Jeruk');
    }

    public function test_admin_can_add_a_product_to_an_umkm(): void
    {
        Storage::fake('local');
        $umkm = Data_umkm::factory()->create(['nama_umkm' => 'Resto A']);

        $this->actingAs($this->admin())->post('/admin/products', [
            'umkm' => $umkm->id, 'nama_makanan' => 'Mie Ayam', 'deskripsi' => 'Enak', 'harga' => 15000, 'promo' => 10,
            'category' => 'mie', 'image' => UploadedFile::fake()->image('mie.jpg'),
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $menu = Menu::where('nama_makanan', 'Mie Ayam')->firstOrFail();
        $this->assertSame($umkm->id, (int) $menu->data_umkm_id);
        $this->assertEquals(10, $menu->diskon);
        $this->assertStringStartsWith('public/Resto A/', $menu->image);
    }

    public function test_adding_a_product_shows_validation_errors_instead_of_a_generic_failure(): void
    {
        $umkm = Data_umkm::factory()->create();

        $this->actingAs($this->admin())->post('/admin/products', ['umkm' => $umkm->id, 'nama_makanan' => 'X'])
            ->assertSessionHasErrors(['deskripsi', 'harga', 'category', 'image']);

        $this->assertSame(0, Menu::count());
    }

    public function test_the_last_product_of_an_umkm_cannot_be_deleted(): void
    {
        $umkm = Data_umkm::factory()->create();
        $only = Menu::factory()->create(['data_umkm_id' => $umkm->id]);

        $this->actingAs($this->admin())->delete("/admin/product/{$only->id}")->assertSessionHas('error2');

        $this->assertNotNull($only->fresh());
    }

    public function test_a_product_can_be_deleted_when_the_umkm_has_others(): void
    {
        $umkm = Data_umkm::factory()->create();
        $first = Menu::factory()->create(['data_umkm_id' => $umkm->id]);
        Menu::factory()->create(['data_umkm_id' => $umkm->id]);

        $this->actingAs($this->admin())->delete("/admin/product/{$first->id}")
            ->assertSessionHas('success')
            ->assertSessionMissing('error2');

        $this->assertNull($first->fresh());
    }

    // ---- transaksi, chat, profil ---------------------------------------------------------

    public function test_transaction_page_merges_firebase_orders_and_history(): void
    {
        $customer = User::factory()->create(['nama_user' => 'Sari Dewi']);
        $courier = User::factory()->create(['nama_user' => 'Joko Driver', 'role' => 'courier']);
        history::create([
            'user_id' => $customer->id, 'cour_id' => $courier->id, 'umkm_id' => 0, 'item' => '[]', 'harga' => 20000,
            'ongkir' => 5000, 'status' => 'completed', 'order_id' => 'abcdef123456', 'jarak' => 1500,
        ]);
        $this->fakeFirebase([
            'needToDeliver' => ['k1' => ['orderID' => 'ntd0000001', 'timestamp' => '2024-01-02 10:00:00', 'nama_penerima' => 'Tia Antri', 'total' => 15000, 'ongkir' => 4000, 'status' => 'searching', 'jarak' => 800]],
            'onProgress' => ['k2' => ['orderID' => 'opr0000002', 'timestamp' => '2024-01-02 11:00:00', 'nama_penerima' => 'Umar Jalan', 'total' => 25000, 'ongkir' => 6000, 'status' => 'on Delivery', 'jarak' => 2400]],
        ]);

        $this->actingAs($this->admin())->get('/admin/transaction')
            ->assertOk()
            ->assertSee('#TRXABCDEF1234')
            ->assertSee('Sari Dewi')
            ->assertSee('Tia Antri')
            ->assertSee('Umar Jalan')
            ->assertSee('Rp 25.000')
            ->assertSee('Belum diambil')
            ->assertSee('class="detail-button', false);
    }

    public function test_transaction_page_with_no_data_shows_the_empty_state(): void
    {
        $this->fakeFirebase();

        $this->actingAs($this->admin())->get('/admin/transaction')->assertOk()->assertSee('Belum ada transaksi');
    }

    public function test_chat_page_keeps_its_script_hooks(): void
    {
        $this->actingAs($this->admin())->get('/admin/chats')
            ->assertOk()
            ->assertSee('id="list-chat"', false)
            ->assertSee('id="room-chat"', false)
            ->assertSee('id="message-form"', false)
            ->assertSee('id="message-input"', false);
    }

    public function test_profile_page_uses_the_frame_of_the_role(): void
    {
        $this->actingAs($this->admin())->get('/profile')->assertOk()->assertSee('Admin DKampus')->assertSee('Kelola Akun', false);
        $this->actingAs(User::factory()->create(['role' => 'customer']))->get('/profile')->assertOk()->assertSee('Customer DKampus');
        $this->actingAs(User::factory()->create(['role' => 'courier']))->get('/profile')->assertOk()->assertSee('Driver DKampus');
    }
}
