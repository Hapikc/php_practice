<?php

namespace Src;

use Error;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher\MarkBased as Dispatcher;
use Src\Traits\SingletonTrait;

class Route
{
    use SingletonTrait;

    private string $currentRoute = '';
    private $currentHttpMethod;
    private string $prefix = '';
    private RouteCollector $routeCollector;

    private function __construct()
    {
        $this->routeCollector = new RouteCollector(new Std(), new MarkBased());
    }

    public static function add($httpMethod, string $route, array $action): self
    {
        // Позволяем передавать как строку, так и массив методов
        $methods = is_array($httpMethod) ? $httpMethod : [$httpMethod];

        foreach ($methods as $method) {
            self::single()->routeCollector->addRoute($method, $route, $action);
        }

        self::single()->currentHttpMethod = $httpMethod;
        self::single()->currentRoute = $route;
        return self::single();
    }

    public static function group(string $prefix, callable $callback): void
    {
        self::single()->routeCollector->addGroup($prefix, $callback);
        Middleware::single()->group($prefix, $callback);
    }

    public function setPrefix(string $value = ''): self
    {
        $this->prefix = $value;
        return $this;
    }

    public function redirect(string $url): void
    {
        header('Location: ' . $this->getUrl($url));
    }

    public function getUrl(string $url): string
    {
        return $this->prefix . $url;
    }

    public function middleware(...$middlewares): self
    {
        Middleware::single()->add($this->currentHttpMethod, $this->currentRoute, $middlewares);
        return $this;
    }

    public function start(): void
    {
        try {
            $httpMethod = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];

            // Удаляем query string
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = rawurldecode($uri);
            $uri = substr($uri, strlen($this->prefix));

            $dispatcher = new Dispatcher($this->routeCollector->getData());
            $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    header("HTTP/1.0 404 Not Found");
                    (new View())->render('errors.404');
                    exit;

                case Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = implode(', ', $routeInfo[1]);
                    header("HTTP/1.0 405 Method Not Allowed");
                    (new View())->render('errors.405', [
                        'allowedMethods' => $allowedMethods,
                        'requestedMethod' => $httpMethod
                    ]);
                    exit;

                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = array_values($routeInfo[2]);
                    $middlewareResult = Middleware::single()->runMiddlewares($httpMethod, $uri);

                    $class = $handler[0];
                    $action = $handler[1];

                    if (!class_exists($class)) {
                        throw new Error("Class {$class} not found");
                    }

                    if (!method_exists($class, $action)) {
                        throw new Error("Method {$action} not found in class {$class}");
                    }

                    call_user_func([new $class, $action], ...array_merge($vars, [$middlewareResult]));
                    break;
            }
        } catch (\Exception $e) {
            if ($e->getCode() === 403) {
                header("HTTP/1.0 403 Forbidden");
                (new View())->render('errors.403');
                exit;
            }

            // Логирование ошибки
            error_log($e->getMessage());

            header("HTTP/1.0 500 Internal Server Error");
            (new View())->render('errors.500', ['message' => $e->getMessage()]);
            exit;
        }
    }
}