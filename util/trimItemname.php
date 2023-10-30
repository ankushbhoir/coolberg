<?php

//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$objs = $db->fetchAllObjects("select * from it_dealer_items");
print "/n";
foreach($objs as $obj){
    for($i=0;$i<10;$i++){
        $db->execUpdate("update it_dealer_items set itemname=replace(itemname,'  ',' ') where id=$obj->id");
        $db->execUpdate("update it_dealer_items set itemname=replace(itemname,'\n',' ') where id=$obj->id");
        $db->execUpdate("update it_dealer_items set itemname=replace(itemname,'\t',' ') where id=$obj->id");
    }
}

echo "Done";
