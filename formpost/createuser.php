<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $utypesel = isset($utypesel) && trim($utypesel) != "" ? intval($utypesel) : false;
    if(!$utypesel){ $error['missing_utype'] = "Select User Type"; }

    $uname = isset($name) && trim($name) != "" ? trim($name) : false;
    if(!$uname){ $error['missing_uname'] = "Enter Name"; }

    $username = isset($username) && trim($username) != "" ? trim($username) : false;
    if(!$username){ $error['missing_username'] = "Enter Username"; }

    $email = isset($email) && trim($email) != "" ? trim($email) : false;
    if(!$email){ $error['missing_emailid'] = "Enter Email Id"; }

    $password = isset($password) && trim($password) != "" ? trim($password) : false;
//    $specialCharacters = "!@#$%^&*()-_=+[]{}|;:'\",.<>?";
    
    if (!$password) {
        $error['missing_password'] = "Enter Password";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*()_+[\]{}|;:'\",.<>?])[A-Za-z\d@#$%^&*()_+[\]{}|;:'\",.<>?]{8,}$/", $password)) {
        $error['invalid_password'] = "Password must be at least 8 characters long and contain at least 1 lowercase letter, 1 uppercase letter, 1 digit, and at least 1 special character.";
    }
    $confirmpassword = isset($confirmPassword) && trim($confirmPassword) != "" ? trim($confirmPassword) : false;
    if(!$confirmpassword){ $error['missing_confirmpassword'] = "Enter Confirm Password"; }
    
    if(isset($password) && isset($confirmpassword)){
        if($password != $confirmpassword){
            if(!$confirmpassword){ $error['missing_confirmpassword'] = "Password and Confirm Password must be same"; }
        }
    }
    
    $phoneno = isset($phone) && trim($phone) != "" ? trim($phone) : false;
    if(!$phoneno){ $error['missing_phoneno'] = "Enter Phone No"; }
    
    if($username){
        
        $obj1 = $dbl->getUserByUsername($username);
        if (isset($obj1) && !empty($obj1)) {
                $error['username_already_exist'] = "$username already exist. Try to create new user";
        }
    }
    if($uname){
        $obj2 = $dbl->getUserByName($uname);
            if (isset($obj2) && !empty($obj2)) {
                $error['name_already_exist'] = "$name already exist. Try to create new user";
        }
    }
//       echo 'commentline1';
    if(count($error) == 0){
//        print_r('error count = 0');
       $user_id = $dbl->insertUser($utypesel,$uname,$username,$email,$password,$phoneno,$userid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error); exit();
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'user/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "users";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
