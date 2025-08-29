<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RequestDtoDiscount;
use App\DTO\RequestDtoDiscountItem;
use App\DTO\ResponseDtoDiscount;
use App\DTO\ResponseDtoDiscountItem;
use App\Entity\Voucher;
use App\Repository\VoucherRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class DiscountApplyService
{
    public function __construct(private VoucherRepository $voucherRepository)
    {
    }

    public function apply(RequestDtoDiscount $dto): ResponseDtoDiscount
    {
        $voucher = $this->getVoucher($dto->getCode());

        return new ResponseDtoDiscount($this->applyProportionalDiscount($dto, $voucher->getDiscount()), $dto->getCode());
    }

    function applyProportionalDiscount(RequestDtoDiscount $dto, int $discount): array
    {
        $hasDiscount = true;
        $totalPriceItems = 0;
        $responseDtoDiscountItems = [];
        $items = $dto->getRequestDtoDiscountItem();

        /** @var RequestDtoDiscountItem $item */
        array_walk($items, function ($item) use (&$totalPriceItems) {
            $totalPriceItems += $item->getPrice();
        });

        if ($discount > $totalPriceItems) {
            $hasDiscount = false;
        }

        if ($totalPriceItems === 0 || $discount <= 0) {
            return $dto->getRequestDtoDiscountItem();
        }

        foreach ($dto->getRequestDtoDiscountItem() as $item) {
            $share = ($item->getPrice() / $totalPriceItems) * $discount;
            $deduction = (int) round($share, mode: PHP_ROUND_HALF_ODD);

            $responseDtoDiscountItems[] = new ResponseDtoDiscountItem(
                $item->getId(),
                $item->getPrice(),
                $hasDiscount ? $deduction : 0,
            );
        }

        return $responseDtoDiscountItems;
    }

    private function getVoucher(string $code): Voucher
    {
        $voucher = $this->voucherRepository->findOneBy(['code' => $code]);
        if (!$voucher) {
            throw new BadRequestHttpException('Voucher not found');
        }

        return $voucher;
    }
}
