<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once ("lib/db/DBLogic.php");
require_once 'session_check.php';
require_once 'lib/user/clsUser.php';

$error = array();
extract($_POST);
$form_id = $_POST['form_id'];
$_SESSION['form_id'] = $form_id;

$total = count($_FILES['upload']['name']);

try {
    $count = 0;
    // Loop through each file
    for ($i = 0; $i < $total; $i++) {

        //Get the temp file path
        $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

        //Make sure we have a file path
        if ($tmpFilePath != "") {
            //Setup our new file path
            $newFilePath = "../Parsers/receivedPO/" . $_FILES['upload']['name'][$i];
            //Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $count++;
            }
        }
    }
    
    if ($count > 0) {
        $success = "$count files uploaded successfully.";
    } else {
        $error['missing_po'] = "File not found";
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'upload/po';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
//    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "upload/po";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
