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

$mid = isset($_POST['mid']) ? trim($_POST['mid']) : false;
$master_name = isset($_POST['master_name']) ? trim($_POST['master_name']) : false;
$emails = isset($_POST['emails']) ? trim($_POST['emails']) : false;



$old_emails = isset($_POST['old_emails']) ? trim($_POST['old_emails']) : false;


try{
       if($emails != $old_emails ){
        

        if(count($error) == 0){

        $last_inserted_id = $dbl->updatemissingmasteremail($mid,$master_name,$emails,$old_emails);
        // print_r($last_inserted_id);
        if(trim($last_inserted_id)== -1){
            $error['insert_loc_fail'] = "New missing master email is not created. Try to create it again.";
        }else{
                $success = "successEdit";
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
    $redirect = 'missing/master/email/edit/shippingId='.$shippingId;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'missing/master/email';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;

