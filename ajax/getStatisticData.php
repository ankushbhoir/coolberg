<?php 
require_once "../../it_config.php";
require_once "lib/db/DBLogic.php";
require_once("session_check.php");

$dbl = new DBLogic();

$fromdate = isset($_GET['fromdate']) ? ($_GET['fromdate']) : false;
$todate = isset($_GET['todate']) ? ($_GET['todate']) : false;
$StatisticsReason = isset($_GET['StatisticsReason']) ? ($_GET['StatisticsReason']) : false;
// print_r($_GET);
$thisMonth = "";
if(!$fromdate || !$todate){
	//current month's date range
	$fromdate = date('Y-m-01');
	$todate = date('Y-m-d');
}else{
	$fromdate = date("Y-m-d", strtotime($fromdate));
	$todate = date("Y-m-d", strtotime($todate));
}

if(!$StatisticsReason){
	error("Please add StatisticsReason.");
	exit;
}else if ($StatisticsReason == StatisticsReason::TOP3CUSTOMERS){
	$sqlObj = $dbl->getTopThreeSalesCustomers($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Top 3 customers record not found for this date range.");
	}else{
		$top3saleCust = (array) $sqlObj;
		success($top3saleCust);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::TOP3CATEGORY){
	$sqlObj = $dbl->getTopThreeSalesCategory($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Top 3 category record not found for this date range.");
	}else{
		$top3saleCategory = (array) $sqlObj;
		success($top3saleCategory);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::TOP3PRODUCTS){
	$sqlObj = $dbl->getTopThreeSalesProducts($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Top 3 products record not found for this date range.");
	}else{
		$top3saleCategory = (array) $sqlObj;
		success($top3saleCategory);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::TOP3REGIONS){
	$sqlObj = $dbl->getTopThreeSalesRegions($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Top 3 regions record not found for this date range.");
	}else{
		$top3saleCategory = (array) $sqlObj;
		success($top3saleCategory);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::BOTTOM3CUSTOMERS){
	$sqlObj = $dbl->getBottomThreeSalesCustomers($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Bottom 3 customers record not found for this date range.");
	}else{
		$top3saleCust = (array) $sqlObj;
		success($top3saleCust);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::BOTTOM3CATEGORY){
	$sqlObj = $dbl->getBottomThreeSalesCategory($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Bottom 3 category record not found for this date range.");
	}else{
		$top3saleCategory = (array) $sqlObj;
		success($top3saleCategory);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::BOTTOM3PRODUCTS){
	$sqlObj = $dbl->getBottomThreeSalesProducts($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Bottom 3 products record not found for this date range.");
	}else{
		$top3saleCategory = (array) $sqlObj;
		success($top3saleCategory);
		exit;
	}
}else if ($StatisticsReason == StatisticsReason::BOTTOM3REGIONS){
	$sqlObj = $dbl->getBottomThreeSalesRegions($fromdate,$todate);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Bottom 3 regions record not found for this date range.");
	}else{
		$top3saleCategory = (array) $sqlObj;
		success($top3saleCategory);
		exit;
	}
}


function error($msg) {
    print json_encode(array(
            "error" => "1",
            "result" => $msg
            ));
}

function success($data) {
    print json_encode(array(
            "error" => "0",
            "result" => $data
            ));
}

// function isJson($string) {
//  	return (json_last_error() == JSON_ERROR_NONE);
// }

?>


