<?php

namespace Genesis\Api\Mocker\Contract;

/**
 * StorageHandler class.
 */
interface StorageHandler
{
    public function save(string $endpoint, $data): bool;

    public function get(string $endpoint): array;

    public function purge(): ?array;

    public function getIndex(): array;
}
