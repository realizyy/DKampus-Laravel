<?php

namespace Tests\Feature;

use App\Models\Data_umkm;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_route_action_points_to_an_existing_controller_method(): void
    {
        $broken = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction('uses');
            if (! is_string($action)) {
                continue; // closure
            }
            [$class, $method] = array_pad(explode('@', $action), 2, '__invoke');

            if (! class_exists($class) || ! method_exists($class, $method)) {
                $broken[] = $route->methods()[0] . ' ' . $route->uri() . ' -> ' . $action;
            }
        }

        $this->assertSame([], $broken, "Route menunjuk method yang tidak ada:\n" . implode("\n", $broken));
    }

    public function test_every_route_middleware_alias_is_registered(): void
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $known = array_merge($kernel->getMiddlewareGroups(), $kernel->getRouteMiddleware());
        $unknown = [];

        foreach (Route::getRoutes() as $route) {
            foreach ($route->gatherMiddleware() as $middleware) {
                if (! is_string($middleware)) {
                    continue;
                }
                $name = explode(':', $middleware)[0];
                if (! class_exists($name) && ! isset($known[$name])) {
                    $unknown[] = $route->uri() . ' -> ' . $name;
                }
            }
        }

        $this->assertSame([], $unknown);
    }

    public function test_orphan_routes_were_removed(): void
    {
        $names = collect(Route::getRoutes()->getRoutesByName())->keys();

        $this->assertFalse($names->contains('payment'));
        $this->assertFalse($names->contains('account.update'));
        $this->assertFalse($names->contains('orderdetail'));
    }

    public function test_public_get_pages_never_return_a_server_error(): void
    {
        $umkm = Data_umkm::factory()->create();
        Menu::factory()->create(['data_umkm_id' => $umkm->id]);

        $skip = ['_ignition', 'sanctum', 'up', 'logout'];
        $failures = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || str_contains($uri, '{') || str_starts_with($uri, 'api/')) {
                continue;
            }
            foreach ($skip as $prefix) {
                if (str_starts_with($uri, $prefix)) {
                    continue 2;
                }
            }

            $status = $this->get('/' . ltrim($uri, '/'))->getStatusCode();
            if ($status >= 500) {
                $failures[] = "{$status} /{$uri}";
            }
        }

        $this->assertSame([], $failures, "Halaman publik mengembalikan 5xx:\n" . implode("\n", $failures));
    }

    public function test_detail_pages_render_for_existing_records(): void
    {
        $umkm = Data_umkm::factory()->create();
        $menu = Menu::factory()->create(['data_umkm_id' => $umkm->id]);

        $this->get('/detail-warung/' . rawurlencode($umkm->nama_umkm))->assertOk();
        $this->get('/detail-makanan/' . rawurlencode($menu->nama_makanan))->assertOk();
    }
}
