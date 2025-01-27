<?php
// src/Service/TaskService.php
namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

class TaskService
{
    private $entityManager;
    private $validator;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    public function createTask(array $data): Task
    {
        $this->logger->info('Creating task with data', $data);

        // Создание задачи
        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);

        // Валидация
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            throw new \Exception('Validation failed', 400); // или возвращаем ошибку
        }

        // Сохранение задачи в базе данных
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->logger->info('Task created successfully', ['task_id' => $task->getId()]);

        return $task;
    }

    public function updateTask(int $id, array $data): Task
    {
        $this->logger->info('Updating task with ID: ' . $id);

        // Получение задачи по ID
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);

        // Валидация
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            throw new \Exception('Validation failed', 400);
        }

        // Обновление данных задачи
        $this->entityManager->flush();

        $this->logger->info('Task updated successfully', ['task_id' => $task->getId()]);

        return $task;
    }

    public function getTaskById(int $id): array
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (!$task) {
            $this->logger->warning('Task not found', ['task_id' => $id]);
        }

        $taskData = [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
        ];

        return $taskData;
    }

    public function getAllTasks(?string $status, int $page, int $limit): array
    {
        $this->logger->info('Incoming request to view all tasks');

        $repository = $this->entityManager->getRepository(Task::class);
        $queryBuilder = $repository->createQueryBuilder('t');

        if ($status) {
            $queryBuilder->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }

        $offset = ($page - 1) * $limit;
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit);

        $tasks = $queryBuilder->getQuery()->getResult();

        $totalTasks = $queryBuilder->select('COUNT(t.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $tasksArray = [];
        foreach ($tasks as $task) {
            $tasksArray[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ];
        }

        return [
            'data' => $tasksArray, // Список задач
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total_items' => $totalTasks,
                'total_pages' => ceil($totalTasks / $limit),
            ],
        ];


//
//        return ($tasksArray);
    }

    public function deleteTask(int $id): void
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->logger->info('Task deleted successfully', ['task_id' => $id]);
    }
}
