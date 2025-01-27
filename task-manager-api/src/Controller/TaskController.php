<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskService;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints as Assert;


class TaskController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/api/task', name: 'task_list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        // Получаем параметры фильтрации и пагинации из запроса
        $status = $request->query->get('status'); // Фильтр по статусу
        $page = max(1, (int) $request->query->get('page', 1)); // Номер страницы
        $limit = max(1, (int) $request->query->get('limit', 10)); // Лимит задач на странице

        $result = $this->taskService->getAllTasks($status, $page, $limit);
        if (count($result) == 0) {
            return $this->json(['message' => 'No tasks found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($result);
    }

    #[Route('/api/task', name: 'task_create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->taskService->createTask($data);
            return $this->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    #[Route('/api/task/{id}', name: 'task_update', methods: ['PUT'])]
    public function update(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->taskService->updateTask($id, $data);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    #[Route('/api/task/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->taskService->deleteTask($id);
            return $this->json(['message' => 'Task deleted']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    #[Route('/api/task/{id}', name: 'task_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $task = $this->taskService->getTaskById($id);
            if (!$task) {
                return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
            }

            return $this->json($task);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }

    }
}