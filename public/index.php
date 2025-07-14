<?php
require_once __DIR__ . '/../app/config/Config.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/core/Router.php';

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
    $router->post('/login', 'AuthController@login');
    
    // ===== RUTAS PROTEGIDAS =====
    $router->group(['middleware' => 'auth'], function($router) {
        // Empleados
        $router->get('/empleados', 'EmpleadoController@index');
        $router->post('/empleados', 'EmpleadoController@store');
        
        // Documentos
        $router->post('/documentos', 'DocumentoController@upload');
        
        // Trayectoria laboral
        $router->get('/trayectoria/{id}', 'TrayectoriaController@show');
        
        // ... otras rutas
    });
    
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