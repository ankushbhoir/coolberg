<?php
//server path
// require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
// require_once "lib/db/DBConn.php";

//local path
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";



//this code is written by Mayur to udate store code and customer given by VLCC (09-01-2020)



$filename = isset($argv[1]) ? $argv[1] : FALSE;
if(!isset($filename)){
    echo "Please add file\n";
    return;
}

$csvAsArray = array_map('str_getcsv', file($filename));

try {

    $rowCount = 0;
    // $eanNotFound = 0;
    // $itemIdIsNull = 0;
    // $articleChainCombNotMatch = 0;

    $db = new DBConn();
    $db->getConnection();
        foreach ($csvAsArray as $row) {
            $chain_name = $row[0];
            $master_dealer_id = $row[1];
            $cust_code = $row[2];
            $store_code = $row[3];

            if($row[0] == 'Chain name'){
                continue;
            }

            if($master_dealer_id != '' && isset($master_dealer_id)){
                if($cust_code != '' && isset($cust_code)){
                    $storeCodeQry = "select sh.id, customer_code,dc_city from it_shipping_address sh , it_business_unit bu where replace(bu.bu_identifier,' ','') = replace(sh.shipping_address ,' ','') and sh.master_dealer_id = $master_dealer_id and bu.code = '$store_code'";
                    


                }else{
                    print_r("\nCustomer code is null at row no. ".$rowCount);
                }
            }else{
                print_r("\nmaster_item_id is null at row no. ".$rowCount);
            }

            print_r($row);
            print_r("\n");
            $rowCount++;
        }
    $db->closeConnection();

    // print_r("\nArticle no. and Chain combination not found in it_dealer_items: ".$articleChainCombNotMatch);
    // print_r("\nmaster_item_id is null in it_dealer_items: ".$itemIdIsNull);
    print_r("\nTotal Rows: ".$rowCount);
    print_r("\n\n\n");
} catch (Exception $ex) {
    print_r($ex->message);
}
