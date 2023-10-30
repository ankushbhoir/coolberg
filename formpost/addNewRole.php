<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/core/Constants.php";
require_once 'lib/user/clsUser.php';
require_once "lib/db/DBLogic.php";

$errors = array();
$user = getCurrStore();
$by_user = getCurrStoreId();
$userpage = new clsUser();
extract($_GET);
$_SESSION['form_id'] = $form_id;

$dbl = new DBLogic();
$errors = array();
$result = 0;
$success = "";
try{   
    
    $result = $dbl->checkRoleIfExist($new_role);
    if($result > 0){
        $errors['duplicate'] =  "Role already exist.";
    }else{
        $id = $dbl->insertNewRole($new_role);
        if($id > 0){
            $success = "New role added successfully ";
        }else{
            $errors['failed'] =  "Failed to add new role";
        } 
    }
    
}catch(Exception $xcp){
   $errors['xcp'] = $xcp->getMessage();
}

 
if (count($errors)>0) {
        unset($_SESSION['form_success']);       
        $_SESSION['form_errors'] = $errors;
  } else {
        unset($_SESSION['form_errors']);
        $_SESSION['form_success'] = $success;        
  }
  
  header("Location: ".DEF_SITEURL."assign/role/permissions");
  exit;



