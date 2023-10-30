<?php
header('Content-Type: application/json');
require_once 'jwt_helper.php';

$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
$validApiKey = 'mamearth'; // Replace with your actual valid API key

if ($apiKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode(array('message' => 'Unauthorized'));
    exit();
}
$data = ['user_id' => 123, 'username' => 'john_doe'];
$token = JWTHelper::generateToken($data);

echo json_encode(array('token' => $token));

?>
