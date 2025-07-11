<?php declare(strict_types=1);

require '../../vendor/autoload.php';

use AndreasHoffmeyer\SimpleTaskPlanner\Controller\TaskController;
use AndreasHoffmeyer\SimpleTaskPlanner\Model\TaskCollection;
use AndreasHoffmeyer\SimpleTaskPlanner\Repository\TaskRepository;
use AndreasHoffmeyer\SimpleTaskPlanner\Validator\TaskValidator;

// Setzt den Content-Type Header, damit der Browser weiß, dass er JSON erwartet.
header('Content-Type: application/json');

// Holt die vom Client gesendete Session-ID aus dem Header.
$clientSessionId = null;
if (isset($_SERVER['HTTP_X_SESSION_ID'])) {
    $clientSessionId = $_SERVER['HTTP_X_SESSION_ID'];
}

// Wenn keine ID gesendet wurde, brich mit einem Fehler ab.
if (!$clientSessionId) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'X-Session-ID header is missing.']);
    exit;
}

session_id($clientSessionId);

// 4. Starte die Session. PHP sucht jetzt nach einer Session-Datei mit genau dieser ID
//    oder erstellt eine neue, falls sie nicht existiert.
session_start();

if (!$_SESSION['tasks']) {
    $_SESSION['tasks'] = new TaskCollection();
}

$taskController = new TaskController(
    new TaskRepository(),
    new TaskValidator()
);

$request = json_decode(file_get_contents('php://input'), true);

$response = new TaskCollection();

$response = match($_SERVER['REQUEST_METHOD']) {
    'GET' => $taskController->get(),
    'POST' => $taskController->post($request),
    'PATCH' => $taskController->patch($request),
    'DELETE' => $taskController->delete($_GET['id']),
    default => throw new \Exception('Unsupported HTTP method.'),
};

echo json_encode($response->toArray());
