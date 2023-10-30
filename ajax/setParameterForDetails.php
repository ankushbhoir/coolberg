<?php 
require_once "../../it_config.php";
require_once "lib/db/DBLogic.php";
require_once("session_check.php");

$dbl = new DBLogic();


$key = isset($_GET['key']) ? ($_GET['key']) : false;
$value = isset($_GET['value']) ? ($_GET['value']) : false;


// print_r($_GET);
// return;


if(!$key & !$value){
	error("Please check parameters.");
	exit;
}else if ($key == 'customer'){
	$sqlObj = $dbl->getMasterDealerIdFromDealerName($value);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Data not found in database.");
	}else{
		$parameter = "/chain=".$sqlObj->id;
		success($parameter);
		exit;
	}
}else if ($key == 'category'){
	$sqlObj = $dbl->getMasterCategoryIdFromCategoryName($value);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Data not found in database.");
	}else{
		$parameter = "/category=".$sqlObj->id;
		success($parameter);
		exit;
	}
}else if ($key == 'product'){
	$value = str_replace(' and ', ' & ', $value);
	$sqlObj = $dbl->getMasterProductIdFromProductName($value);
	// print_r($value);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Data not found in database.");
	}else{
		$parameter = "/product=".$sqlObj->id;
		success($parameter);
		exit;
	}
}else if ($key == 'region'){
	$sqlObj = $dbl->getMasterRegionIdFromRegionName($value);
	// print_r($sqlObj);
	if($sqlObj == null || $sqlObj == "" || !isset($sqlObj)){
		error("Data not found in database.");
	}else{
		$parameter = "/region=".$sqlObj->id;
		success($parameter);
		exit;
	}
}


function error($msg) {
    print json_encode(array(
            "error" => "1",
            "msg" => $msg
            ));
}

function success($data) {
    print json_encode(array(
            "error" => "0",
            "parameters" => $data
            ));
}


?>


