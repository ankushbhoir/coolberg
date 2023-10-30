<?php

include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
$db = new DBConn();
/* Indexed column (used for fast and accurate table cardinality) */

$aColumns = array('invoice_no', 'itemname', 'quantity', 'value', 'sale_date', 'by_user', 'createtime');
$sColumns = array('s.invoice_no', 'mi.itemname', 's.quantity', 's.value', 's.sale_date', 'u.name');
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
//$sOrder = "order by m.name, dc_name";
$sOrder = "order by s.sale_date, s.createtime";
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


$sWhere .= "s.item_id = mi.id and s.updated_by = u.id";




/*
 * SQL queries
 * Get data to display
 */
//status = 0 means unprocessed PO
$sQuery = "
	select SQL_CALC_FOUND_ROWS s.invoice_no, mi.itemname, s.quantity, s.value, s.sale_date, u.name as by_user, s.createtime from it_sales_data s, it_master_items mi, it_users u
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
        if ($aColumns[$i] == "invoice_no") {
            $row[] = $obj->invoice_no;
        }else if ($aColumns[$i] == "itemname") {
            $row[] = $obj->itemname;
        }else if ($aColumns[$i] == "quantity") {
            $row[] = $obj->quantity;
        }else if ($aColumns[$i] == "value") {
            $row[] = $obj->value;
        }else if ($aColumns[$i] == "sale_date") {
            $row[] = date('d-m-Y',strtotime($obj->sale_date));
        }else if ($aColumns[$i] == "by_user") {
            $row[] = $obj->by_user;
        }else if ($aColumns[$i] == "createtime") {
            $row[] = date('d-m-Y',strtotime($obj->createtime));
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
