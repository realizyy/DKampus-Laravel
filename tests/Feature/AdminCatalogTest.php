<?php

namespace Tests\Feature;

use App\Models\Data_umkm;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Edit produk (menu) dan UMKM oleh admin. */
class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function menuPayload(Data_umkm $umkm, array $overrides = []): array
    {
        return array_merge([
            'edit_umkm' => $umkm->id,
            'edit_nama_makanan' => 'Mie Ayam Baru',
            'edit_deskripsi' => 'Deskripsi baru',
            'edit_harga' => 26000,
            'edit_promo' => 35,
            'edit_category' => 'mie',
        ], $overrides);
    }

    private function umkmPayload(array $overrides = []): array
    {
        return array_merge([
            'edit_nama_umkm' => 'UMKM Baru',
            'edit_alamat' => 'Jl. Baru 1',
            'edit_open_time' => '10:15',
            'edit_close_time' => '22:00',
            'edit_no_telp_umkm' => '081200000000',
            'edit_vip' => '1',
            'edit_link' => 'https://maps.example/baru',
            'edit_geo' => '-6.9,107.7',
        ], $overrides);
    }

    // ---- menu --------------------------------------------------------------------------

    public function test_menu_update_stores_the_discount_in_diskon_and_leaves_other_menus_alone(): void
    {
        $umkm = Data_umkm::factory()->create();
        $target = Menu::factory()->create(['data_umkm_id' => $umkm->id, 'diskon' => 20]);
        $other = Menu::factory()->create(['data_umkm_id' => $umkm->id, 'diskon' => 5]);

        $this->actingAs($this->admin())->put("/admin/product/{$target->id}", $this->menuPayload($umkm))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $target->refresh();
        $this->assertSame('Mie Ayam Baru', $target->nama_makanan);
        $this->assertEquals(26000, $target->harga);
        $this->assertEquals(35, $target->diskon);
        $this->assertSame('mie', $target->category);
        $this->assertEquals(5, $other->refresh()->diskon);
    }

    public function test_menu_update_rejects_an_invalid_discount_and_category_is_required(): void
    {
        $umkm = Data_umkm::factory()->create();
        $menu = Menu::factory()->create(['data_umkm_id' => $umkm->id, 'diskon' => 20]);

        $this->actingAs($this->admin())->put("/admin/product/{$menu->id}", $this->menuPayload($umkm, ['edit_promo' => 150]))
            ->assertSessionHasErrors('edit_promo');
        $this->actingAs($this->admin())->put("/admin/product/{$menu->id}", $this->menuPayload($umkm, ['edit_category' => '']))
            ->assertSessionHasErrors('edit_category');

        $this->assertEquals(20, $menu->refresh()->diskon);
    }

    public function test_menu_update_can_move_to_another_umkm_and_replace_the_image(): void
    {
        Storage::fake('local');
        $from = Data_umkm::factory()->create();
        $to = Data_umkm::factory()->create(['nama_umkm' => 'Tujuan']);
        $menu = Menu::factory()->create(['data_umkm_id' => $from->id, 'image' => 'lama.jpg']);

        $this->actingAs($this->admin())->put("/admin/product/{$menu->id}", $this->menuPayload($to, [
            'edit_image' => UploadedFile::fake()->image('baru.png'),
        ]))->assertSessionHasNoErrors();

        $menu->refresh();
        $this->assertSame($to->id, (int) $menu->data_umkm_id);
        $this->assertStringStartsWith('public/Tujuan/', $menu->image);
        Storage::disk('local')->assertExists($menu->image);
    }

    public function test_menu_update_keeps_the_old_image_when_none_is_uploaded(): void
    {
        $umkm = Data_umkm::factory()->create();
        $menu = Menu::factory()->create(['data_umkm_id' => $umkm->id, 'image' => 'lama.jpg']);

        $this->actingAs($this->admin())->put("/admin/product/{$menu->id}", $this->menuPayload($umkm))
            ->assertSessionHasNoErrors();

        $this->assertSame('lama.jpg', $menu->refresh()->image);
    }

    public function test_menu_edit_pages_render(): void
    {
        $umkm = Data_umkm::factory()->create();
        $menu = Menu::factory()->create(['data_umkm_id' => $umkm->id]);
        $admin = $this->admin();

        // /admin/product/{id}/edit dulu 500: view product_form tidak ada & product_update punya syntax error
        $this->actingAs($admin)->get("/admin/product/{$menu->id}/edit")
            ->assertOk()
            ->assertSee('name="edit_nama_makanan"', false)
            ->assertSee('name="edit_promo"', false);
        // /admin/umkm/{id}/edit dulu 500 untuk UMKM yang punya menu (route product.destroy tidak ada)
        $this->actingAs($admin)->get("/admin/umkm/{$umkm->id}/edit")->assertOk();
        $this->actingAs($admin)->get('/admin/product')->assertOk();
        $this->actingAs($admin)->get('/admin/umkm')->assertOk();
    }

    // ---- umkm --------------------------------------------------------------------------

    public function test_umkm_update_saves_all_fields(): void
    {
        $umkm = Data_umkm::factory()->create(['vip' => '0']);

        $this->actingAs($this->admin())->put("/admin/umkm/{$umkm->id}", $this->umkmPayload())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $umkm->refresh();
        $this->assertSame('UMKM Baru', $umkm->nama_umkm);
        $this->assertSame('1', (string) $umkm->vip);
        $this->assertSame('10:15', date('H:i', strtotime($umkm->open_time)));
        $this->assertSame('22:00', date('H:i', strtotime($umkm->close_time)));
        $this->assertSame('https://maps.example/baru', $umkm->link);
        $this->assertSame('-6.9,107.7', $umkm->geo);
    }

    public function test_umkm_update_is_validated_instead_of_blanking_columns(): void
    {
        $umkm = Data_umkm::factory()->create(['nama_umkm' => 'Nama Lama']);

        // Form lama mengirim nama field yang salah -> semua edit_* kosong
        $this->actingAs($this->admin())->put("/admin/umkm/{$umkm->id}", ['nama_umkm' => 'x', 'vip' => 1])
            ->assertSessionHasErrors(['edit_nama_umkm', 'edit_alamat', 'edit_open_time', 'edit_close_time', 'edit_no_telp_umkm', 'edit_vip']);

        $this->assertSame('Nama Lama', $umkm->refresh()->nama_umkm);
    }

    public function test_umkm_update_rejects_a_bad_vip_value_and_time_format(): void
    {
        $umkm = Data_umkm::factory()->create();

        $this->actingAs($this->admin())->put("/admin/umkm/{$umkm->id}", $this->umkmPayload(['edit_vip' => '2', 'edit_open_time' => '25:99']))
            ->assertSessionHasErrors(['edit_vip', 'edit_open_time']);
    }

    public function test_umkm_edit_page_posts_the_fields_the_controller_expects(): void
    {
        $umkm = Data_umkm::factory()->create(['vip' => '1']);
        $umkm->forceFill(['open_time' => '2024-01-01 08:00:00', 'close_time' => '2024-01-01 21:00:00', 'link' => 'https://maps.example/x', 'geo' => '-6,107'])->save();

        $this->actingAs($this->admin())->get("/admin/umkm/{$umkm->id}/edit")
            ->assertOk()
            ->assertSee('name="edit_nama_umkm"', false)
            ->assertSee('name="edit_open_time"', false)
            ->assertSee('name="edit_vip"', false)
            ->assertSee('value="https://maps.example/x"', false)
            ->assertSee('value="08:00"', false);
    }

    public function test_admin_can_delete_an_umkm(): void
    {
        $umkm = Data_umkm::factory()->create();

        $this->actingAs($this->admin())->delete("/admin/umkm/{$umkm->id}")->assertRedirect();

        $this->assertNull($umkm->fresh());
    }

    public function test_public_umkm_resource_route_is_not_exposed(): void
    {
        // Route::resource('umkm', 'UmkmController') tanpa auth dulu ada di luar grup admin
        $umkm = Data_umkm::factory()->create();

        $this->delete("/umkm/{$umkm->id}")->assertStatus(404);
        $this->put("/umkm/{$umkm->id}", ['edit_nama_umkm' => 'x'])->assertStatus(404);
        $this->assertNotNull($umkm->fresh());
    }
}
