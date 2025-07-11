<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Model;

class TaskCollection
{

    private array $tasks = [];

    public function add(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function toArray(): array
    {
        $tasksArray = [];

        foreach ($this->tasks as $taskEntity) {
            $tasksArray[] = [
                'id' => $taskEntity->getId(),
                'title' => $taskEntity->getTitle(),
                'description' => $taskEntity->getDescription(),
                'completed' => $taskEntity->isCompleted()
            ];
        }

        return $tasksArray;
    }

    public function remove(string $id): void
    {
        foreach ($this->tasks as $key => $task) {
            if ($task->getId() === $id) {
                unset($this->tasks[$key]);
            }
        }
    }
    
}