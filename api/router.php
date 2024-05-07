<?php

require_once dirname(__DIR__) . '/config/DBConnect.php';
require_once dirname(__DIR__) . '/api/routes.php';

use Helpers\HeaderHelper;
use Helpers\ResponseHelper;

use Middlewares\BaseMiddleware;

// This code sets the necessary headers for the response.
HeaderHelper::SendPreflighthHeaders();
HeaderHelper::setResponseHeaders();

$pdo = DBConnect();
$route = new Route();

$url = $_GET['url'] ?? '';

$request_method = strtoupper(trim($_SERVER['REQUEST_METHOD']));

$handler = $route->get_route($url);

$isMiddlewareRequired = isset($handler['middleware']) && !empty($handler['middleware']) && $handler['middleware'];

if ($isMiddlewareRequired) {
    try {
        $middleware = new BaseMiddleware($handler['requiredRole']);

        $middleware->validateRequest();
    } catch (RuntimeException $e) {
        ResponseHelper::sendErrorResponse($e->getMessage(), 401);
        exit;
    }
}

list($controller, $method) = explode('@', is_array($handler['handler']) ? $handler['handler']['handler'] : $handler['handler']);

require_once dirname(__DIR__) . '/controllers' . '/' . $controller . '.php';

$controller = new $controller($pdo);

switch ($request_method) {
    case 'GET':
        if (isset($handler['params'])) {
            $controller->$method($handler['params']);
        } else {
            $controller->$method();
        }
        break;
    case 'POST':
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($payload === null && $url !== 'api/auth/logout') {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        $controller->$method($payload);
        break;
    case 'PUT':
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($payload === null) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        $controller->$method($handler['params'], $payload);
        break;

    case 'DELETE':
        $controller->$method($handler['params']);
        break;
    default:
        ResponseHelper::sendErrorResponse('Invalid Request Method', 400);
        break;
}
