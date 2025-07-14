<?php
class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $token = $this->authService->authenticate(
                $data['username'] ?? '',
                $data['password'] ?? ''
            );
            
            echo json_encode(['token' => $token]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}