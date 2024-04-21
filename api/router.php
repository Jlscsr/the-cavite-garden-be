<?php

use Helpers\HeaderHelper;
use Helpers\ResponseHelper;

require_once dirname(__DIR__) . '/config/db_connect.php';
require_once dirname(__DIR__) . '/api/routes.php';


$pdo = db_connect();
$route = new Route();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    HeaderHelper::setHeaders();
    http_response_code(200);
    exit();
}

HeaderHelper::setHeaders();

$url = $_GET['url'] ?? '';

$request_method = strtoupper(trim($_SERVER['REQUEST_METHOD']));

$handler = $route->get_route($url);

$middleware_required = isset($handler['middleware']) && is_array($handler['middleware']) && $handler['middleware']['required'];

if ($middleware_required) {
    $middleware = $handler['middleware']['handler'];

    try {
        require_once dirname(__DIR__) . '/middleware' . '/' . $middleware . '.php';

        $is_valid = new $middleware();

        if (!$is_valid) {
            ResponseHelper::sendErrorResponse(["error" => "unauthorized", "message" => "User is not authenticated"], 401);
            return;
        }
    } catch (Exception $e) {
        ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        return;
    }
}

list($controller, $method) = explode('@', is_array($handler['handler']) ? $handler['handler']['handler'] : $handler['handler']);

switch ($request_method) {
    case 'GET':
        require_once dirname(__DIR__) . '/controller' . '/' . $controller . '.php';

        $controller = new $controller($pdo);

        if (isset($handler['params'])) {
            $controller->$method($handler['params']);
        } else {
            $controller->$method();
        }
        break;
    case 'POST':
        $payload = json_decode(file_get_contents('php://input'), true);

        require_once dirname(__DIR__) . '/controller' . '/' . $controller . '.php';

        $controller = new $controller($pdo);

        if ($payload === null) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        $controller->$method($payload);
        break;
    case 'PUT':
        $payload = json_decode(file_get_contents('php://input'), true);

        require_once dirname(__DIR__) . '/controller' . '/' . $controller . '.php';

        $controller = new $controller($pdo);

        if ($payload === null) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        $controller->$method($handler['params'], $payload);
        break;

    case 'DELETE':
        require_once dirname(__DIR__) . '/controller' . '/' . $controller . '.php';

        $controller = new $controller($pdo);

        $controller->$method($handler['params']);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
