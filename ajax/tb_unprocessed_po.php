<?php

include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
//if (!$currStore || $currStore->usertype != UserType::Admin) { print "Unauthorized Access !!! ".print_r($currStore,true); return; }
$dateRange = isset($_GET['selDateRange']) ? $_GET['selDateRange'] : false;
$aColumns = array('checkBox', 'po_filenames', 'from_email', 'status', 'datetime','action');
$sColumns = array('po_filenames', 'from_email', 'datetime');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

/*
 * Paging
 */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $sLimit = " LIMIT " . $db->getConnection()->real_escape_string($_GET['iDisplayStart']) . ", " .
            $db->getConnection()->real_escape_string($_GET['iDisplayLength']);
}


/*
 * Ordering
 */
//$sOrder = "m.name, dc_name";
$sOrder = "";
if (isset($_GET['iSortCol_0'])) {
    $sOrder = " ORDER BY  ";
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
			 	" . $db->getConnection()->real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == " ORDER BY ") {
        $sOrder = "";
    }
}


/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */

$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
	$sWhere = "WHERE (";
	for ( $i=0 ; $i<count($sColumns) ; $i++ )
	{
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string( $_GET['sSearch'] )."%' OR ";
	}
	$sWhere = substr_replace( $sWhere, "", -3 );
	$sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($sColumns) ; $i++ )
{
	if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && isset($_GET['sSearch_'.$i]) && $_GET['sSearch_'.$i] != '' )
	{
		if ( $sWhere == "" )
		{
			$sWhere = "WHERE ";
		}
		else
		{
			$sWhere .= " AND ";
		}
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string($_GET['sSearch_'.$i])."%' ";
	}
}

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
$addWhere = "";
if($dateRange){
    $dtrng= explode ("-", $dateRange);
    $frmdt=$dtrng[0];
    $tdt=$dtrng[1];

    $frmdt = str_replace("/", "-", $frmdt);
    $tdt = str_replace("/", "-", $tdt);
    $frmdt1 =explode ("-", $frmdt);
    $tdt1 =explode ("-", $tdt);
    $FromDate=trim($frmdt1[2])."-".trim($frmdt1[1])."-".trim($frmdt1[0])." 00:00:00";
    $ToDate= trim($tdt1[2])."-".trim($tdt1[1])."-".trim($tdt1[0])." 23:59:59";
    $addWhere = "and datetime >='".$FromDate."' and datetime <='".$ToDate."'";
}else{
     $date = date('Y-m-d');
    $addWhere = "and datetime like '".$date."%'";
    
}

$sWhere .= "status = 0 $addWhere";




/*
 * SQL queries
 * Get data to display
 */
//status = 0 means unprocessed PO
  $sQuery = "
	select SQL_CALC_FOUND_ROWS * from it_po_details
	$sWhere
        $sOrder
	$sLimit
";

//error_log("\nMSL query: ".$sQuery."\n",3,"unProcPO.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj1 = $db->fetchObject($sQuery);
$iFilteredTotal = $obj1->TOTAL_ROWS;

$rows = array();
$iTotal = 0;
//print_r($objs);


foreach ($objs as $obj) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "checkBox") {
            if($obj->ready_process == 1){
                $row[] = '<input type="checkbox" disabled class="selPO" value="'.$obj->id.'"/>';
            }else{
                $row[] = '<input type="checkbox" class="selPO" value="'.$obj->id.'"/>';
            }
        }else if ($aColumns[$i] == "po_filenames") {
            $row[] = clean($obj->po_filenames);
        }else if ($aColumns[$i] == "from_email") {
            $row[] = clean($obj->from_email);
        }else if ($aColumns[$i] == "status") {
            if($obj->ready_process == 1){
                $row[] = "Processing...";
            }else if($obj->ready_process == 0){
                $row[] = "Unprocessed";
            }else{
                $row[] = "Status not found";
            }
            
        }else if ($aColumns[$i] == "datetime") {
            $date=date_create($obj->datetime);
            $row[] = clean(date_format($date,"d-M-Y"));
        }else if ($aColumns[$i] == "action") {
//            $row[] = '<input type="button" class="btn btn-primary" onclick="vie   wPO('.$obj->po_filenames.');" value="View PO"/>';
           $row[] = "-";
        }else {
            $row[] = "-";
        }
    }

    $rows[] = $row;
    $iTotal++;
}

function clean($string) {
//   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^a-zA-Z0-9,.:;\-_@\(\)#&%*$! ]/', '', $string); // Removes special chars.
    return $string;
//   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

$db->closeConnection();
/*
 * Output
 */
$output = array(
    //"sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => $rows
);
//print_r($output);
echo json_encode($output);
