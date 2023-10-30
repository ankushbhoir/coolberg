<?php
require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();

$file = $argv[1];
$updated=0;
$cnt=0;
$handle = fopen($file,"r");
while (($data = fgetcsv($handle, 1048576, ",")) !== FALSE) {
    //$id = $data[0];
    $shipping_addr = $data[2];   
    $cust_code = $data[3];    
  
    $cnt++;
    $objs = $db->fetchAllObjects("select * from it_shipping_address where dc_address like '%$shipping_addr%'");
    
    if(isset($objs) && !empty($objs) && trim($cust_code)!=""){  
        foreach($objs as $obj){
            $cust_code_db = $db->safe($cust_code);
            $db->execUpdate("update it_shipping_address set customer_code=$cust_code_db,updatetime=now() where id=$obj->id");
            $updated++;
        }
    }else{
        echo "Shipping addr is: $shipping_addr\n";
    }

}

echo "No. of rows: $cnt\n";
echo "No. of updates: $updated\n";