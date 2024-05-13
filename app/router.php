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

    /**
     * Runs the application by processing the incoming request.
     *
     * @return void
     */
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

    /**
     * Handles the middleware for the given handler.
     *
     * @param array $handler The handler containing the middleware information.
     * @throws RuntimeException If the request validation fails.
     * @return void
     */
    private function handleMiddleware(array $handler): void
    {
        // Check if middleware is enabled on the handler
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

    /**
     * Parses the handler to extract controller name and method.
     *
     * @param array $handler The handler containing the controller and method information.
     * @return array An array containing the controller name and method name.
     */
    private function parseHandler(array  $handler): array
    {
        $handlerValue = is_array($handler['handler']) ? $handler['handler']['handler'] : $handler['handler'];
        return explode('@', $handlerValue);
    }

    /**
     * Executes the specified method on the given controller based on the request method.
     *
     * @param string $controllerName The name of the controller class.
     * @param string $methodName The name of the method to be executed.
     * @param string $requestMethod The HTTP request method used for the request.
     * @param array $handler The handler containing method parameters and other information.
     * @throws RuntimeException If an error occurs during method execution.
     * @return void
     */
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
                    ResponseHelper::sendErrorResponse("Invalid payload format or payload is empty", 400);
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
