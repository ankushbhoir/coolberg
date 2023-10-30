<?php
require_once("../../it_config.php");
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/Constants.php';

$error = array();
$success = "";
extract($_POST);
$dbl = new DBLogic();
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;




//print_r($error);
//exit;
$chain   = isset($_POST['chain']) ? trim($_POST['chain']) : false;
$custCode = isset($_POST['custCode']) ? trim($_POST['custCode']) : false;
$storeCode = isset($_POST['storeCode']) ? trim($_POST['storeCode']) : false;
$dcName = isset($_POST['dcName']) ? trim($_POST['dcName']) : false;
$dcAddress = isset($_POST['dcAddress']) ? trim($_POST['dcAddress']) : false;
$dcCity = isset($_POST['dcCity']) ? trim($_POST['dcCity']) : false;
$state = isset($_POST['state']) ? trim($_POST['state']) : false;


//Remove Line Breaks From Textarea
$dcAddress = preg_replace( "/(\r|\n)/"," ",$dcAddress);

try{

    if(isset($chain) && trim($chain)== ""){
        $error['select_chain'] = "Please select chain from the list.";
    }
    if(isset($state) && trim($state)== ""){
        $error['select_state'] = "Please select state from the list.";
    }
        
    if(count($error) == 0){
            $obj = $dbl->checkShippingAddress($dcAddress);
            // print_r($obj);
            // return;
            if(isset($obj) && !empty($obj)){
                $error['addrs_already_exist'] = "$dcAddress already exist. Try to create new location";
            }else{
                $objIni = $dbl->getIniList($chain);
                $iniList = $objIni->iniList;
                $stateObj = $dbl->getStateName($state);
                $stateName = $stateObj->name;

                $last_inserted_id = $dbl->insertShippingAddress($chain,$iniList,$dcAddress,$dcName,$dcCity,$stateName,$custCode);
                if(trim($last_inserted_id)<=0){
                    $error['insert_loc_fail'] = "New location is not created. Try to create it again.";
                }else{
                    $objBU = $dbl->checkShippingAddressInBusinessUnit($dcAddress);
                    $buId = 0;
                    if(isset($objBU) && !empty($objBU)){
                        $buId = $objBU->id;
                    }else{
                        $last_BU_id = $dbl->insertBussinessUnit($chain,$storeCode,$dcAddress);
                        $buId = $last_BU_id;
                    }
                    if(trim($buId)<=0){
                        $error['insert_bu_fail'] = "Dependancy could not be set. Bussiness unit does not set successfully";
                    }else{
                        $success = "success";
                    }
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
    $redirect = 'location/create';
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