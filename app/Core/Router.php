<?php
namespace App\Core;

class Router
{
	private array $routes = [
		'GET' => [],
		'POST' => [],
		'PUT' => [],
		'DELETE' => [],
	];

	public function get(string $path, string $action): void
	{
		$this->routes['GET'][$this->normalize($path)] = $action;
	}

	public function post(string $path, string $action): void
	{
		$this->routes['POST'][$this->normalize($path)] = $action;
	}

	public function put(string $path, string $action): void
	{
		$this->routes['PUT'][$this->normalize($path)] = $action;
	}

	public function delete(string $path, string $action): void
	{
		$this->routes['DELETE'][$this->normalize($path)] = $action;
	}

	public function dispatch(string $method, string $path): void
	{
		$method = strtoupper($method);
		$path = $this->normalize($path);
		$action = $this->routes[$method][$path] ?? null;
		if (!$action) {
			http_response_code(404);
			echo '404 Not Found';
			return;
		}
		[$controller, $methodName] = explode('@', $action);
		$controllerClass = '\\App\\Controllers\\' . $controller;
		if (!class_exists($controllerClass)) {
			http_response_code(500);
			echo 'Controller not found';
			return;
		}
		$instance = new $controllerClass();
		if (!method_exists($instance, $methodName)) {
			http_response_code(500);
			echo 'Action not found';
			return;
		}
        // CSRF check for POST and PUT (skip for scanner verification to allow kiosk flows)
        if (($method === 'POST' || $method === 'PUT') && function_exists('verify_csrf')) {
            if ($path !== '/scanner/verify') {
                verify_csrf();
            }
        }
		$instance->$methodName();
	}

	private function normalize(string $path): string
	{
		if ($path === '') { return '/'; }
		$path = '/' . ltrim($path, '/');
		return rtrim($path, '/') ?: '/';
	}
}


