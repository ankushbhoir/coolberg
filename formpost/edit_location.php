<?php
require_once("../../it_config.php");
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/Constants.php';

$error = array();
$success = "";
extract($_POST);
$dbl = new DBLogic();
// print_r($_POST);
// return;
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;




//print_r($error);
//exit;
$shippingId   = isset($_POST['shippingid']) ? trim($_POST['shippingid']) : false;

$custCode = isset($_POST['custCode']) ? trim($_POST['custCode']) : false;
$storeCode = isset($_POST['storeCode']) ? trim($_POST['storeCode']) : false;
$dcName = isset($_POST['dcName']) ? trim($_POST['dcName']) : false;
$dcAddress = isset($_POST['dcAddress']) ? trim($_POST['dcAddress']) : false;
$dcCity = isset($_POST['dcCity']) ? trim($_POST['dcCity']) : false;


$old_custCode = isset($_POST['old_custCode']) ? trim($_POST['old_custCode']) : false;
$old_storeCode = isset($_POST['old_storeCode']) ? trim($_POST['old_storeCode']) : false;
$old_dcName = isset($_POST['old_dcName']) ? trim($_POST['old_dcName']) : false;
$old_dcAddress = isset($_POST['old_dcAddress']) ? trim($_POST['old_dcAddress']) : false;
$old_dcCity = isset($_POST['old_dcCity']) ? trim($_POST['old_dcCity']) : false;

$old_shippingAddress = isset($_POST['old_shippingAddress']) ? trim($_POST['old_shippingAddress']) : false;
//Remove Line Breaks From Textarea
$dcAddress = preg_replace( "/(\r|\n)/"," ",$dcAddress);

try{
    if($dcAddress != $old_dcAddress){
        $obj = $dbl->checkShippingAddress($dcAddress);
        if(isset($obj) && !empty($obj)){
            $error['addrs_already_exist'] = "$dcAddress already exist. Try to create new location";
        }
    }

    if(count($error) == 0){

        $last_inserted_id = $dbl->updateShippingAddress($dcAddress,$dcName,$dcCity,$custCode,$shippingId,$old_dcAddress,$old_dcName,$old_custCode,$old_dcCity);
        // print_r($last_inserted_id);
        if(trim($last_inserted_id)== -1){
            $error['insert_loc_fail'] = "New location is not created. Try to create it again.";
        }else{
            $objBU = $dbl->checkShippingAddressInBusinessUnit($old_shippingAddress);
            $last_BU_id = $dbl->updateBussinessUnit($objBU->id,$storeCode,$dcAddress,$old_storeCode);
            if(trim($last_BU_id)== -1){
                $error['insert_bu_fail'] = "Dependancy could not be set. Bussiness unit does not set successfully";
            }else{
                $result = $dbl->insertIntoLocationEditDiary($dcAddress,$dcName,$dcCity,$custCode,$shippingId,$old_dcAddress,$old_dcName,$old_custCode,$old_dcCity,$storeCode,$old_storeCode);
                $success = "successEdit";
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
    $redirect = 'location/edit/shippingId='.$shippingId;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'locations';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;