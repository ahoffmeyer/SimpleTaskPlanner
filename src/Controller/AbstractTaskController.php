<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Controller;

use AndreasHoffmeyer\SimpleTaskPlanner\Model\TaskCollection;

abstract class AbstractTaskController
{

    abstract public function get(): TaskCollection;
    abstract public function post(array $request): TaskCollection;
    abstract public function patch(array $request): TaskCollection;
    abstract public function delete(string $id): TaskCollection;

}