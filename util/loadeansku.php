<?php

require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once 'lib/core/strutil.php';
require_once "lib/core/Constants.php";
$db = new DBConn();

$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

//$master_dealer_id = isset($argv[2]) ? $argv[2] : FALSE;
//if(!isset($master_dealer_id) || $master_dealer_id==''){
//    echo "Please put master dealer id\n";
//    return;
//}

$handle = fopen("$filename","r");
while($data = fgetcsv($handle,100000,",")){
    $sku = $db->safe(trim($data[0]));
    $ean = $db->safe(trim($data[1]));
     $Cat = $db->safe(trim($data[2]));
  // $dealer_id = $db->safe(trim($data[2]));
     $new_ean = str_replace(' ', '', $ean);

// INSERT INTO `it_ean_sku_mapping` (`id`, `ean`, `sku`, `createtime`, `updatetime`) VALUES (NULL, '8901365202370', '20237', NULL, CURRENT_TIMESTAMP);
      $get_info="select * from it_ean_sku_mapping where ean=$sku and sku=$ean";
     $issuesobj= $db->fetchAllObjects($get_info);
  
    if(isset($issuesobj) && !empty($issuesobj)){

      foreach ($issuesobj as $value) {
      	echo "update it_ean_sku_mapping set catetory=".$Cat." where id=$value->id";
      	echo "\n";
       $db->execUpdate("update it_ean_sku_mapping set catetory=".$Cat." where id=$value->id");
      }
}
else{

	echo $insertQry= "INSERT INTO `it_ean_sku_mapping` (`id`, `ean`, `sku`,`category`, `createtime`, `updatetime`) VALUES (NULL, $sku, $new_ean,".$Cat.", NULL, CURRENT_TIMESTAMP)";
	echo "\n";
	  $insertedId = $db->execInsert($insertQry);
}

}

echo "Done";