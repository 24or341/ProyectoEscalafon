<?php
require_once __DIR__ . '/../app/config/Config.php';
require_once __DIR__ . '/../app/config/Database.php';

try {
    Config::load();
    $db = Database::getInstance();
    
    // Ejecutar consulta de prueba
    $stmt = $db->query("SELECT 1 AS connection_test");
    $result = $stmt->fetch();
    
    echo "Conexión exitosa a Neon.tech! Resultado: " . $result['connection_test'];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}