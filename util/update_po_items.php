<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";

$db= new DBConn();

//$master_dealer_id = $argv[1];

/*$objs = $db->fetchAllObjects("select id,itemcode,is_master_item,is_vlcc,master_item_id,is_NotFound from it_dealer_items where master_dealer_id=$master_dealer_id and master_item_id is not NULL");

if(isset($objs) && !empty($objs)){
    foreach($objs as $obj){
        $master_item_id = $obj->master_item_id;
//        $db->execUpdate("update it_dealer_items set is_vlcc=1,is_NotFound=0,is_master_item=1 where id=$obj->id");
        
        $check = $db->fetchAllObjects("select id,master_item_id,dealer_item_id from it_po_items where dealer_item_id=$obj->id");
        
        if(isset($check) && !empty($check)){
            foreach($check as $obs){
                $db->execUpdate("update it_po_items set master_item_id=$master_item_id where id=$obs->id");
            }
        }
                
    }
}*/


$objs = $db->fetchAllObjects("select id,master_item_id,dealer_item_id from it_po_items where master_item_id is NULL");

if(isset($objs) && !empty($objs)){
    foreach($objs as $obj){
        $dealer_item_id = $obj->dealer_item_id;
//        $db->execUpdate("update it_dealer_items set is_vlcc=1,is_NotFound=0,is_master_item=1 where id=$obj->id");
        
        $obs = $db->fetchObject("select * from it_dealer_items where id=$dealer_item_id");
        
        if(isset($obs) && !empty($obs)){
            $master_item_id = $obs->master_item_id;
           // foreach($check as $obs){
                $db->execUpdate("update it_po_items set master_item_id=$master_item_id where id=$obj->id");
           // }
        }
                
    }
}

