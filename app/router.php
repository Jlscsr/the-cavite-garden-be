<?php

namespace App;

use Config\DatabaseConnection;

use App\Routes;

use App\Middlewares\BaseMiddleware;

use App\Helpers\HeaderHelper;
use App\Helpers\ResponseHelper;

class Router
{
    private $pdo;
    private $route;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::connect();
        $this->route = new Routes();
    }

    public function run(): void
    {
        HeaderHelper::SendPreflightHeaders();
        HeaderHelper::SetResponseHeaders();

        $url = $_GET['url'] ?? '';
        $requestMethod = strtoupper(trim($_SERVER['REQUEST_METHOD']));
        $handler = $this->route->getRoute($url);

        $this->handleMiddleware($handler);

        list($controllerName, $methodName) = $this->parseHandler($handler);

        $this->invokeControllerMethod($controllerName, $methodName, $requestMethod, $handler);
    }

    private function handleMiddleware(array $handler): void
    {
        if (isset($handler['middleware']) && !empty($handler['middleware']) && $handler['middleware']) {
            try {
                $middleware = new BaseMiddleware($handler['requiredRole']);
                $middleware->validateRequest();
            } catch (RuntimeException $e) {
                ResponseHelper::sendErrorResponse($e->getMessage(), 401);
                exit;
            }
        }
    }

    private function parseHandler(array  $handler): array
    {
        $handlerValue = is_array($handler['handler']) ? $handler['handler']['handler'] : $handler['handler'];
        return explode('@', $handlerValue);
    }

    private function invokeControllerMethod(string $controllerName, string $methodName, string $requestMethod, array $handler): void
    {
        require_once dirname(__DIR__) . '/app/' . 'controllers/' . $controllerName . '.php';

        $controller = new $controllerName($this->pdo);

        switch ($requestMethod) {
            case 'GET':
                $params = isset($handler['params']) ? $handler['params'] : null;
                $controller->$methodName($params);
                break;
            case 'POST':
                $payload = json_decode(file_get_contents('php://input'), true);
                if ($payload === null && $_GET['url'] !== 'api/auth/logout') {
                    ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
                    exit;
                }
                $controller->$methodName($payload);
                break;
            case 'PUT':
                $payload = json_decode(file_get_contents('php://input'), true);
                if ($payload === null && $url !== 'api/auth/logout') {
                    ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
                    exit;
                }
                $controller->$methodName($handler['params'], $payload);
                break;
            case 'DELETE':
                $controller->$methodName($handler['params']);
                break;
            default:
                ResponseHelper::sendErrorResponse('Invalid Request Method', 405);
                break;
        }
    }
}
