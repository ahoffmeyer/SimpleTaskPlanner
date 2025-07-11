<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Factory;

use AndreasHoffmeyer\SimpleTaskPlanner\Model\Task;

class TaskFactory
{

    public static function create(array $data, ?string $id = null): Task
    {
        $task = new Task();
        $task->setId(uniqid());
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setCompleted(false);
        $task->setCreatedAt(new \DateTimeImmutable());

        return $task;
    }

}