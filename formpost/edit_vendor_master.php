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
 print_r($_POST);
// return;
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;

$vendorid = isset($_POST['vendorid']) ? trim($_POST['vendorid']) : false;
$vendorno = isset($_POST['vendorno']) ? trim($_POST['vendorno']) : false;
$plant = isset($_POST['plant']) ? trim($_POST['plant']) : false;
$storage_location_code = isset($_POST['storage_location_code']) ? trim($_POST['storage_location_code']) : false;


$old_vendorno = isset($_POST['old_vendorno']) ? trim($_POST['old_vendorno']) : false;
$old_plant = isset($_POST['old_plant']) ? trim($_POST['old_plant']) : false;
$old_storage_location_code = isset($_POST['old_storage_location_code']) ? trim($_POST['old_storage_location_code']) : false;
$mdid=$_POST['mdid'];

if($vendorno != $old_vendorno){
    $vmarray['Vendor No'] = $old_vendorno . '::' . $vendorno;
}
if($plant != $old_plant){
    $vmarray['Plant'] = $old_plant . '::' . $plant;
}
if($storage_location_code != $old_storage_location_code){
    $vmarray['Storage Location Code'] = $old_storage_location_code . '::' . $storage_location_code;
}

$json_obj = json_encode($vmarray);
//print_r($json_obj); exit();
    
try{
       if($vendorno != $old_vendorno || $plant != $old_plant || $storage_location_code != $old_storage_location_code ){
         $obj = $dbl->checkVendorMasterDetails($vendorno,$plant,$storage_location_code);
            if(isset($obj) && !empty($obj)){
             $error['addrs_already_exist'] = "vendor master details already exist. Try to create new ship to party";
            }
        }

        if(count($error) == 0){

        $last_inserted_id = $dbl->updateVendorMasterDetails($mdid,$vendorid,$vendorno,$plant,$storage_location_code,$old_vendorno,$old_plant,$old_storage_location_code,$mdid);
        // print_r($last_inserted_id);
        if(trim($last_inserted_id)== -1){
            $error['insert_loc_fail'] = "New vendor master is not created. Try to create it again.";
        }else{
                $success = "successEdit";
                 if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
                $insert_qry = "insert into it_masters_logs set master_id = $vendorid, master_type = 'Vendor Master', updateby_id = $userId, change_data = '$json_obj',createtime = now(),updatetime = now()";
//             echo $insert_qry;
//             exit;
                $result = $db->execInsert($insert_qry);
            }
        }
      }

}catch(Exception $ex){
   $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);    
    $_SESSION['form_errors'] = $error;
    $redirect = 'vendor/master/edit/vendorId='.$vendorid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'vendor/master/';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;

