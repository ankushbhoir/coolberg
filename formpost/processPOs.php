<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
extract($_GET);
$errors = array();
try{
	$db = new DBConn();
	$poIds = isset($_GET['ids'])? $_GET['ids'] : false;
	if (isset($poIds) && $poIds != null && !empty($poIds)){
            $query = "update it_po_details set ready_process = 1 where id in ($poIds)";
            $db->execUpdate($query);
            $db->closeConnection();
            $success = "success";
        }else{
            $errors["error"] = "Not able to process POs";
        }
} catch (Exception $xcp) {
	print $xcp->getMessage();
}

if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'unprocessed/po';
    // print $redirect;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'unprocessed/po';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;