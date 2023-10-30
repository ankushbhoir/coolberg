<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
//to show value in indian currency
setlocale(LC_MONETARY, 'en_IN');

$aColumns = array('srNo','product_category_id','master_item_description','quantity','tot_amt_including_gst');
$sColumns = array('drd.product_category','drd.master_item_description', 'drd.quantity', 'drd.tot_amt_including_gst');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
// print_r($_GET);

$region = isset($_GET['region']) ? $_GET['region'] : false;
$state = isset($_GET['state']) ? $_GET['state'] : false;
$city = isset($_GET['city']) ? $_GET['city'] : false;
$chain = isset($_GET['chain']) ? $_GET['chain'] : false;
$dc = isset($_GET['dc']) ? $_GET['dc'] : false;
$category = isset($_GET['category']) ? $_GET['category'] : false;
$product = isset($_GET['product']) ? $_GET['product'] : false;
$fromdate = isset($_GET['fromdate']) ? $_GET['fromdate'] : false;
$todate = isset($_GET['todate']) ? $_GET['todate'] : false;


/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by sum(drd.tot_amt_including_gst) desc";
if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = " ORDER BY  ";
	for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
	{
		if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		{
			$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
			 	".$db->getConnection()->real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
	}
	
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == " ORDER BY " )
	{
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

$addWhere="";
if($chain != '' && $chain != -1){
                    $addWhere .= ' and drd.master_dealer_id='.$chain;
                }
                if($region != '' && $region != -1){
                	$objZone = $db->fetchObject("Select name from it_regions where id = $region");
                    $addWhere .= " and drd.zone='$objZone->name'";
                }
                if($category != '' && $category != -1){
                    $addWhere .= ' and drd.product_category_id='.$category;
                }
                if($product != '' && $product != -1){
                	$objProduct = $db->fetchObject("Select itemname from it_master_items where id = $product");
                    $addWhere .= " and drd.master_item_description='$objProduct->itemname'";                   
                }
                if($state != '' && $state != -1){
                	$objState = $db->fetchObject("Select name from it_regions where id = $state");
                    $addWhere .= " and drd.state='$objState->name'";
                }
                if($city != '' && $city != -1){
                    $addWhere .= " and drd.city='$city'";
                }
                if($dc != '' && $dc != -1){
                    $addWhere .= ' and drd.dc_address='.$dc;
                }
                if(!$fromdate || !$todate){
					//current month's date range
					$fromdate = date('Y-m-01');
					$todate = date('Y-m-d');
				}else{
					$fromdate = date("Y-m-d", strtotime($fromdate));
					$todate = date("Y-m-d", strtotime($todate));
				}

// print_r($addWhere);
$sWhere .= "drd.po_date >= '$fromdate' and drd.po_date <= '$todate' and drd.master_item_description !='' $addWhere";

/*
 * SQL queries
 * Get data to display
 */


$sQuery = "
	select SQL_CALC_FOUND_ROWS drd.product_category,drd.master_item_description, sum(drd.quantity) as quantity, sum(drd.tot_amt_including_gst) as tot_amt_including_gst from it_daily_report_data drd $sWhere group by drd.master_item_description
	 
	$sOrder
	$sLimit
";
// echo $sQuery;
// error_log("\nDashboars query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); 
$iTotal=0;
$srNo = 1;
foreach ($objs as $obj){
	$row = array();

	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'srNo') {
                 $row[] = $srNo;
             }else if ($aColumns[$i] == 'product_category_id') {
                 $row[] = $obj->product_category;
             }else if ($aColumns[$i] == 'master_item_description') {
             	// Remove special characters except space
				$cleanStr = preg_replace('/[^A-Za-z0-9 ]/', '', $obj->master_item_description);
                 $row[] = $cleanStr;
             }else if ($aColumns[$i] == 'quantity') {
                 $row[] = $obj->quantity;
             }else if($aColumns[$i] == 'tot_amt_including_gst'){
             	$tot_amt_including_gst = money_format('%!i', $obj->tot_amt_including_gst);
                 $row[] = $tot_amt_including_gst;
             }else{
                 $row[] = "-";
             }   
	}
	$rows[] = $row;
	$iTotal++;
	$srNo++;
}
// print_r($rows);
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

echo json_encode( $output );
?>
