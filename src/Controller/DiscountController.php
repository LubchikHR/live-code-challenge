<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\RequestDtoDiscount;
use App\DTO\RequestDtoDiscountItem;
use App\DTO\ResponseDtoDiscount;
use App\DTO\ResponseDtoDiscountItem;
use App\Service\DiscountApplyService;
use App\Service\DiscountGenerateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class DiscountController extends AbstractController
{
    public function __construct(
        private DiscountGenerateService $discountGenerateService,
        private DiscountApplyService $discountApplyService,
    ) {
    }

    #[Route('generate', name: 'api_discount_generate', methods: ['POST'])]
    public function generate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['discount'])) {
            throw new BadRequestHttpException();
        }

        $voucher = $this->discountGenerateService->generate($data['discount']);

        return $this->json(['code' => $voucher->getCode()], 201);
    }

    #[Route('apply', name: 'api_discount_apply', methods: ['POST'])]
    public function apply(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['code']) || !isset($data['items'])) {
            throw new BadRequestHttpException();
        }

        $dtoItems = [];
        foreach ($data['items'] as $item) {
            $dtoItems[] = new RequestDtoDiscountItem($item['id'], $item['price']);
        }

        $dto = new RequestDtoDiscount($dtoItems, $data['code']);
        $responseDto = $this->discountApplyService->apply($dto);

        return $this->json($this->applyResponse($responseDto), 201);
    }

    // TODO move to data mapper
    private function applyResponse(ResponseDtoDiscount $dto): array
    {
        $response = [];

        foreach ($dto->getResponseDtoDiscountItem() as $item) {
            $response['items'][] = ['id' => $item->getId(), 'price' => $item->getPrice(), 'price_with_discount' => $item->getPriceWithDiscount()];
        }

        $response['code'] = $dto->getCode();
        return $response;
    }
}
