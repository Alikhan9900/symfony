<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private array $items = [
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
    ];

    // CREATE
    #[Route('/api/items', name: 'create_item', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $data['id'] = count($this->items) + 1;
        $this->items[] = $data;

        return new JsonResponse(['message' => 'Item created', 'item' => $data]);
    }

    // READ (усі)
    #[Route('/api/items', name: 'get_items', methods: ['GET'])]
    public function readAll(): JsonResponse
    {
        return new JsonResponse($this->items);
    }

    // UPDATE
    #[Route('/api/items/{id}', name: 'update_item', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($this->items as &$item) {
            if ($item['id'] === $id) {
                $item['name'] = $data['name'] ?? $item['name'];
                return new JsonResponse(['message' => 'Item updated', 'item' => $item]);
            }
        }

        return new JsonResponse(['error' => 'Item not found'], 404);
    }

    // DELETE
    #[Route('/api/items/{id}', name: 'delete_item', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        foreach ($this->items as $key => $item) {
            if ($item['id'] === $id) {
                unset($this->items[$key]);
                return new JsonResponse(['message' => 'Item deleted']);
            }
        }

        return new JsonResponse(['error' => 'Item not found'], 404);
    }
}
