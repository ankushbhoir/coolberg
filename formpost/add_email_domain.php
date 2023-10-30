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
$domain   = isset($_POST['domain']) ? trim($_POST['domain']) : false;


//Remove Line Breaks From Textarea

try{

    if(isset($domain) && trim($domain)== ""){
        $error['select_chain'] = "Please add domain from the list.";
    }
  
        
    if(count($error) == 0){
            $obj = $dbl->checkEmailDomain($domain);
            // print_r($obj);
            // return;
            if(isset($obj) && !empty($obj)){
                $error['domain_exist'] = "$domain already exist. Try to create new domain";
            }else{
                

                $last_inserted_id = $dbl->insertEmailDomain($domain);
                if(trim($last_inserted_id)<=0){
                    $error['insert_loc_fail'] = "New Email is not created. Try to create it again.";
                }else{
                        $success = "success";
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
    $redirect = 'email/domain/add';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'email/domain';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;