<?php

namespace Tests\Feature;

use App\Models\Data_umkm;
use App\Models\Favorit;
use App\Models\Menu;
use App\Models\User;
use App\Support\Favorites;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakeFirebaseDatabase;
use Tests\TestCase;

/** Halaman customer (layouts/customer): beranda, jelajah, favorit, keranjang, chat. Firebase memakai FakeFirebaseDatabase (hanya baca). */
class CustomerPagesTest extends TestCase
{
    use RefreshDatabase;

    private function umkm(array $attributes = []): Data_umkm
    {
        return Data_umkm::factory()->create(array_merge(['nama_umkm' => 'Warung Uji', 'open_time' => '2024-01-01 00:00:00', 'close_time' => '2024-01-01 23:59:00'], $attributes));
    }

    private function menu(Data_umkm $umkm, array $attributes = []): Menu
    {
        return Menu::factory()->create(array_merge(['data_umkm_id' => $umkm->id, 'nama_makanan' => 'Mie Uji', 'harga' => 20000, 'diskon' => 0, 'rating' => '4.5'], $attributes));
    }

    private function customer(): User
    {
        Favorites::flush();

        return User::factory()->create(['role' => 'customer', 'nama_user' => 'Pelanggan Uji']);
    }

    // ---- beranda & jelajah ---------------------------------------------------------------

    public function test_homepage_renders_for_guests_with_login_actions(): void
    {
        $umkm = $this->umkm();
        $this->menu($umkm, ['diskon' => 20]);

        $this->get('/')->assertOk()
            ->assertSee('Resto Terdekat')
            ->assertSee('Rekomendasi Makanan')
            ->assertSee('Warung Uji')
            ->assertSee('Mie Uji')
            ->assertSee('Masuk')
            ->assertDontSee('Menu Utama'); // panel akun hanya untuk pengguna login
    }

    public function test_homepage_shows_the_account_panel_for_a_logged_in_customer(): void
    {
        $this->umkm();

        $this->actingAs($this->customer())->get('/')->assertOk()
            ->assertSee('Menu Utama')
            ->assertSee('Pelanggan Uji')
            ->assertSee('Konfirmasi Logout');
    }

    public function test_the_discount_percentage_and_discounted_price_are_shown(): void
    {
        $this->menu($this->umkm(), ['harga' => 25000, 'diskon' => 20]);

        $this->get('/')->assertOk()->assertSee('-20%')->assertSee('20.000')->assertSee('25.000');
    }

    public function test_all_promo_urls_render_one_view(): void
    {
        $this->menu($this->umkm(), ['diskon' => 30, 'category' => 'mie']);

        foreach (['/promo', '/promo/semua', '/promo/special', '/promo/category/mie'] as $uri) {
            $this->get($uri)->assertOk()->assertSee('Promo Spesial')->assertSee('Menu Diskon');
        }
        $this->get('/promo/category/tidak-ada')->assertOk()->assertSee('Belum ada promo');
    }

    public function test_category_search_and_recommendation_pages_render(): void
    {
        $umkm = $this->umkm();
        $this->menu($umkm, ['category' => 'mie']);

        $this->get('/kategori/mie')->assertOk()->assertSee('Mie Uji');
        $this->get('/kategori/kosong')->assertOk()->assertSee('Belum ada menu');
        $this->get('/search?keyword=Mie')->assertOk()->assertSee('Mie Uji')->assertSee('value="Mie"', false);
        $this->get('/search?keyword=Mie&filter=umkm')->assertOk()->assertSee('Tidak ada hasil');
        $this->get('/search?keyword=Warung&filter=umkm')->assertOk()->assertSee('Warung Uji');
        $this->get('/rekomendasi-menu')->assertOk()->assertSee('Mie Uji');
        $this->get('/rekomendasi-warung')->assertOk()->assertSee('Warung Uji');
    }

    public function test_warung_and_food_detail_pages_render(): void
    {
        $umkm = $this->umkm();
        $menu = $this->menu($umkm, ['harga' => 30000, 'diskon' => 10, 'deskripsi' => 'Enak sekali']);

        $this->get('/detail-warung/'.rawurlencode($umkm->nama_umkm))->assertOk()->assertSee('Warung Uji')->assertSee('Mie Uji')->assertSee('Beli');
        $this->get('/detail-makanan/'.rawurlencode($menu->nama_makanan))->assertOk()
            ->assertSee('Pesan Sekarang')
            ->assertSee('27.000')       // harga setelah diskon
            ->assertSee('name="catatan"', false)
            ->assertSee('name="quantity"', false)
            ->assertSee('action="'.url('/detail-makanan/'.$menu->id).'"', false);
    }

