<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Factory;

use AndreasHoffmeyer\SimpleTaskPlanner\Model\Task;

class TaskFactory
{

    public static function create(array $post, ?string $id = null): Task
    {
        $task = new Task();
        $task->setId($id ?? uniqid());
        $task->setTitle($post['title']);
        $task->setDescription($post['description']);
        $task->setCompleted(false);
        $task->setCreatedAt(new \DateTimeImmutable());

        return $task;
    }

}