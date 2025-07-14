<?php
class AuthMiddleware {
    public function handle() {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (empty($authHeader)) {
            $this->unauthorized('Token no proporcionado');
        }
        
        if (!preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            $this->unauthorized('Formato de token inválido');
        }
        
        $token = $matches[1];
        
        if (!$payload = JWTAuth::validateToken($token)) {
            $this->unauthorized('Token inválido o expirado');
        }
        
        $_SERVER['USER_ID'] = $payload['sub'];
    }
    
    private function unauthorized($message) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }
}