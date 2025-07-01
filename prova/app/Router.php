<?php
class Router
{
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function get($uri, $controllerAction, $middlewares = [])
    {
        $this->addRoute('GET', $uri, $controllerAction, $middlewares);
    }

    public function post($uri, $controllerAction, $middlewares = [])
    {
        $this->addRoute('POST', $uri, $controllerAction, $middlewares);
    }

    private function addRoute($method, $uri, $controllerAction, $middlewares)
    {
        $this->routes[$method][$uri] = [
            'handler' => $controllerAction,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if (BASE_PATH !== '' && strpos($uri, BASE_PATH) === 0) {
        $uri = substr($uri, strlen(BASE_PATH));
    }
    $uri = $uri ?: '/';

    // Parse query string parameters
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $_GET);
    

        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];

            // Execute middlewares
            foreach ($route['middlewares'] as $middleware) {
                $middlewareInstance = $this->container->get($middleware);
                if (!$middlewareInstance->handle()) {
                    return; // Middleware blocked the request
                }
            }

            // Process controller
            [$controllerName, $action] = explode('@', $route['handler']);
            $controller = $this->container->get($controllerName);
            
            if (method_exists($controller, $action)) {
                return $controller->$action();
            }
        }

        // Route not found
        http_response_code(404);
        require VIEWS_PATH . '404.php';
    }
}