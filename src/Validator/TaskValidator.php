<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Validator;

use AndreasHoffmeyer\SimpleTaskPlanner\Exception\ControllerException;

class TaskValidator
{

    public function validate(array $post): void
    {

        if (empty($post)) {
            throw new ControllerException('Task id cannot be empty');
        }

        if (isset($post['title']) && !is_string($post['title'])) {
            throw new ControllerException('Task title must be a string');
        }

        if (isset($post['description']) && !is_string($post['description'])) {
            throw new ControllerException('Task description must be a string');
        }

        if (isset($post['completed']) && !is_bool($post['completed'])) {
            throw new ControllerException('Task completed must be a bool');
        }

    }

}