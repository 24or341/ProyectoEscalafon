<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth {
    public static function generateToken($userId) {
        $secretKey = Config::get('JWT_SECRET');
        $expire = (int) Config::get('JWT_EXPIRE', 3600);
        
        $payload = [
            'iss' => 'escalafon-system',
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $expire
        ];
        
        return JWT::encode($payload, $secretKey, 'HS256');
    }
    
    public static function validateToken($token) {
        $secretKey = Config::get('JWT_SECRET');
        
        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}