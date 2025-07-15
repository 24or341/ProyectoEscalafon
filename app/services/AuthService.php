<?php
class AuthService {
    private $empleadoRepository;

    public function __construct() {
        $this->empleadoRepository = new EmpleadoRepository();
    }

    public function registrarEmpleado($data) {
        // Validar DNI único
        if ($this->empleadoRepository->buscarPorDNI($data['dni'])) {
            throw new Exception("El DNI ya está registrado");
        }

        // Validar formato de contraseña
        if (strlen($data['password']) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres");
        }

        return $this->empleadoRepository->crearEmpleado($data);
    }

    public function autenticar($dni, $password) {
        $empleado = $this->empleadoRepository->buscarPorDNI($dni);
        
        if (!$empleado) {
            throw new Exception("Empleado no encontrado");
        }
        
        if (!password_verify($password, $empleado['password'])) {
            throw new Exception("Contraseña incorrecta");
        }
        
        // Eliminar contraseña antes de devolver
        unset($empleado['password']);
        
        return [
            'empleado' => $empleado,
            'token' => JWTAuth::generateToken($empleado['id'])
        ];
    }
}