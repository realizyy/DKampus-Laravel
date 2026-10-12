<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** Password bawaan UserFactory */
    private const PASSWORD = '12345';

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_customer_is_redirected_to_homepage_after_login(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => self::PASSWORD]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('homepage'));
    }

    public function test_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => self::PASSWORD]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_courier_is_redirected_to_courier_dashboard_after_login(): void
    {
        $user = User::factory()->create(['role' => 'courier']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => self::PASSWORD]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboardCourier'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_is_redirected_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect('/');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
