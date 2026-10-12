<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_only_the_selected_user_is_changed(): void
    {
        // Dulu id_user berisi user TERAKHIR di tabel (variabel foreach bocor), bukan baris yang diklik
        $first = User::factory()->create(['role' => 'customer']);
        $selected = User::factory()->create(['role' => 'customer']);
        $last = User::factory()->create(['role' => 'customer']);

        $this->actingAs($this->admin())->post('/admin/updateUser', [
            'id_user' => $selected->id,
            'role' => 'courier',
            'restriction' => 1,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertSame('courier', $selected->refresh()->role);
        $this->assertTrue((bool) $selected->restriction);
        $this->assertSame('customer', $first->refresh()->role);
        $this->assertSame('customer', $last->refresh()->role);
        $this->assertFalse((bool) $last->restriction);
    }

    public function test_account_modal_does_not_preload_a_user_id(): void
    {
        User::factory()->count(3)->create();

        $html = $this->actingAs($this->admin())->get('/admin/account')->assertOk()->getContent();

        // id_user diisi JS sesuai baris yang diklik; tidak boleh sudah berisi id user terakhir
        $this->assertMatchesRegularExpression('/name="id_user" id="id_user" value=""/', $html);
    }

    public function test_invalid_role_is_rejected(): void
    {
        $target = User::factory()->create(['role' => 'customer']);

        $this->actingAs($this->admin())->post('/admin/updateUser', [
            'id_user' => $target->id,
            'role' => 'superuser',
            'restriction' => 0,
        ])->assertSessionHasErrors('role');

        $this->assertSame('customer', $target->refresh()->role);
    }

    public function test_non_admins_cannot_change_accounts(): void
    {
        $target = User::factory()->create(['role' => 'customer']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/admin/updateUser', [
            'id_user' => $target->id,
            'role' => 'admin',
            'restriction' => 0,
        ])->assertRedirect();

        $this->assertSame('customer', $target->refresh()->role);
    }
}
