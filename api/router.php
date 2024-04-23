<?php

use Helpers\HeaderHelper;
use Helpers\ResponseHelper;

require_once dirname(__DIR__) . '/config/db_connect.php';
require_once dirname(__DIR__) . '/api/routes.php';

// This code sets the necessary headers for the response.
HeaderHelper::SendPreflighthHeaders();
HeaderHelper::setResponseHeaders();

$pdo = db_connect();
$route = new Route();

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
            ResponseHelper::sendUnauthorizedResponse('Invalid Token or User is not authorized');
            return;
        }
    } catch (Exception $e) {
        ResponseHelper::sendErrorResponse($e->getMessage());
        return;
    }
}

list($controller, $method) = explode('@', is_array($handler['handler']) ? $handler['handler']['handler'] : $handler['handler']);

require_once dirname(__DIR__) . '/controller' . '/' . $controller . '.php';

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

        if ($payload === null) {
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