    public function test_change_password_requires_login_and_posts_to_the_password_endpoint(): void
    {
        $this->get('/change-password')->assertRedirect();

        $this->actingAs($this->customer())->get('/change-password')->assertOk()
            ->assertSee('action="'.route('password.update').'"', false)
            ->assertSee('name="current_password"', false);
    }

    // ---- favorit -------------------------------------------------------------------------

    public function test_favorites_page_asks_guests_to_log_in(): void
    {
        $this->get('/favorit')->assertOk()->assertSee('Masuk untuk melihat favorit');
    }

    public function test_toggling_a_favorite_requires_login_and_updates_the_favorites_page(): void
    {
        $menu = $this->menu($this->umkm());
        $user = $this->customer();

        $this->postJson('/favoritStore/'.$menu->id)->assertUnauthorized();

        $this->actingAs($user)->postJson('/favoritStore/'.$menu->id)->assertOk();
        $this->assertSame(1, Favorit::where('user_id', $user->id)->where('menu_id', $menu->id)->count());

        Favorites::flush();
        $this->actingAs($user)->get('/favorit')->assertOk()->assertSee('Mie Uji')->assertSee('Warung Uji');

        $this->actingAs($user)->postJson('/favoritStore/'.$menu->id)->assertOk();
        $this->assertSame(0, Favorit::where('user_id', $user->id)->count());
    }

    public function test_food_cards_mark_favorited_menus_as_pressed(): void
    {
        $menu = $this->menu($this->umkm());
        $user = $this->customer();
        Favorit::create(['user_id' => $user->id, 'menu_id' => $menu->id, 'data_umkm_id' => null]);
        Favorites::flush();

        $html = $this->actingAs($user)->get('/')->getContent();
        $this->assertStringContainsString('data-fav="'.$menu->id.'" aria-pressed="true"', $html);
    }

    // ---- keranjang, status, chat (Firebase hanya dibaca) -----------------------------------------

    private function fakeFirebase(array $tree): void
    {
        $this->app->instance('firebase.database', new FakeFirebaseDatabase($tree));
    }

    public function test_the_cart_page_lists_items_with_total_and_address(): void
    {
        $umkm = $this->umkm();
        $menu = $this->menu($umkm, ['nama_makanan' => 'Bakmie Uji']);
        $user = $this->customer();
        $user->addresses()->create(['address' => 'Jl. Uji 1', 'link' => 'x', 'nama_alamat' => 'Kost Uji', 'geo' => '1,1', 'utama' => 1]);

        $this->fakeFirebase(['cart' => [$user->id => ['orders' => [
            'item1' => ['id' => $menu->id, 'nama' => 'Bakmie Uji', 'harga' => 20000, 'umkm_id' => $umkm->id, 'jumlah' => 2, 'catatan' => '-'],
        ]]]]);

        $this->actingAs($user)->get('/pesanan')->assertOk()
            ->assertSee('Warung Uji')
            ->assertSee('Bakmie Uji')
            ->assertSee('Kost Uji')
            ->assertSee('name="selected_address_id"', false)
            ->assertSee('action="'.route('checkout').'"', false);
    }

    public function test_the_empty_cart_shows_a_prompt(): void
    {
        $user = $this->customer();
        $user->addresses()->create(['address' => 'Jl. Uji 1', 'link' => 'x', 'nama_alamat' => 'Kost Uji', 'geo' => '1,1', 'utama' => 1]);
        $this->fakeFirebase([]);

        $this->actingAs($user)->get('/pesanan')->assertOk()->assertSee('Ups, pesanan kamu kosong nih');
    }

    public function test_the_status_page_renders_with_and_without_history(): void
    {
        $user = $this->customer();
        $this->fakeFirebase([]);

        $this->actingAs($user)->get('/pesanan/status')->assertOk()->assertSee('Proses')->assertSee('belum pernah melakukan transaksi');
    }

    public function test_chat_list_and_address_pages_render_for_a_logged_in_customer(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->get('/chats')->assertOk()->assertSee('id="chat-list"', false)->assertSee('action="'.route('chatWa').'"', false);
        $this->actingAs($user)->get('/daftar-alamat')->assertOk()->assertSee('Belum ada alamat');
    }

    public function test_customer_pages_start_with_the_doctype_and_have_a_navbar(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringStartsWith('<!DOCTYPE', ltrim($html));
        $this->assertStringContainsString('<nav', $html);
    }
}
