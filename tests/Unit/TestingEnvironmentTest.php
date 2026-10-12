<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Pengaman: test memakai RefreshDatabase, jadi lingkungan test tidak boleh menunjuk ke
 * database development maupun ke layanan eksternal (Firebase, Telegram, Google Maps).
 */
class TestingEnvironmentTest extends TestCase
{
    public function test_database_is_a_dedicated_testing_database(): void
    {
        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        $this->assertTrue(
            $database === ':memory:' || str_ends_with($database, '_testing'),
            "Database test seharusnya berakhiran _testing, ditemukan [{$database}]"
        );
        $this->assertNotSame('dkampus', $database);
    }

    public function test_external_integrations_are_disabled(): void
    {
        $this->assertSame('', (string) env('TELEGRAM_BOT_TOKEN'));
        $this->assertSame('', (string) env('TELEGRAM_CHAT_ID'));
        $this->assertSame('', (string) env('GOOGLE_MAPS_API_KEY'));
        $this->assertStringNotContainsString('firebaseio.com', (string) env('FIREBASE_DATABASE_URL'));
        $this->assertFileDoesNotExist(base_path((string) env('FIREBASE_CREDENTIALS')));
    }

    public function test_firebase_cannot_be_resolved_during_tests(): void
    {
        $this->expectException(\Throwable::class);

        app('firebase.database');
    }
}
