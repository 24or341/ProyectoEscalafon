<?php
class Config {
    public static function load($path = null) {
        $envFile = $path ?? __DIR__ . '/../../.env';
        
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0 || trim($line) === '') continue;
            
            // Manejar valores entre comillas
            if (preg_match('/^([A-Z0-9_]+)=(.*)/', $line, $matches)) {
                $name = trim($matches[1]);
                $value = trim($matches[2]);
                
                // Remover comillas
                if (preg_match('/^"(.*)"$/', $value, $quoted)) {
                    $value = $quoted[1];
                }
                
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
    
    public static function get($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}