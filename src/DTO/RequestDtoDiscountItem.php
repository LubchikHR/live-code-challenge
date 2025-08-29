<?php

declare(strict_types=1);

namespace App\DTO;

class RequestDtoDiscountItem
{
    public function __construct(
        protected int $id,
        protected int $price,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
