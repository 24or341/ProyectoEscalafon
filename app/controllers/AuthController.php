<?php
class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $empleadoId = $this->authService->registrarEmpleado($data);
            echo json_encode([
                'success' => true,
                'message' => 'Empleado registrado exitosamente',
                'id' => $empleadoId
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $result = $this->authService->autenticar(
                $data['dni'] ?? '',
                $data['password'] ?? ''
            );
            
            echo json_encode([
                'success' => true,
                'token' => $result['token'],
                'empleado' => $result['empleado']
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function verifyToken() {
        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['token'] ?? '';
        
        try {
            $decoded = JWTAuth::validateToken($token);
            http_response_code(200);
            echo json_encode(['valid' => true]);
        } catch (Exception $e) {
            http_response_code(200);
            echo json_encode(['valid' => false]);
        }
    }
}