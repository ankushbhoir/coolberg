<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";

$currStore = getCurrStore();

$aColumns = array('id','loc_id','name','code','gst_no','state_id','createtime','updatetime','action');
$sColumns = array('id','loc_id','name','code','gst_no','state_id','createtime');
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

//if (trim($sWhere) == "") {
//    $sWhere .= " where ";
//} else {
//    $sWhere .= " and ";
//}
//
//$addedWhere="";
//if($chain_id != -1){
//    $addedWhere .= "and m.id = $chain_id";
//}
//
//$sWhere .= " s.master_dealer_id = m.id $addedWhere";

/*
 * SQL queries
 * Get data to display
 */

$sQuery = "
    select SQL_CALC_FOUND_ROWS
    id,loc_id,name,code,gst_no,state_id,createtime,updatetime from it_cool_locations
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
             }else if ($aColumns[$i] == 'loc_id') {
                 $row[] = $obj->loc_id;
             }else if ($aColumns[$i] == 'name') {
                 $row[] = $obj->name;
             }else if ($aColumns[$i] == 'code') {
                 $row[] = $obj->code;
             }else if ($aColumns[$i] == 'gst_no') {
                 $row[] = $obj->gst_no;
             }else if ($aColumns[$i] == 'state_id') {
                 $row[] = $obj->state_id;
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
