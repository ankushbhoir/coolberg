<?php

include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
//if (!$currStore || $currStore->usertype != UserType::Admin) { print "Unauthorized Access !!! ".print_r($currStore,true); return; }
$dateRange = isset($_GET['selDateRange']) ? $_GET['selDateRange'] : false;
$chainId = isset($_GET['chainId']) ? $_GET['chainId'] : false;
// print_r($chainId);
// exit();
$aColumns = array('checkBox','invoice_no', 'chain_name','details', 'ctime','action');
$sColumns = array('p.invoice_no', 'md.name', 'p.ctime');
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
    $addWhere = "and p.ctime >='".$FromDate."' and p.ctime <='".$ToDate."'";
}else{
    $date = date('Y-m-d');
    // print_r($date);
    // return;
    $addWhere = "and p.ctime between '".$date." 00:00:00' and '".$date." 23:59:59'";
}

$addChain = "";
//if All chain(-1) selected then no need add chain id in where clause - Mayur 18112019 
if($chainId && $chainId != -1){
    $addChain = "and p.master_dealer_id = $chainId";
}

$sWhere .= "p.status not in (11,21) and p.master_dealer_id = md.id $addWhere $addChain";




/*
 * SQL queries
 * Get data to display
 */
//status = 0 means unprocessed PO
$sQuery = "
    select SQL_CALC_FOUND_ROWS p.id,p.status, p.filename,p.invoice_no,p.ctime,md.displayname as chain_name from it_po p, it_master_dealers md 
        $sWhere
    $sOrder
    $sLimit
";

 //print "$sQuery<br>";
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

// select fullpath from it_po_details where new_filename='1250_1_106565TTKPRESTIGELIMITED-10362.pdf'
// select fullpath from it_po_details where new_filename='1251_1_za711r93.pdf'
// select fullpath from it_po_details where new_filename='1249_1_4040971124-R413.pdf
foreach ($objs as $obj) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "checkBox") {
            $row[] = '<input type="checkbox" class="selPO" value="'.$obj->id.'"/>';
        }else if ($aColumns[$i] == "invoice_no") {
            $row[] = clean($obj->invoice_no);
        }else if ($aColumns[$i] == "chain_name") {
            $row[] = clean($obj->chain_name);
        }else if ($aColumns[$i] == "details") {
                  if($obj->status == POStatus::STATUS_NOT_PROCESSED){
                $row[] = "STATUS_NOT_PROCESSED";
            }else if($obj->status == POStatus::STATUS_ISSUE_AT_PROCESSING){
                $row[] = "STATUS_ISSUE_AT_PROCESSING";
            }else if($obj->status == POStatus::STATUS_UNRECOGNIZED_BU){
                $row[] = "STATUS UNRECOGNIZED BU";
            }else if($obj->status == POStatus::STATUS_MISSING_VENDOR){
                $row[] = "MISSING VENDOR";
            }else if($obj->status == POStatus::STATUS_UNRECOGNIZED_CHAIN){
                $row[] = "SHIP TO PATRTY MISSING";
            }else if($obj->status == POStatus::STATUS_MISSING_EAN){
                $row[] = "MISSING_EAN";
            }else if($obj->status == POStatus::STATUS_JUNK_FILES){
                $row[] = "STATUS_JUNK_FILES";
            }else if($obj->status == POStatus::STATUS_DUPLICATE_PO){
                $row[] = "STATUS_DUPLICATE_PO";
            }else if($obj->status == POStatus::STATUS_ARTICLE_NO_MISSING){
                $row[] = "STATUS_ARTICLE_NO_MISSING";
            }
            else if($obj->status == POStatus::STATUS_MRP_MISMATCH){
                $row[] = "STATUS_MRP_MISMATCH";
            }
            else if($obj->status == POStatus::STATUS_WRONG_COST){
                $row[] = "STATUS_WRONG_COST";
            }
          
        
        }else if ($aColumns[$i] == "ctime") {
            $date=date_create($obj->ctime);
            $row[] = clean(date_format($date,"d-M-Y h:i:s"));
        }else if ($aColumns[$i] == "action") {
              $query = "select fullpath from it_po_details where new_filename='".$obj->filename."'";
            
            $obj_details = $db->fetchObject($query);
            if(isset($obj_details->fullpath) && $obj_details->fullpath != null){

                $temp_path=str_replace(ROOTPATH, '', $obj_details->fullpath);
                $finalpath= str_replace('missingArticleNo', 'missingArticleNo', $temp_path);
                $row[] = '<a class="btn btn-primary" target="_blank" href="'.$finalpath.'"><u>View PO</u></a>';
            }else{
                $row[] = "Old PO";
            }
            
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
