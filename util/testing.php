<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once("lib/db/DBConn.php");

$db = new DBConn();

$files = scandir(DEF_READ_PATH);

$num = count($files);
print_r($files);

//echo "No. of files in receivedpo: $num\n";


$cnt = count(scandir(DEF_READ_PATH));


if(count(scandir(DEF_READ_PATH)) > 2){
    echo "No. of files in receivedpo: $cnt\n";
}else{
    echo "NOTICE::POs not found in receivedPOs folder\n";
}
