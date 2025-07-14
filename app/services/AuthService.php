<?php
class AuthService {
    public function authenticate($username, $password) {
        // Validación básica
        if (empty($username) || empty($password)) {
            throw new Exception('Credenciales inválidas');
        }
        
        // Aquí iría la lógica real de autenticación con la base de datos
        $user = $this->validateCredentials($username, $password);
        
        if (!$user) {
            throw new Exception('Usuario o contraseña incorrectos');
        }
        
        return JWTAuth::generateToken($user['id']);
    }
    
    private function validateCredentials($username, $password) {
        // Simulación: en un caso real se consultaría la base de datos
        $validUser = [
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT)
        ];
        
        if ($username === $validUser['username'] && password_verify($password, $validUser['password'])) {
            return $validUser;
        }
        
        return null;
    }
}