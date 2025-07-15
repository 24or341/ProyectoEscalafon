<?php
require_once __DIR__ . '/../app/config/Config.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/core/Router.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de errores según entorno
try {
    Config::load();
    
    if (Config::get('APP_ENV') === 'production') {
        error_reporting(0);
        ini_set('display_errors', 0);
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
    
    $router = new Router();
    
    // ===== RUTAS PÚBLICAS =====
    $router->post('/register', 'AuthController@register'); // Registrar nuevo empleado
    $router->post('/login', 'AuthController@login');       // Iniciar sesión
    
    // ===== RUTAS PROTEGIDAS =====
    $router->group(['middleware' => 'auth'], function($router) {
        // Empleados
        $router->get('/empleados/{id}', 'EmpleadoController@show');
        $router->post('/empleados', 'EmpleadoController@store');
        $router->post('/verify-token', 'AuthController@verifyToken');
        
        // Documentos
        $router->post('/documentos', 'DocumentoController@upload');
        
        // Trayectoria laboral
        $router->get('/trayectoria/{id}', 'TrayectoriaController@show');
        
        // ... otras rutas
    });

    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (preg_match('/\.(?:html|css|js|png|jpg|jpeg|gif)$/', $requestPath)) {
        $filePath = __DIR__ . $requestPath;
        
        if (file_exists($filePath)) {
            $mimeTypes = [
                'html' => 'text/html',
                'css'  => 'text/css',
                'js'   => 'application/javascript',
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif'
            ];
            
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
            
            header("Content-Type: $contentType");
            readfile($filePath);
            exit;
        }
    }
    $router->dispatch();

} catch (Exception $e) {
    $response = ['error' => 'Error interno del sistema'];
    
    if (Config::get('DEBUG_MODE') === 'true') {
        $response['details'] = $e->getMessage();
        $response['trace'] = $e->getTraceAsString();
    }
    
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}