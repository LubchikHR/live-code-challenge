<?php

declare(strict_types=1);

namespace App\DTO;

class RequestDtoDiscount
{
    public function __construct(
        private array $requestDtoDiscountItem,
        private string $code,
    ) {
    }

    /**
     * @return RequestDtoDiscountItem[] $requestDtoDiscountItem
     */
    public function getRequestDtoDiscountItem(): array
    {
        return $this->requestDtoDiscountItem;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
