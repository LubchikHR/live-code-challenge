<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $text;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?int { return $this->id !== null ? (int)$this->id : null; }
    public function getText(): string { return $this->text; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'text' => $this->text,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
