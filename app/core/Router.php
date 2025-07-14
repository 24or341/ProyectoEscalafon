<?php
class Router {
    private $routes = [];
    private $currentMiddleware = null;

    public function group($options, $callback) {
        $previousMiddleware = $this->currentMiddleware;
        $this->currentMiddleware = $options['middleware'] ?? null;
        $callback($this);
        $this->currentMiddleware = $previousMiddleware;
    }

    public function get($uri, $handler) {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post($uri, $handler) {
        $this->addRoute('POST', $uri, $handler);
    }

    private function addRoute($method, $uri, $handler) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'middleware' => $this->currentMiddleware
        ];
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            // Convertir URI con parámetros a regex
            $pattern = str_replace('/', '\/', $route['uri']);
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $pattern);
            $pattern = "/^{$pattern}$/";
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                // Manejar middleware
                if ($route['middleware']) {
                    $middlewareClass = ucfirst($route['middleware']) . 'Middleware';
                    if (class_exists($middlewareClass)) {
                        $middleware = new $middlewareClass();
                        $middleware->handle();
                    }
                }
                
                // Extraer parámetros
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Ejecutar controlador
                list($controllerName, $methodName) = explode('@', $route['handler']);
                $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";
                
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $controllerName();
                    $controller->$methodName($params);
                    return;
                }
            }
        }
        
        // Ruta no encontrada
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}