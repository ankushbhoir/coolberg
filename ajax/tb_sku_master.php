<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";

$currStore = getCurrStore();

$chain_id = isset($_GET['chain_id']) ? $_GET['chain_id'] : false;

$aColumns = array('id','displayname','sku','ean','category','product_name','mrp','gst','inner_size','outer_size','purchase_rate_gst','moq','action');
$sColumns = array('ekm.id','m.displayname','ekm.sku','ekm.ean','ekm.category','ekm.product_name','ekm.mrp','ekm.gst','ekm.inner_size','ekm.outer_size','ekm.purchase_rate_gst','ekm.moq');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

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
$sOrder = " order by id ";
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
//    $sWhere = $sWhere . " and (";
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

if (trim($sWhere) == "") {
    $sWhere .= " where ";
} else {
    $sWhere .= " and ";
}

$addedWhere="";
if($chain_id != -1){
    $addedWhere .= "and m.id = $chain_id";
}

$sWhere .= " ekm.master_dealer_id = m.id $addedWhere";
/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
    select SQL_CALC_FOUND_ROWS
    ekm.id, m.displayname,ekm.sku,ekm.ean, ekm.category, ekm.product_name, ekm.mrp, ekm.gst, ekm.inner_size, ekm.outer_size,ekm.purchase_rate_gst,ekm.moq from it_ean_sku_mapping ekm, it_master_dealers m
    $sWhere 
    $sOrder
    $sLimit
";
// echo $sQuery;
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
    SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
foreach ($objs as $obj){
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }else if ($aColumns[$i] == 'displayname') {
                 $row[] = $obj->displayname;
             }else if ($aColumns[$i] == 'sku') {
                 $row[] = $obj->sku;
             }else if ($aColumns[$i] == 'ean') {
                 $row[] = $obj->ean;
             }else if ($aColumns[$i] == 'category') {
                 $row[] = $obj->category;
             }else if($aColumns[$i] == 'product_name'){
//                 $row[] = $obj->product_name;
                 $originalProductName = $obj->product_name;
                $truncatedProductName = strlen($originalProductName) > 37 ? substr($originalProductName, 0, 37) : $originalProductName;
                $row[] = $truncatedProductName;
             }else if($aColumns[$i] == 'mrp'){
                 $row[] = $obj->mrp;
             }else if($aColumns[$i] == 'gst'){
                 $row[] = $obj->gst;
             }else if($aColumns[$i] == 'inner_size'){
                 $row[] = $obj->inner_size;
             }else if($aColumns[$i] == 'outer_size'){
                 $row[] = $obj->outer_size;
             }else if($aColumns[$i] == 'purchase_rate_gst'){
                 $row[] = $obj->purchase_rate_gst;
             }else if($aColumns[$i] == 'moq'){
                 $row[] = $obj->moq;
             }else if ($aColumns[$i] == "action") {
               $row[] = '<button type="button" class="btn btn-primary" onclick="edit('.$obj->id.');">Edit</button>';
             }else{
                 $row[] = "-";
             }
    }
    $rows[] = $row;
    $iTotal++;
}

$db->closeConnection();
/*
 * Output
 */
$output = array(
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => $rows
);

echo json_encode( $output );
?>
