<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->guardAgainstNonTestingDatabase($app);

        return $app;
    }

    /**
     * Test memakai RefreshDatabase (migrate:fresh). Tolak berjalan kalau koneksi default
     * bukan database khusus test, supaya data development tidak pernah ikut terhapus.
     */
    private function guardAgainstNonTestingDatabase(Application $app): void
    {
        $config = $app['config'];
        $connection = $config->get('database.default');
        $database = (string) $config->get("database.connections.{$connection}.database");

        if ($database !== ':memory:' && ! str_ends_with($database, '_testing')) {
            throw new RuntimeException(
                "Test ditolak: database [{$database}] bukan database test. ".
                'Atur DB_DATABASE di phpunit.xml ke database yang namanya berakhiran "_testing".'
            );
        }
    }
}
