<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Factory;

use AndreasHoffmeyer\SimpleTaskPlanner\Model\Task;

class TaskFactory
{

    public static function create(array $post, bool $update = false): Task
    {
        $id = $_GET['id'] ?? uniqid();

        $task = new Task();
        $task->setId($id);
        $task->setTitle($post['title']);
        $task->setDescription($post['description']);
        $task->setCompleted(false);
        $task->setCreatedAt(new \DateTimeImmutable());

        return $task;
    }

}