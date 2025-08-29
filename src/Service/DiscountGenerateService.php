<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Voucher;
use App\Repository\VoucherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DiscountGenerateService
{
    private const MAX_ATTEMPT = 3;
    private static int $countAttempt = 0;

    /** @var VoucherRepository $voucherRepository */
    private EntityRepository $voucherRepository;

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $this->voucherRepository = $this->entityManager->getRepository(Voucher::class);
    }

    public function generate(int $discount): Voucher
    {
        $voucher = Voucher::createVoucher($discount, $this->getCode());

        $this->entityManager->persist($voucher);
        $this->entityManager->flush();

        return $voucher;
    }

    private function getCode(): string
    {
        $code = $this->generateUniqueCode();

        if ($this->voucherRepository->findOneBy(['code' => $code])) {
            if (self::MAX_ATTEMPT < self::$countAttempt) {
                throw new \HttpException('Maximum number of attempts reached');
            }

            ++self::$countAttempt;

            return $this->getCode();
        }

        return $code;
    }

    protected function generateUniqueCode(int $length = 7): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
