<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Model;

class TaskCollection
{

    private array $tasks = [];

    public function add(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function getTaskById(string $id): ?Task
    {
        foreach ($this->tasks as $task) {
            if ($task->getId() === $id) {
                return $task;
            }
        }
        return null;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function toArray(): array
    {
        $tasksArray = [];

// 2. Durchlaufe deine Collection.
        foreach ($this->tasks as $taskEntity) {
            // 3. Wandle jede einzelne Entity in ein einfaches assoziatives Array um.
            //    Füge dieses neue Array dem Haupt-Array hinzu.
            $tasksArray[] = [
                'id' => $taskEntity->getId(), // Annahme: es gibt eine Methode getId()
                'title' => $taskEntity->getTitle(),
                'description' => $taskEntity->getDescription(),
                'completed' => $taskEntity->isCompleted()
            ];
        }

        return $tasksArray;
    }

    public function json(): string
    {
        return json_encode($this->tasks);
    }

    public function remove(string $id): void
    {
        foreach ($this->tasks as $key => $task) {
            if ($task->getId() === $id) {
                unset($this->tasks[$key]);
            }
        }
    }

    public function count(): int
    {
        return count($this->tasks);
    }
    
}