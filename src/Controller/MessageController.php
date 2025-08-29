<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\MessageStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/messages')]
final class MessageController extends AbstractController
{
    public function __construct(private MessageStorageInterface $storage) {}

    #[Route('', name: 'api_messages_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['text']) || trim((string)$data['text']) === '') {
            return $this->json(['error' => 'Field "text" is required'], 400);
        }

        return $this->json($this->storage->add((string)$data['text']), 201);
    }

    #[Route('', name: 'api_messages_get', methods: ['GET'])]
    public function get(): JsonResponse
    {
        return $this->json($this->storage->all(), 200);
    }
}
