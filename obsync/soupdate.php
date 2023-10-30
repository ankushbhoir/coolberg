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

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$matches = [];

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    exit();
}

$token = $matches[1];

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
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'Failed to retrieve JSON data'));
    } else {
        // Parse the JSON data and access the "data" element
        $dataObj = json_decode($jsonData);

        // Check if "data" element exists and is an array
        if (isset($dataObj->data) && is_array($dataObj->data)) {
            $data = $dataObj->data;

            try {
                $db = new DBConn();
                $logFileName = 'query_log.txt';
                $logFile = fopen($logFileName, 'a');
                $success = true; // Flag to track success

                foreach ($data as $item) {
                    $po_number = $item->po_number;
                    $so_number = $item->so_number;
                    $sap_message = $item->sap_message;

                    if (!isset($po_number) || trim($po_number) == "") {
                        fwrite($logFile, "Po number not found\n");
                        $success = false; // Mark as failure
                        continue;
                    }

                    $pro_query = "SELECT * FROM it_po WHERE invoice_no = '$po_number'";
                    $po_obj = $db->fetchObject($pro_query);

                    if (isset($po_obj) && !empty($po_obj)) {
                        $qry = "UPDATE it_po SET so_number = '$so_number', sap_message = '$sap_message' WHERE invoice_no = '$po_number'";
                        $db->execQuery($qry);
                        fwrite($logFile, "Updated Po number $po_number\n");
                    } else {
                        fwrite($logFile, "Po number $po_number not found\n");
                        $success = false; // Mark as failure
                    }
                }

                fclose($logFile);

                if ($success) {
                    echo json_encode(array('message' => 'Success'));
                } else {
                    echo json_encode(array('message' => 'Partial success or failure'));
                }
            } catch (Exception $xcp) {
                $xcp->getMessage();
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array('error' => 'Invalid JSON format. Expected "data" array.'));
        }
    }
} else {
    http_response_code(400); // Bad Request
    exit;
}
