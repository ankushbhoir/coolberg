<?php
//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "lib/email/EmailHelper.php";

$db = new DBConn();
$fpatharr = array();

$start_date = date('Y-m-d',strtotime("first day of previous month"));
$end_date = date('Y-m-d',strtotime("last day of previous month"));
//echo $start_date."<>".$end_date;

$start_date_db = $db->safe($start_date." 00:00:00");
$end_date_db = $db->safe($end_date." 23:59:59");
$emailHelper = new EmailHelper();

$query = "select m.name as chain_name,ps.master_dealer_id, sum(ps.noofpos) as no_of_pos from it_process_status ps, it_master_dealers m where ps.status not in (".POStatus::STATUS_DUPLICATE_PO.") and ps.is_current_status = 1 and ps.createtime between $start_date_db and $end_date_db and ps.master_dealer_id = m.id group by ps.master_dealer_id";
//echo $query;
$objs = $db->fetchAllObjects($query);

$sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('DataFixing');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Count');
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);

 
    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
    ));
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    
    $pos_cnt = 0;
    foreach($objs as $obj){
        $pos_cnt += $obj->no_of_pos;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->chain_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->no_of_pos); 
        
        $colCount = 0;
        $rowCount++;
    }
    
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, "Total"); 
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $pos_cnt); 
    

   $month = date("F", strtotime($start_date)); 
   $year = date("Y", strtotime($start_date)); 
    $name = $month."_".$year;
    $Ext = ".xls";
    $Filename = DEF_BILLING_DATA.$name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    $dirBilling = $Filename;
//    echo $dirBilling;
    array_push($fpatharr, $dirBilling);
    
    $subject = "VLCC: Billing Data of $month $year";
    $body = " Dear Sir,<br>Please find attached VLCC billing data.";
    
     $toArray = array("mdeodhar@intouchrewards.com","npande@intouchrewards.com","igoyal@intouchrewards.com");    
//    $toArray = array("npande@intouchrewards.com");

    $errormsg = $emailHelper->send($toArray, $subject, $body, $fpatharr);
    if ($errormsg != "0") {
        $errors['mail'] = " <br/> Error in sending mail, please try again later.";
        return -1;
    } else {
        return 1;
    }
