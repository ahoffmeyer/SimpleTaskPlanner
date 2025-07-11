<?php declare(strict_types=1);

namespace AndreasHoffmeyer\SimpleTaskPlanner\Controller;

use AndreasHoffmeyer\SimpleTaskPlanner\Exception\ControllerException;
use AndreasHoffmeyer\SimpleTaskPlanner\Factory\TaskFactory;
use AndreasHoffmeyer\SimpleTaskPlanner\Model\TaskCollection;
use AndreasHoffmeyer\SimpleTaskPlanner\Repository\TaskRepository;
use AndreasHoffmeyer\SimpleTaskPlanner\Validator\TaskValidator;

class TaskController extends AbstractTaskController
{
    private TaskCollection $tasks;
    public function __construct(
        readonly private TaskRepository $taskRepository,
        readonly private TaskValidator $validator
    ) {
        $this->tasks = $_SESSION['tasks'];
    }

    public function get(): TaskCollection
    {
        return $this->tasks;
    }

    public function post(array $request): TaskCollection
    {
        try {
            $this->validator->validate($request);

            $task = TaskFactory::create($request);

            $this->taskRepository->addTaskInSession($task);

            return $this->get();
        } catch (ControllerException $exception) {
            return new TaskCollection();
        }
    }

    public function patch(array $request): TaskCollection
    {
        try {
            $this->validator->validate($request);

            $this->taskRepository->updateTaskInSession($this->tasks, $_GET['id'], $request);

            return $this->get();
        } catch (ControllerException $exception) {
            return new TaskCollection();
        }
    }

    public function delete(string $id): TaskCollection
    {
        $this->taskRepository->removeTaskFromSession($id);

        return $this->get();
    }
}