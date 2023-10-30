<?php

class JWTHelper {
    private static $key = 'M@m@earthInt0uch2023';
    private static $expiration = 24 * 60 * 60; // 24 hours in seconds

    public static function generateToken($data) {
        $currentTime = time();
        $expirationTime = $currentTime + self::$expiration;

        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode(['data' => $data, 'exp' => $expirationTime]);

        $base64Header = base64_encode($header);
        $base64Payload = base64_encode($payload);

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$key, true);
        $base64Signature = base64_encode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    public static function verifyToken($token) {
        list($base64Header, $base64Payload, $base64Signature) = explode('.', $token);

        $signature = base64_decode($base64Signature);
        $expectedSignature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$key, true);

        if (!hash_equals($signature, $expectedSignature)) {
            return null;
        }

        $payload = json_decode(base64_decode($base64Payload), true);

        if (isset($payload['exp']) && $payload['exp'] >= time()) {
            return $payload['data'];
        }

        return null;
    }
}

?>
