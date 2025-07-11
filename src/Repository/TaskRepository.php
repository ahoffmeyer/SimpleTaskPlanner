<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Repository;

use AndreasHoffmeyer\SimpleTaskPlanner\Model\Task;
use AndreasHoffmeyer\SimpleTaskPlanner\Model\TaskCollection;
use DateTimeImmutable;

class TaskRepository
{
    private TaskCollection $tasks;

    public function getAll(): TaskCollection
    {
        return $_SESSION['tasks'] ?? new TaskCollection();
    }

    public function getById(string $id): ?Task
    {
        foreach ($this->tasks->getTasks() as $task) {
            if ($task->getId() === $id) {
                return $task;
            }
        }

        return null;
    }

    public function addTaskInSession(Task $task): void
    {
        $tasks = $_SESSION['tasks'] ?? new TaskCollection();

        $tasks->add($task);

        $this->storeInSession($tasks);
    }

    public function updateTaskInSession(TaskCollection $taskCollection, string $id, array $data): void
    {
        $newTaskCollection = new TaskCollection();

        /** @var Task $task */
        foreach ($taskCollection->getTasks() as $task) {
            if ($task->getId() === $id) {
                $task->setTitle($data['title'] ?? $task->getTitle());
                $task->setDescription($data['description'] ?? $task->getDescription());
                $task->setCompleted($data['completed'] ?? $task->isCompleted());
                $task->setUpdatedAt(new \DateTimeImmutable());
            }

            $newTaskCollection->add($task);
        }

        $this->storeInSession($newTaskCollection);
    }

    public function removeTaskFromSession(string $id): void
    {
        $tasks = $_SESSION['tasks'] ?? new TaskCollection();

        $tasks->remove($id);

        $this->storeInSession($tasks);
    }

    public function storeInSession(TaskCollection $taskCollection): void
    {
        $_SESSION['tasks'] = $taskCollection;

        $this->tasks = $taskCollection;
    }

}