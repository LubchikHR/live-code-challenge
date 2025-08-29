<?php
declare(strict_types=1);

namespace App\Service;

/**
 * Проксі над обраною реалізацією MessageStorageInterface.
 */
final class MessageStorageSelector implements MessageStorageInterface
{
    /** @param iterable<string, MessageStorageInterface> $storages indexed by alias */
    public function __construct(
        private iterable $storages,
        private string $selectedAlias  // 'memory' | 'doctrine'
    ) {}

    private function inner(): MessageStorageInterface
    {
        // iterable індексований по alias завдяки index_by у services.yaml
        foreach ($this->storages as $alias => $service) {
            if ($alias === $this->selectedAlias) {
                return $service;
            }
        }
        // fallback: перший доступний
        foreach ($this->storages as $service) {
            return $service;
        }
        throw new \RuntimeException('No message storage implementations registered.');
    }

    public function add(string $text): array   { return $this->inner()->add($text); }
    public function all(): array               { return $this->inner()->all(); }
}
