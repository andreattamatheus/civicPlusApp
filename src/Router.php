<?php

namespace App;

class Router
{
    private $routes = [];

    public function get($uri, $action)
    {
        $this->register('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->register('POST', $uri, $action);
    }

    public function register($method, $uri, $action)
    {
        $this->routes[] = ['method' => $method, 'uri' => $uri, 'action' => $action];
    }

    public function resolve($requestUri, $requestMethod)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchUri($route['uri'], $requestUri)) {
                return $this->callAction($route['action'], $this->getUriParams($route['uri'], $requestUri));
            }
        }

        return $this->abort(404);
    }

    private function matchUri($routeUri, $requestUri)
    {
        // Match URI, accounting for route parameters like /events/{id}
        $routeUri = preg_replace('/\{[^\}]+\}/', '([^\/]+)', $routeUri);
        return preg_match('#^' . $routeUri . '$#', $requestUri);
    }

    private function getUriParams($routeUri, $requestUri)
    {
        // Extract route parameters (e.g. /events/{id})
        $routeUriParts = explode('/', $routeUri);
        $requestUriParts = explode('/', $requestUri);

        $params = [];
        foreach ($routeUriParts as $index => $part) {
            if (strpos($part, '{') === 0 && isset($requestUriParts[$index])) {
                $params[] = $requestUriParts[$index];
            }
        }
        return $params;
    }

    private function callAction($action, $params = [])
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        } elseif (is_string($action)) {
            list($controller, $method) = explode('@', $action);
            $controller = "App\\Controllers\\{$controller}";
            if (class_exists($controller)) {
                return call_user_func_array([new $controller, $method], $params);
            }
        }

        return $this->abort(500);
    }

    private function abort($code)
    {
        http_response_code($code);
        echo $code == 404 ? '404 Not Found' : 'An error occurred.';
    }
}
