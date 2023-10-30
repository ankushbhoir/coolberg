<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$arr = array();
$db = new DBConn();
$objs = $db->fetchAllObjects("select id,invoice_text,master_dealer_id,invoice_no from it_po where status=1");

if(isset($objs) && !empty($objs)){
    foreach($objs as $obj){
        $inv_text = $obj->invoice_text;
        
        if(preg_match('/vlcc\s+personal\s+care\s+ltd/i',$inv_text)==1){
            array_push($arr,$obj->master_dealer_id."<>".$obj->invoice_no);
        }
    }
}

echo "\n\n";
print_r($arr);