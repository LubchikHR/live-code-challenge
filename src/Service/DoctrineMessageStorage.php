<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineMessageStorage implements MessageStorageInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageRepository $repo
    ) {}

    public function add(string $text): array
    {
        $m = new Message($text);
        $this->em->persist($m);
        $this->em->flush();

        return $m->toArray();
    }

    public function all(): array
    {
        return array_map(
            static fn(Message $m) => $m->toArray(),
            $this->repo->findBy([], ['id' => 'DESC'])
        );
    }
}
