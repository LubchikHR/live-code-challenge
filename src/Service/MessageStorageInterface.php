<?php
declare(strict_types=1);

namespace App\Service;

interface MessageStorageInterface
{
    /** @return array{id:int|null,text:string,createdAt?:string} */
    public function add(string $text): array;

    /** @return array<int, array{id:int,text:string,createdAt?:string}> */
    public function all(): array;
}
