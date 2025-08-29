<?php

declare(strict_types=1);

namespace App\DTO;

class ResponseDtoDiscountItem extends RequestDtoDiscountItem
{
    public function __construct(int $id, int $price, protected int $priceWithDiscount)
    {
        parent::__construct($id, $price);
    }

    public function getPriceWithDiscount(): int
    {
        return $this->priceWithDiscount;
    }
}
