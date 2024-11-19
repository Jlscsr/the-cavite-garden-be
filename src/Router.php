<?php

namespace App;

use Config\DatabaseConnection;
use App\Helpers\HeaderHelper;
use App\Helpers\ResponseHelper;
use App\Routes;

use RuntimeException;
use Exception;


class Router
{
    private $pdo;
    private $routes;

    public function __construct()
    {
        // Connect to the database
        $this->pdo = DatabaseConnection::connect();

        // Initialize route
        $this->routes = new Routes();
    }

    public function handleRequest($url)
    {

        // Set headers
        HeaderHelper::SendPreflightHeaders();
        HeaderHelper::setResponseHeaders();

        // Get the URL from the query parameter
        $request_method = strtoupper(trim($_SERVER['REQUEST_METHOD']));

        $handler = $this->routes->getRoute($url);

        if (!$handler) {
            throw new RuntimeException("Route not found for URL: $url");
        }

        // Check if middleware is required
        $middleware_required = isset($handler['middleware']) && is_array($handler['middleware']) && $handler['middleware']['required'];

        try {
            if ($middleware_required) {
                $this->handleMiddleware($handler['middleware']['handler']);
            }

            $handlerDefinition = is_array($handler['handler']) ? $handler['handler']['handler'] : $handler['handler'];

            if (empty($handlerDefinition)) {
                throw new RuntimeException("Handler not defined for route.");
            }

            list($controller, $method) = explode('@', $handlerDefinition);

            if (empty($controller) || empty($method)) {
                throw new RuntimeException("Invalid handler format. Expected 'Controller@method'.");
            }

            // Get the controller and method
            $this->processRequest($controller, $method, $handler, $request_method);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage());
        } catch (Exception $e) {
            ResponseHelper::sendErrorResponse($e->getMessage());
        }
    }

    private function handleMiddleware($middleware)
    {
        try {

            $midllewareClass = 'App\\Middleware\\' . $middleware;
            $is_valid = new $midllewareClass();

            if (!$is_valid) {
                ResponseHelper::sendUnauthorizedResponse('Invalid Token or User is not authorized');
                return;
            }
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage());
            return;
        }
    }

    private function processRequest($controller, $method, $handler, $request_method)
    {
        $controllerClass = 'App\\Controllers\\' . $controller;
        $controller = new $controllerClass($this->pdo);

        $payload = json_decode(file_get_contents('php://input'), true);

        switch ($request_method) {
            case 'GET':
                $this->handleGet($controller, $method, $handler);
                break;
            case 'POST':
                $this->handlePost($controller, $method, $handler, $payload);
                break;
            case 'PUT':
                $this->handlePut($controller, $method, $handler, $payload);
                break;
            case 'DELETE':
                $this->handleDelete($controller, $method, $handler);
                break;
            default:
                ResponseHelper::sendErrorResponse('Invalid Request Method', 400);
                break;
        }
    }

    private function handleGet($controller, $method, $handler)
    {
        if (isset($handler['params'])) {
            $controller->$method($handler['params']);
        } else {
            $controller->$method();
        }
    }

    private function handlePost($controller, $method, $handler, $payload)
    {
        if ($payload === null) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        if (isset($handler['params'])) {
            $controller->$method($handler['params'], $payload);
            return;
        } else {
            $controller->$method($payload);
        }
    }

    private function handlePut($controller, $method, $handler, $payload)
    {
        if ($payload === null) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        $controller->$method($handler['params'], $payload);
    }

    private function handleDelete($controller, $method, $handler)
    {
        $controller->$method($handler['params']);
    }
}
