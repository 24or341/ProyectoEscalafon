<?php
class EmpleadoRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function crearEmpleado($data) {
        $sql = "INSERT INTO escalafon.empleado (
            nombres, 
            apellidos, 
            dni, 
            fecha_nacimiento, 
            direccion, 
            telefono, 
            email, 
            afp_id, 
            fecha_ingreso
        ) VALUES (
            :nombres, 
            :apellidos, 
            :dni, 
            :fecha_nacimiento, 
            :direccion, 
            :telefono, 
            :email, 
            :afp_id, 
            :fecha_ingreso
        ) RETURNING id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombres' => $data['nombres'],
            ':apellidos' => $data['apellidos'],
            ':dni' => $data['dni'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':direccion' => $data['direccion'],
            ':telefono' => $data['telefono'],
            ':email' => $data['email'],
            ':afp_id' => $data['afp_id'],
            ':fecha_ingreso' => $data['fecha_ingreso']
        ]);
        
        return $stmt->fetchColumn();
    }

    public function buscarPorDNI($dni) {
        $sql = "SELECT * FROM escalafon.empleado WHERE dni = :dni";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}