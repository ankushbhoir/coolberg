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
$productId   = isset($_POST['productId']) ? trim($_POST['productId']) : false;
$itemId   = isset($_POST['itemId']) ? trim($_POST['itemId']) : false;


$chain_article = isset($_POST['chain_article']) ? trim($_POST['chain_article']) : false;
$prod_desc = isset($_POST['prod_desc']) ? trim($_POST['prod_desc']) : false;
$mrp = isset($_POST['mrp']) ? trim($_POST['mrp']) : false;
$ean = isset($_POST['ean']) ? trim($_POST['ean']) : false;
$fg_code = isset($_POST['fg_code']) ? trim($_POST['fg_code']) : false;


$old_chain_article = isset($_POST['old_chain_article']) ? trim($_POST['old_chain_article']) : false;
$old_prod_desc = isset($_POST['old_prod_desc']) ? trim($_POST['old_prod_desc']) : false;
$old_mrp = isset($_POST['old_mrp']) ? trim($_POST['old_mrp']) : false;
$old_ean = isset($_POST['old_ean']) ? trim($_POST['old_ean']) : false;
$old_fg_code = isset($_POST['old_fg_code']) ? trim($_POST['old_fg_code']) : false;


try{
        
    if(count($error) == 0){

        $update_result = $dbl->updateMasterItem($itemId,$prod_desc,$mrp,$ean,$fg_code,$old_prod_desc,$old_mrp,$old_ean,$old_fg_code);
        // print_r($update_result);
        // return;
        if(trim($update_result)== -1){
            $error['update_fail'] = "Update failed. Please try again.";
        }else{
            $update_status = $dbl->updateDealerItems($productId,$chain_article,$old_chain_article);
            if(trim($update_status)== -1){
                $error['update_not_done'] = "Dependancy could not be set. Data is not updated successfully.";
            }else{
                $result = $dbl->insertIntoProductEditDiary($productId,$itemId,$chain_article,$prod_desc,$mrp,$ean,$fg_code,$old_chain_article,$old_prod_desc,$old_mrp,$old_ean,$old_fg_code);
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
    $redirect = 'chain/wise/product/edit/productid='.$productId;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'chain/Wise/products';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;