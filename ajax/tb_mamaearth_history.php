<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";

$currStore = getCurrStore();
//C:\xampp\htdocs\mamaearth_dt\mamaearth\ajax\tb_mamaearth_history.php
$aColumns = array('id','name','master_type','master_id','updatetime','action');
$sColumns = array('ml.id','u.name','ml.master_type','ml.master_id');
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

$sWhere .= " ml.updateby_id=u.id";
/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
    select SQL_CALC_FOUND_ROWS
    ml.id,ml.master_id,ml.master_type,u.name,ml.updatetime,ml.change_data from it_masters_logs ml,it_users u 
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
             }else if ($aColumns[$i] == 'name') {
                 $row[] = $obj->name;
             }else if ($aColumns[$i] == 'master_type') {
                 $row[] = $obj->master_type;
             }else if ($aColumns[$i] == 'master_id') {
                 $row[] = $obj->master_id;
             }else if($aColumns[$i] == 'updatetime'){
                 $row[] = $obj->updatetime;
             }else if ($aColumns[$i] == "action") {
//               $row[] = '<button type="button" class="btn btn-primary" onclick="view('.$obj->id.');">view</button>';
//               $row[] = '<a href="#" onclick="edit('.$obj->id.');">view</a>';
                 $encodedData = htmlspecialchars($obj->change_data, ENT_QUOTES, 'UTF-8'); //json_encode($obj->change_data); 
               $row[] = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-default" onclick="changedata('.$obj->id.', \''.$encodedData.'\');">view</button>';
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
