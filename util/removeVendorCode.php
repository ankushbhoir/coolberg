<?php

//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$objs = $db->fetchAllObjects("select * from it_distributors where code like '%PO%'");

foreach($objs as $obj){
    $vendor_code = $obj->code;
    if(preg_match("/(\d\S*)\s+PO\s+No/",$vendor_code,$matches)){
        $vendorCode = $db->safe(trim($matches[1]));
        $db->execUpdate("update it_distributors set code=$vendorCode where id=$obj->id");
    }
}

