<?php

namespace Tests\Feature;

use App\Models\history;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Rating dan ulasan pembeli untuk pesanan selesai (Figma: Rating Pelayanan/Makanan, aspek, ulasan). */
class RatingTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $customer, string $status = 'completed'): history
    {
        $courier = User::factory()->create(['role' => 'courier']);

        return history::create([
            'user_id' => $customer->id, 'cour_id' => $courier->id, 'umkm_id' => 0, 'item' => '1 Mie Ayam (-)',
            'harga' => 10000, 'ongkir' => 3000, 'jarak' => 1.5, 'status' => $status, 'bukti' => null, 'bukti_akhir' => 'b.png',
            'alasan' => null, 'total_driver' => 13000, 'kategori' => 'jastip', 'order_id' => 'tr'.uniqid(), 'notes' => '-',
        ]);
    }

    private function payload(history $order, array $overrides = []): array
    {
        return array_merge([
            'history_id' => $order->id, 'rating_pelayanan' => 5, 'rating_makanan' => 4,
            'aspects' => ['Rasa', 'Harga'], 'review' => 'Enak dan cepat',
        ], $overrides);
    }

    public function test_guest_cannot_rate(): void
    {
        $this->post('/rating/kirim', [])->assertRedirect();
        $this->assertSame(0, Rating::count());
    }

    public function test_customer_can_rate_a_completed_order_once(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->order($customer);

        $this->actingAs($customer)->post('/rating/kirim', $this->payload($order))
            ->assertRedirect('/pesanan/status')
            ->assertSessionHas('success');

        $rating = Rating::first();
        $this->assertSame(5, $rating->rating_pelayanan);
        $this->assertSame(['Rasa', 'Harga'], $rating->aspects);

        // kirim ulang memperbarui, bukan menggandakan
        $this->actingAs($customer)->post('/rating/kirim', $this->payload($order, ['rating_pelayanan' => 3]));
        $this->assertSame(1, Rating::count());
        $this->assertSame(3, Rating::first()->rating_pelayanan);
    }

    public function test_stars_are_required_and_limited_to_one_through_five(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->order($customer);

        $this->actingAs($customer)->post('/rating/kirim', $this->payload($order, ['rating_pelayanan' => 0]))->assertSessionHasErrors('rating_pelayanan');
        $this->actingAs($customer)->post('/rating/kirim', $this->payload($order, ['rating_makanan' => 6]))->assertSessionHasErrors('rating_makanan');
        $this->actingAs($customer)->post('/rating/kirim', $this->payload($order, ['aspects' => ['Bebas']]))->assertSessionHasErrors('aspects.0');
        $this->assertSame(0, Rating::count());
    }

    public function test_cannot_rate_someone_elses_order(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $order = $this->order($owner);

        $this->actingAs($other)->post('/rating/kirim', $this->payload($order))->assertNotFound();
        $this->assertSame(0, Rating::count());
    }

    public function test_cannot_rate_an_order_that_is_not_completed(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->order($customer, 'canceled');

        $this->actingAs($customer)->post('/rating/kirim', $this->payload($order))->assertForbidden();
        $this->assertSame(0, Rating::count());
    }
}
