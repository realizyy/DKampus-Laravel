<?php

namespace Tests\Feature;

use App\Models\Addresse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private function address(User $user, string $name, bool $main = false): Addresse
    {
        return Addresse::create([
            'user_id' => $user->id,
            'address' => 'Jl. ' . $name,
            'link' => 'https://maps.example/' . $name,
            'nama_alamat' => $name,
            'notes' => null,
            'geo' => '-6.9,107.7',
            'utama' => $main ? 1 : 0,
        ]);
    }

    public function test_user_can_add_an_address(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/daftar_alamat', [
            'address' => 'Jl. Baru',
            'link' => 'https://maps.example/baru',
            'namaAlamat' => 'Rumah',
            'geo' => '-6.9,107.7',
        ])->assertRedirect();

        $this->assertDatabaseHas('addresses', ['user_id' => $user->id, 'nama_alamat' => 'Rumah', 'utama' => 0]);
    }

    public function test_setting_the_main_address_clears_the_previous_one(): void
    {
        $user = User::factory()->create();
        $a = $this->address($user, 'A', true);
        $b = $this->address($user, 'B');

        $this->actingAs($user)->post('/defaul-address', ['id' => $b->id, 'custId' => $user->id])->assertRedirect();

        $this->assertFalse((bool) $a->refresh()->utama);
        $this->assertTrue((bool) $b->refresh()->utama);
    }

    public function test_user_cannot_make_someone_elses_address_main(): void
    {
        $user = User::factory()->create();
        $victim = User::factory()->create();
        $mine = $this->address($user, 'Mine', true);
        $theirs = $this->address($victim, 'Theirs', true);
        $theirsOther = $this->address($victim, 'TheirsOther');

        // Memakai custId korban dan id alamat korban (celah lama: id/custId dipercaya dari request)
        $this->actingAs($user)->post('/defaul-address', ['id' => $theirsOther->id, 'custId' => $victim->id]);

        $this->assertTrue((bool) $theirs->refresh()->utama);
        $this->assertFalse((bool) $theirsOther->refresh()->utama);
        $this->assertTrue((bool) $mine->refresh()->utama);
    }

    public function test_user_cannot_delete_someone_elses_address(): void
    {
        $user = User::factory()->create();
        $victim = User::factory()->create();
        $theirs = $this->address($victim, 'Theirs');
        $mine = $this->address($user, 'Mine');

        $this->actingAs($user)->post('/delete-address', ['id' => $theirs->id])->assertRedirect();
        $this->assertNotNull($theirs->fresh());

        $this->actingAs($user)->post('/delete-address', ['id' => $mine->id])->assertRedirect();
        $this->assertNull($mine->fresh());
    }
}
