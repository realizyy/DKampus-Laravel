<?php

namespace Tests\Support;

use LogicException;

/**
 * Pengganti app('firebase.database') untuk test. Hanya mendukung operasi BACA dari array
 * di memori; semua operasi tulis melempar LogicException, jadi test tidak mungkin menulis
 * ke Firebase (atau memanggil jaringan) walaupun kode yang diuji berubah.
 *
 * Pemakaian:
 *   $this->app->instance('firebase.database', new FakeFirebaseDatabase(['onProgress' => [...]]));
 */
class FakeFirebaseDatabase
{
    public function __construct(private array $tree = [])
    {
    }

    public function getReference(string $path = ''): FakeFirebaseReference
    {
        return new FakeFirebaseReference($this->tree, trim($path, '/'));
    }
}

class FakeFirebaseReference
{
    public function __construct(private array $tree, private string $path)
    {
    }

    public function getValue(): mixed
    {
        $node = $this->tree;
        foreach (array_filter(explode('/', $this->path), 'strlen') as $segment) {
            if (! is_array($node) || ! array_key_exists($segment, $node)) {
                return null;
            }
            $node = $node[$segment];
        }

        return $node;
    }

    /** @return array<int, string> */
    public function getChildKeys(): array
    {
        $value = $this->getValue();

        return is_array($value) ? array_map('strval', array_keys($value)) : [];
    }

    public function getSnapshot(): FakeFirebaseSnapshot
    {
        return new FakeFirebaseSnapshot($this->getValue());
    }

    public function set(mixed $value = null): never
    {
        $this->forbidden('set');
    }

    public function push(mixed $value = null): never
    {
        $this->forbidden('push');
    }

    public function update(array $values = []): never
    {
        $this->forbidden('update');
    }

    public function remove(): never
    {
        $this->forbidden('remove');
    }

    private function forbidden(string $operation): never
    {
        throw new LogicException("Penulisan ke Firebase dilarang saat test (dipanggil: {$operation} pada [{$this->path}]).");
    }
}

class FakeFirebaseSnapshot
{
    public function __construct(private mixed $value)
    {
    }

    public function exists(): bool
    {
        return $this->value !== null;
    }

    public function hasChildren(): bool
    {
        return is_array($this->value) && count($this->value) > 0;
    }

    public function numChildren(): int
    {
        return is_array($this->value) ? count($this->value) : 0;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
