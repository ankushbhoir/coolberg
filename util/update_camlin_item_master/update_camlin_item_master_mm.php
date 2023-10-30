<?php
//local
require_once("../../../it_config.php");
require_once "lib/db/DBConn.php";
//server
// require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
// require_once "lib/db/DBConn.php";



$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

$db = new DBConn();
$flag=0;
$totCount=0;
$existCount=0;
$updatedCount=0;
$failedCount=0;

$updated_rows="Following rows are update successfully:\n";
$already_exist_rows="Following rows are already exist in database:\n";
$failed_to_upload="Following rows are failed to update in database:\n";

$handle = fopen("$filename","r");

while($data = fgetcsv($handle,100000,",")){
    
    $mean = trim($data[0]);
    $customer_article = trim($data[0]);
    $camlin_article = trim($data[1]);  
    $master_dealer_id = trim($data[2]);   
   
    //to skip first line
    if(trim($flag)==0){
        $totCount++;
        $flag=1;
        continue;
    }
    $totCount++;
    //check if enrty already exist.
    $selQry = "select id from it_camlin_item_master where camlin_itemcode = '$camlin_article' and cust_itemcode = '$customer_article' and master_dealer_id = '$master_dealer_id'";
    $itemObj = $db->fetchObject($selQry);
    if(isset($itemObj) && $itemObj != null && $itemObj != ""){
        $already_exist_rows .= "Line no. -> $totCount, Customer item code -> $customer_article, Camlin item code -> $camlin_article, master_dealer_id -> $master_dealer_id \n";
        $existCount++;
    }else{

        $insertQry = "insert into it_camlin_item_master set camlin_itemcode = '$camlin_article', cust_itemcode = '$customer_article', master_dealer_id = '$master_dealer_id'";
        $insertedId = $db->execInsert($insertQry);

        if($insertedId > 0){
            $updated_rows .= "Line no. -> $totCount, Customer item code -> $customer_article, Camlin item code -> $camlin_article, master_dealer_id -> $master_dealer_id \n";
            $updatedCount++;
        }else{
            $failed_to_upload .= "Line no. -> $totCount, Customer item code -> $customer_article, Camlin item code -> $camlin_article, master_dealer_id -> $master_dealer_id \n";
            $failedCount++;
        }
    }

}

echo "\n";
echo "Total count : $totCount\n";
echo "Exist count : $existCount\n";
echo "Updated count : $updatedCount\n";
echo "Failed count : $failedCount\n";


echo "$already_exist_rows\n";
echo "$updated_rows\n";
echo "$failed_to_upload\n";



