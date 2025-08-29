<?php
declare(strict_types=1);

namespace App\Service;

final class InMemoryMessageStorage implements MessageStorageInterface
{
    /** @var array<int, array{id:int,text:string}> */
    private array $messages = [];
    private int $autoIncrement = 1;

    public function add(string $text): array
    {
        $row = ['id' => $this->autoIncrement++, 'text' => $text];
        $this->messages[] = $row;
        return $row;
    }

    public function all(): array
    {
        // нові зверху
        return array_values(array_reverse($this->messages));
    }
}
