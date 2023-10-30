<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$objs = $db->fetchAllObjects("select id,filename,createtime from it_receivedpos where master_dealer_id=-1");

foreach($objs as $obj){
    $filename = $obj->filename;
    
    include_once "/home/vlcc/public_html/vlcc_dt/home/Parsers/";
    
}
