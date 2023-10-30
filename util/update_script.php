<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";

$db= new DBConn();

//update ean code to null

$objs = $db->fetchAllObjects("select * from it_dealer_items");

if(isset($objs) && !empty($objs)){
    foreach($objs as $obj){
        if(trim($obj->eancode)!=NULL){
            $exist = $db->fetchObject("select * from it_master_items where itemcode='$obj->eancode'");
            
            if(isset($exist) && !empty($exist)){
                
            }else{
                echo "update it_dealer_items set eancode=NULL,master_item_id=NULL,is_vlcc=0,is_NotFound=1,is_master_item=0 where id=$obj->id\n";
                $db->execUpdate("update it_dealer_items set eancode=NULL,master_item_id=NULL,is_vlcc=0,is_NotFound=1,is_master_item=0 where id=$obj->id");
                $cnt++;
            }
        }else if(trim($obj->eancode)==NULL){
            echo "update it_dealer_items set master_item_id=NULL,is_vlcc=0,is_NotFound=1,is_master_item=0 where id=$obj->id\n";
                $db->execUpdate("update it_dealer_items set master_item_id=NULL,is_vlcc=0,is_NotFound=1,is_master_item=0 where id=$obj->id");
                $cnt++;
        }
    }
}


echo "No. of updates: $cnt\n";