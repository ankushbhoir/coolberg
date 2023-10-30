<?php
//server path
// require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
// require_once "lib/db/DBConn.php";

//local path
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";

$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

$csvAsArray = array_map('str_getcsv', file($filename));

try {

    $updatedCount = 0;
    $eanNotFound = 0;
    $itemIdIsNull = 0;
    $articleChainCombNotMatch = 0;

    $db = new DBConn();
    $db->getConnection();
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

                $selectQry = "SELECT master_item_id FROM it_dealer_items WHERE master_dealer_id = $chainId AND itemcode = '$articleId'";
                $dealerObj = $db->fetchObject($selectQry);
                if(isset($dealerObj) && $dealerObj != null && $dealerObj != ""){
                    if(isset($dealerObj->master_item_id) && $dealerObj->master_item_id != null && $dealerObj->master_item_id != ""){
                            $master_item_id = $dealerObj->master_item_id;
                            $selQry = "select id, itemname, product_code, mrp, category_id from it_master_items where id = '$master_item_id'";
                            $itemObj = $db->fetchObject($selQry);
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
                            $updatedCount++;
                    }else{
                        $insertQry2 = "insert into it_article_notfound set chain_id = '$chainId', article_no = '$articleId', article_desc = '$articleDesc', FG_code = '$FGcode', mrp = '$mrp', vlcc_category = '$category', intouch_comment = 'master_item id is null in it_dealer_items' ";
                        $insertedId2 = $db->execInsert($insertQry2);
                        $itemIdIsNull++;
                    }
                }else{
                    $insertQry = "insert into it_article_notfound set chain_id = '$chainId', article_no = '$articleId', article_desc = '$articleDesc', FG_code = '$FGcode', mrp = '$mrp', vlcc_category = '$category', intouch_comment = 'combination of article no. and chain not found in it_dealer_items' ";
                    $insertedId = $db->execInsert($insertQry);
                    $articleChainCombNotMatch++;
                }
            }else{
                //if row is having weird data after convert .xlsx to .csv then this code will call
            }
            
        }
    $db->closeConnection();

    print_r("\nArticle no. and Chain combination not found in it_dealer_items: ".$articleChainCombNotMatch);
    print_r("\nmaster_item_id is null in it_dealer_items: ".$itemIdIsNull);
    print_r("\nUpdated Rows: ".$updatedCount);
    print_r("\n\n\n");
} catch (Exception $ex) {
    print_r($ex->message);
}
