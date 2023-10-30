<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once ("lib/db/DBLogic.php");
require_once 'session_check.php';
require_once 'lib/user/clsUser.php';

$errors = array();
$success = "";
extract($_POST);
$form_id = $_POST['form_id'];
$_SESSION['form_id'] = $form_id;

$filename = $_FILES['upload']['name'];
$tmpName = $_FILES['upload']['tmp_name'];
$form_id = $_POST['form_id'];

$uploads_dir = '../uploads/';
$filenamecopy = date('Y-m-d_H:i:s');
$target_loc = $uploads_dir . $filenamecopy;
move_uploaded_file($tmpName, $target_loc);

$csvAsArray = array_map('str_getcsv', file($uploads_dir . $filenamecopy));



$extention = explode(".", $filename);
$ext = end($extention);
if ($ext != "csv") {
    $errors["name"] = "Please upload .csv file only";
}



try {

    $count = 0;
    $db = new DBConn();
    $db->getConnection();
    if (count($errors) == 0) {
        foreach ($csvAsArray as $row) {
            if(count($row) >= 7){
                $chainId = trim($row[0]);
                $articleId = trim($row[1]);
                $articleDesc = trim($row[3]);
                $FGcode = trim($row[4]);
                $mrp = trim($row[5]);
                $category = trim($row[6]);

                $categoryId = 0;
                if($category != "" && $category != null){
                    $selCat = "select id from it_category where category = '$category'";
                    $catObj = $db->fetchObject($selCat);
                    if(isset($catObj) && $catObj != null && $catObj != ""){
                        $categoryId = $catObj->id;
                    }
                }

                $selectQry = "SELECT eancode FROM it_dealer_items WHERE master_dealer_id = $chainId AND itemcode = '$articleId'";
                $dealerObj = $db->fetchObject($selectQry);
                if(isset($dealerObj) && $dealerObj != null && $dealerObj != ""){
                    if(isset($dealerObj->eancode) && $dealerObj->eancode != null && $dealerObj->eancode != ""){
                        $eanCode = $dealerObj->eancode;
                        $selQry = "select id, itemname, product_code, mrp, category_id from it_master_items where itemcode = '$eanCode'";
                        $itemObj = $db->fetchObject($selQry);
                        if(isset($itemObj) && $itemObj != null && $itemObj != ""){
                            if($itemObj->itemname != $articleDesc && $articleDesc != ""){

                                $updateItem = "update it_master_items set itemname = '$articleDesc' where id = $itemObj->id";
                                $updateItemId = $db->execUpdate($updateItem);
                            }
                            if($itemObj->product_code != $FGcode && $FGcode != ""){
                                $updateFGCode = "update it_master_items set product_code = '$FGcode' where id = $itemObj->id";
                                $updateFGCodeId = $db->execUpdate($updateFGCode);
                            }
                            if($itemObj->mrp != $mrp && $mrp != ""){
                                $updateMRP = "update it_master_items set mrp = '$mrp' where id = $itemObj->id";
                                $updateMRPId = $db->execUpdate($updateMRP);
                            }
                            if(isset($catObj->id) && $catObj->id != null && $catObj->id != "" && $categoryId != 0){
                                if($categoryId != $itemObj->category_id){
                                    $updateCat = "update it_master_items set category_id = '$categoryId' where id = $itemObj->id";
                                    $updateCatId = $db->execUpdate($updateCat);
                                }
                            }
                            
                        }else{
                            $insertQry3 = "insert into it_article_notfound set chain_id = '$chainId', article_no = '$articleId', article_desc = '$articleDesc', FG_code = '$FGcode', mrp = '$mrp', vlcc_category = '$category', intouch_comment = 'EAN is not found in it_master_items' ";
                            $insertedId3 = $db->execInsert($insertQry3);
                        }

                    }else{
                        $insertQry2 = "insert into it_article_notfound set chain_id = '$chainId', article_no = '$articleId', article_desc = '$articleDesc', FG_code = '$FGcode', mrp = '$mrp', vlcc_category = '$category', intouch_comment = 'EAN is null in it_dealer_items' ";
                        $insertedId2 = $db->execInsert($insertQry2);
                    }
                }else{
                    $insertQry = "insert into it_article_notfound set chain_id = '$chainId', article_no = '$articleId', article_desc = '$articleDesc', FG_code = '$FGcode', mrp = '$mrp', vlcc_category = '$category', intouch_comment = 'combination of article no. and chain not found in it_dealer_items' ";
                    $insertedId = $db->execInsert($insertQry);
                }
            }else{
                //if row is having weird data after convert .xlsx to .csv then this code will call
            }
            
        }
    }
    $db->closeConnection();
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}

//print_r($error);
if (count($errors) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $errors;
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
