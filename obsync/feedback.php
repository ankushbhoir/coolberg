<?php
header('Content-Type: application/json');
require_once('../../it_config.php');
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/db/DBLogic.php";
require_once 'jwt_helper.php';
require_once "lib/securereq/clsSecureReq.php";

// Check if the request method is allowed
$clsReq = new clsSecureReq();
$allowedMethods = ["POST"];
$method = $_SERVER["REQUEST_METHOD"];

$response = $clsReq->isMethodAllowed($method, $allowedMethods);

if ($response != 200) {
    http_response_code($response);
    exit;
}

$db = new DBConn();

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$matches = [];

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(array('message' => 'Unauthorized'));
    exit();
}

$token = $matches[1];

try {
    $decodedData = JWTHelper::verifyToken($token);

    if ($decodedData === null) {
        http_response_code(401);
        echo json_encode(array('message' => 'Unauthorized'));
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jsonData = file_get_contents('php://input');

        // Check if JSON data was successfully retrieved
        if ($jsonData === false) {
            throw new Exception('Failed to retrieve JSON data');
        }

        // Parse the JSON data
        $data = json_decode($jsonData, true); // true for associative array

        // Check if JSON decoding was successful
        if ($data === null) {
            throw new Exception('Error decoding JSON data');
        }

        // Check if the "po_numbers" field exists in the JSON data
        if (!isset($data['po_numbers'])) {
            throw new Exception('Missing "po_numbers" field in JSON data');
        }

        $poNumbers = $data['po_numbers'];

        foreach ($poNumbers as $number) {
             $pro_query = "SELECT id, invoice_no FROM it_po WHERE invoice_no = '$number'";
            $po_obj = $db->fetchObject($pro_query);

            if (isset($po_obj) && !empty($po_obj)) {
                 $qry = "UPDATE it_po SET status = '21' WHERE invoice_no = '$number' AND status = 11";
                $db->execQuery($qry);
                
                //return;
            } else {
                $msg = "$number number not found";
                echo json_encode(array('message' => $msg));
               // return;
            }
        }
        echo json_encode(array('message' => 'Success'));
    } else {
        http_response_code(400); // Bad Request
        exit;
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(array('error' => $e->getMessage()));
}
?>
