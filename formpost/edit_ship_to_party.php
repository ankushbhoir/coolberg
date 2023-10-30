<?php

require_once("../../it_config.php");
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/Constants.php';

$error = array();
$success = "";
extract($_POST);
$user = getCurrStore();
$userId = $user->id;
$dbl = new DBLogic();
$db = new DBConn();
//print_r($_POST);
//  return;
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;

$shippingid = isset($_POST['shippingid']) ? trim($_POST['shippingid']) : false;
$shiptoparty = isset($_POST['shiptoparty']) ? trim($_POST['shiptoparty']) : false;
$siteindentifier = isset($_POST['siteindentifier']) ? trim($_POST['siteindentifier']) : false;
$site_identifier_type = isset($_POST['site_identifier_type']) ? trim($_POST['site_identifier_type']) : false;
//$category = isset($_POST['category']) ? trim($_POST['category']) : false;
//$plant = isset($_POST['plant']) ? trim($_POST['plant']) : false;
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : false;
//$margin = isset($_POST['margin']) ? trim($_POST['margin']) : false;
$distribution_channel = isset($_POST['distribution_channel']) ? trim($_POST['distribution_channel']) : false;
$sales_document_type = isset($_POST['sales_document_type']) ? trim($_POST['sales_document_type']) : false;
$distribution_channel_code = isset($_POST['distribution_channel_code']) ? trim($_POST['distribution_channel_code']) : false;

$old_shiptoparty = isset($_POST['old_shiptoparty']) ? trim($_POST['old_shiptoparty']) : false;
$old_siteidentifier = isset($_POST['old_siteidentifier']) ? trim($_POST['old_siteidentifier']) : false;
$old_site_identifier_type = isset($_POST['old_site_identifier_type']) ? trim($_POST['old_site_identifier_type']) : false;
//$old_category = isset($_POST['old_category']) ? trim($_POST['old_category']) : false;
//$old_plant = isset($_POST['old_plant']) ? trim($_POST['old_plant']) : false;
$old_customer_name = isset($_POST['old_customer_name']) ? trim($_POST['old_customer_name']) : false;
//$old_margin = isset($_POST['old_margin']) ? trim($_POST['old_margin']) : false;
$old_distribution_channel = isset($_POST['old_distribution_channel']) ? trim($_POST['old_distribution_channel']) : false;
$old_sales_document_type = isset($_POST['old_sales_document_type']) ? trim($_POST['old_sales_document_type']) : false;
$old_distribution_channel_code = isset($_POST['old_distribution_channel_code']) ? trim($_POST['old_distribution_channel_code']) : false;

if ($shiptoparty != $old_shiptoparty) {
    $stparray['Ship To Party'] = $old_shiptoparty . '::' . $shiptoparty;
}
if ($siteindentifier != $old_siteidentifier) {
    $stparray['Site Indentifier'] = $old_siteidentifier . '::' . $siteindentifier;
}
if ($site_identifier_type != $old_site_identifier_type) {
    $stparray['Site Identifier Type'] = $old_site_identifier_type . '::' . $site_identifier_type;
}
if ($customer_name != $old_customer_name) {
    $stparray['Customer Name'] = $old_customer_name . '::' . $customer_name;
}
if ($distribution_channel != $old_distribution_channel) {
    $stparray['Distribution Channel'] = $old_distribution_channel . '::' . $distribution_channel;
}
if ($sales_document_type != $old_sales_document_type) {
    $stparray['Sales Document Type'] = $old_sales_document_type . '::' . $sales_document_type;
}
if ($distribution_channel_code != $old_distribution_channel_code) {
    $stparray['Distribution Channel Code'] = $old_distribution_channel_code . '::' . $distribution_channel_code;
}
//else{
//    
//}
$json_obj = json_encode($stparray);
//print_r($json_obj);
//if (isset($json_obj) && !empty($json_obj)) {
//    $insert_qry = "insert into it_masters_logs set master_type = 'shiptoparty', updateby_id = $userId, change_data = '$json_obj',createtime = now(),updatetime = now()";
////             echo $insert_qry;
////             exit;
//    $result = $db->execInsert($insert_qry);
//}

try {
    if ($shiptoparty != $old_shiptoparty || $siteindentifier != $old_siteidentifier || $site_identifier_type != $old_site_identifier_type || $customer_name != $old_customer_name || $distribution_channel != $old_distribution_channel || $sales_document_type != $old_sales_document_type || $distribution_channel_code != $old_distribution_channel_code) {
        $obj = $dbl->checkShippingDetails($shiptoparty, $siteindentifier, $site_identifier_type, $customer_name, $distribution_channel, $sales_document_type, $distribution_channel_code);
        if (isset($obj) && !empty($obj)) {
            $error['addrs_already_exist'] = "shiptoparty details already exist. Try to create new ship to party";
        }
    }

    if (count($error) == 0) {

        $last_inserted_id = $dbl->updateShippingDetails($shippingid, $shiptoparty, $siteindentifier, $site_identifier_type, $customer_name, $distribution_channel, $sales_document_type, $distribution_channel_code, $old_shiptoparty, $old_siteidentifier, $old_site_identifier_type, $old_customer_name, $old_distribution_channel, $old_sales_document_type, $old_distribution_channel_code);
        // print_r($last_inserted_id);
        if (trim($last_inserted_id) == -1) {
            $error['insert_loc_fail'] = "New shiptoparty is not created. Try to create it again.";
        } else {
            $success = "successEdit";
            if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
                $insert_qry = "insert into it_masters_logs set master_id = $shippingid,master_type = 'Ship To Party', updateby_id = $userId, change_data = '$json_obj',createtime = now(),updatetime = now()";
//             echo $insert_qry;
//             exit;
                $result = $db->execInsert($insert_qry);
            }
        }
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'ship/to/party/edit/shippingId=' . $shippingid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'ship/to/party';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;

