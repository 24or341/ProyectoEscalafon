<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        Config::load();
        
        // Obtener configuración específica para Neon.tech
        $host = Config::get('PGHOST');
        $dbname = Config::get('PGDATABASE');
        $user = Config::get('PGUSER');
        $password = Config::get('PGPASSWORD');
        $sslmode = Config::get('PGSSLMODE', 'require');
        $channelbinding = Config::get('PGCHANNELBINDING', 'require');
        $port = Config::get('PGPORT', '5432');
        
        // Construir DSN con parámetros SSL
        $dsn = "pgsql:"
            . "host=$host;"
            . "port=$port;"
            . "dbname=$dbname;"
            . "sslmode=$sslmode;"
            . "channel_binding=$channelbinding";
        
        try {
            // Conexión simple sin opciones SSL adicionales
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true
            ]);
            
            // Configuración adicional recomendada
            $this->connection->exec("SET NAMES 'UTF8'");
            $this->connection->exec("SET timezone = 'UTC'");
            
        } catch (PDOException $e) {
            // Manejo de errores mejorado
            $errorMessage = "NEON TECH CONNECTION ERROR: " . $e->getMessage();
            error_log($errorMessage);
            
            // Mensaje amigable para producción
            $publicMessage = (Config::get('APP_ENV') === 'production')
                ? "Error de conexión con la base de datos"
                : $errorMessage;
            
            throw new Exception($publicMessage);
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}