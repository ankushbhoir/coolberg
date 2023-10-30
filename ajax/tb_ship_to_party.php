<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";

$currStore = getCurrStore();

$chain_id = isset($_GET['chain_id']) ? $_GET['chain_id'] : false;

$aColumns = array('id','ship_to_party','displayname','site','site_identifier_type','customer_name','distribution_channel','sales_document_type','distribution_channel_code','createtime','updatetime','action');
$sColumns = array('s.id','s.ship_to_party','m.displayname','s.category','s.site','s.site_identifier_type','s.plant','s.customer_name','s.margin','s.distribution_channel','s.sales_document_type','s.distribution_channel_code');
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

$sWhere .= " s.master_dealer_id = m.id $addedWhere";

/*
 * SQL queries
 * Get data to display
 */

$sQuery = "
    select SQL_CALC_FOUND_ROWS
    s.id, s.ship_to_party, m.displayname, s.site,s.category,s.plant,s.customer_name,s.margin,s.site_identifier_type,s.distribution_channel,s.sales_document_type,s.distribution_channel_code,s.createtime, s.updatetime from it_ship_to_party s, it_master_dealers m
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
             }else if ($aColumns[$i] == 'ship_to_party') {
                 $row[] = $obj->ship_to_party;
             }else if ($aColumns[$i] == 'displayname') {
                 $row[] = $obj->displayname;
             }else if ($aColumns[$i] == 'site') {
                 $row[] = $obj->site;
             }else if ($aColumns[$i] == 'site_identifier_type') {
                 $row[] = $obj->site_identifier_type;
             }
//             else if ($aColumns[$i] == 'category') {
//                 $row[] = $obj->category;
//             }else if ($aColumns[$i] == 'plant') {
//                 $row[] = $obj->plant;
//             }
             else if ($aColumns[$i] == 'customer_name') {
                 $row[] = $obj->customer_name;
//                 $originalCustomerName = $obj->customer_name;
//                 $truncatedCustomerName = strlen($originalCustomerName) > 25 ? substr($originalCustomerName, 0, 25) : $originalCustomerName;
//                 $row[] = $truncatedCustomerName;
             }
//             else if ($aColumns[$i] == 'margin') {
//                 $row[] = $obj->margin;
//             }
             else if ($aColumns[$i] == 'distribution_channel') {
                 $row[] = $obj->distribution_channel;
             }else if ($aColumns[$i] == 'sales_document_type') {
                 $row[] = $obj->sales_document_type;
             }else if ($aColumns[$i] == 'distribution_channel_code') {
                 $row[] = $obj->distribution_channel_code;
             }else if ($aColumns[$i] == 'createtime') {
                 $row[] = $obj->createtime;
             }else if($aColumns[$i] == 'updatetime'){
                 $row[] = $obj->updatetime;
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
