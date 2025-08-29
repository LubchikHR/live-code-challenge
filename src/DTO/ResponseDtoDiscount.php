<?php

declare(strict_types=1);

namespace App\DTO;

class ResponseDtoDiscount
{
    public function __construct(
        private array $responseDtoDiscountItems,
        private string $code,
    ) {
    }

    /**
     * @return ResponseDtoDiscountItem[] $responseDtoDiscountItems
     */
    public function getResponseDtoDiscountItem(): array
    {
        return $this->responseDtoDiscountItems;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
