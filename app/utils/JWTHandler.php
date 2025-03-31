<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JWTHandler {
    private $secret_key;
    private $algorithm = 'HS256';
    private $issuer = "webbanhang";
    private $audience = "webbanhang_user";
    private $expire_after = 3600; // 1 hour

    public function __construct() {
        $this->secret_key = getenv('JWT_SECRET') ?: 'HUTECH_SECURE_KEY_!@#$%^&*()';
    }

    public function encode(array $data): string {
        $issued_at = time();
        $expire = $issued_at + $this->expire_after;
        
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $issued_at,
            'exp' => $expire,
            'data' => $data
        ];

        return JWT::encode($payload, $this->secret_key, $this->algorithm);
    }

    public function decode(string $jwt): ?array {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, $this->algorithm));
            return (array) $decoded->data;
        } catch (ExpiredException $e) {
            error_log("JWT Expired: " . $e->getMessage());
            return null;
        } catch (SignatureInvalidException $e) {
            error_log("Invalid JWT Signature: " . $e->getMessage());
            return null;
        } catch (Exception $e) {
            error_log("JWT Error: " . $e->getMessage());
            return null;
        }
    }

    public function validateToken(string $token): bool {
        $decoded = $this->decode($token);
        if (!$decoded) return false;
        
        // Additional validation checks
        $now = time();
        if ($decoded['exp'] < $now) return false;
        
        return true;
    }
}