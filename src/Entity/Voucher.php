<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VoucherRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoucherRepository::class)]
#[ORM\Table(name: 'voucher')]
#[ORM\UniqueConstraint('code', ['code'])]
class Voucher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private string $id;

    #[ORM\Column(type: 'integer')]
    private int $discount;

    #[ORM\Column(type: 'string', length: 255)]
    private string $code;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(int $discount, string $code)
    {
        $this->discount = $discount;
        $this->code = $code;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public static function createVoucher(int $discount, string $code): static
    {
        return new self($discount, $code);
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
