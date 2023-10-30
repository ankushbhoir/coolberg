<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$objs = $db->fetchAllObjects("select * from it_dealer_items where master_dealer_id=4");

foreach ($objs as $obj){
    $itemcode = $db->safe($obj->eancode);
    $ob = $db->fetchObject("select * from it_master_items where itemcode=$itemcode");
    if(isset($ob) && !empty($ob) && $ob!=NULL){
        $db->execUpdate("update it_dealer_items set master_item_id=$ob->id,createtime=now(),updatetime=now() where id=$obj->id");
    }
}
