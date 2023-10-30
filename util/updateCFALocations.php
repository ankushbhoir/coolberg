<?php

//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";

$db = new DBConn();
$cnt = 0;
$cnt_insert = 0;
$cnt_update = 0;
$filename = $argv[1];
$handle = fopen($filename,"r");


while($data = fgetcsv($handle,100000,",")){    
    $city = $db->safe(trim($data[0]));        
    $cfa_location = $db->safe(trim($data[2]));    
    $zone = $db->safe(trim($data[3]));
    
    if(trim($data[2])=='CFA Location'){
        continue;
    }
    
    $obj = $db->fetchObject("select * from it_cfa_location where city=$city and active=1");
    $cnt++;
    if(!isset($obj) && empty($obj)){
        $db->execInsert("insert into it_cfa_location set city=$city,cfa_location=$cfa_location,zone=$zone,createtime=now()");
        $cnt_insert++;
    }else{
        $db->execUpdate("update it_cfa_location set cfa_location=$cfa_location,zone=$zone,updatetime=now() where id=$obj->id");
        $cnt_update++;
    }
}

echo "No. of entries: $cnt\n";
echo "New cities inserted: $cnt_insert\n";
echo "New cities updated: $cnt_update\n";


