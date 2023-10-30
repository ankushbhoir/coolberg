<?php
require_once("../../it_config.php");
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/Constants.php';

$error = array();
$success = "";
//$skuarray = "";
extract($_POST);
$user = getCurrStore();
$userId = $user->id;
$dbl = new DBLogic();
$db = new DBConn();
// print_r($_POST);
// return;
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;

$skuid = isset($_POST['skuid']) ? trim($_POST['skuid']) : false;
$sku = isset($_POST['sku']) ? trim($_POST['sku']) : false;
$ean = isset($_POST['ean']) ? trim($_POST['ean']) : false;
$category = isset($_POST['category']) ? trim($_POST['category']) : false;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : false;
$mrp = isset($_POST['mrp']) ? trim($_POST['mrp']) : false;
$gst = isset($_POST['gst']) ? trim($_POST['gst']) : false;
$inner_size = isset($_POST['inner_size']) ? trim($_POST['inner_size']) : false;
//$case_size = isset($_POST['case_size']) ? trim($_POST['case_size']) : false;
$outer_size = isset($_POST['outer_size']) ? trim($_POST['outer_size']) : false;
$purchase_rate_gst = isset($_POST['purchase_rate_gst']) ? trim($_POST['purchase_rate_gst']) : false;
$moq = isset($_POST['moq']) ? trim($_POST['moq']) : false;

$old_sku = isset($_POST['old_sku']) ? trim($_POST['old_sku']) : false;
$old_ean = isset($_POST['old_ean']) ? trim($_POST['old_ean']) : false;
$old_category = isset($_POST['old_category']) ? trim($_POST['old_category']) : false;
$old_product_name = isset($_POST['old_product_name']) ? trim($_POST['old_product_name']) : false;
$old_mrp = isset($_POST['old_mrp']) ? trim($_POST['old_mrp']) : false;
$old_gst = isset($_POST['old_gst']) ? trim($_POST['old_gst']) : false;
$old_inner_size = isset($_POST['old_inner_size']) ? trim($_POST['old_inner_size']) : false;
//$old_case_size = isset($_POST['old_case_size']) ? trim($_POST['old_case_size']) : false;
$old_outer_size = isset($_POST['old_outer_size']) ? trim($_POST['old_outer_size']) : false;
$old_purchase_rate_gst = isset($_POST['old_purchase_rate_gst']) ? trim($_POST['old_purchase_rate_gst']) : false;
$old_moq = isset($_POST['old_moq']) ? trim($_POST['old_moq']) : false;

if ($sku != $old_sku) {
    $skuarray['SKU'] = $old_sku . '::' . $sku;
}
if ($ean != $old_ean) {
    $skuarray['EAN'] = $old_ean . '::' . $ean;
}
if ($category != $old_category) {
    $skuarray['Category'] = $old_category . '::' . $category;
}
if ($product_name != $old_product_name) {
    $skuarray['Product Name'] = $old_product_name . '::' . $product_name;
}
if ($mrp != $old_mrp) {
    $skuarray['MRP'] = $old_mrp . '::' . $mrp;
}
if ($gst != $old_gst) {
    $skuarray['GST'] = $old_gst . '::' . $gst;
}
if ($inner_size != $old_inner_size) {
    $skuarray['Inner Size'] = $old_inner_size . '::' . $inner_size;
}
if ($outer_size != $old_outer_size) {
    $skuarray['Outer Size'] = $old_outer_size . '::' . $outer_size;
}
if ($purchase_rate_gst != $old_purchase_rate_gst) {
    $skuarray['Purchase Rate GST'] = $old_purchase_rate_gst . '::' . $purchase_rate_gst;
}
if ($moq != $old_moq) {
    $skuarray['MOQ'] = $old_moq . '::' . $moq;
}

$json_obj = json_encode($skuarray);
//print_r($json_obj); exit();
try{
       if($sku != $old_sku || $ean != $old_ean || $category != $old_category || $product_name != $old_product_name || $mrp != $old_mrp || $gst != $old_gst || $inner_size != $old_inner_size || $outer_size != $old_outer_size || $purchase_rate_gst != $old_purchase_rate_gst || $moq != $old_moq ){
         $obj = $dbl->checkSkuMasterDetails($sku,$ean,$category,$product_name,$mrp,$gst,$inner_size,$outer_size,$purchase_rate_gst,$moq);
            if(isset($obj) && !empty($obj)){
             $error['addrs_already_exist'] = "sku master details already exist. Try to create SKU Master";
            }
        }

        if(count($error) == 0){

        $last_inserted_id = $dbl->updateSkuMasterDetails($skuid,$sku,$ean,$category,$product_name,$mrp,$gst,$inner_size,$outer_size,$purchase_rate_gst,$moq,$old_sku,$old_ean,$old_category,$old_product_name,$old_mrp,$old_gst,$old_inner_size,$old_outer_size,$old_purchase_rate_gst,$old_moq);
        // print_r($last_inserted_id);
        if(trim($last_inserted_id)== -1){
            $error['insert_loc_fail'] = "New sku master is not created. Try to create it again.";
        }else{
                $success = "successEdit";
                if (isset($json_obj) && $json_obj !== null && $json_obj !== "null" && !empty($json_obj)) {
                $insert_qry = "insert into it_masters_logs set master_id = $skuid,master_type = 'SKU Master', updateby_id = $userId, change_data = '$json_obj',createtime = now(),updatetime = now()";
//             echo $insert_qry;
//             exit;
                $result = $db->execInsert($insert_qry);
            }
        }
      }
//exit();
}catch(Exception $ex){
   $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);    
    $_SESSION['form_errors'] = $error;
    $redirect = 'sku/master/edit/sku_Id='.$skuid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'sku/master/';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;

