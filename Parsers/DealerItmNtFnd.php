<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try{
   
    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('ProductData');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Article No.');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $styleArray = array(
        'font' => array(
            'bold' => false,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
$objPHPExcel->getActiveSheet()->getStyle('A1:X1')->applyFromArray($headerstyleArray);
$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
$srno=1;
$colCount=0;
$rowCount=2;
        
$db=new DBConn();

//$nowtime=date('Y-m-d H:i:s');
$nowtime=date('Y-m-d');
$today=$db->safe(date('Y-m-d 00:00:00'));
$query= "select DISTINCT itemcode from it_dealer_items where is_notfound = 1 and updatetime >= $today"; 
//$query= "select DISTINCT itemcode from it_dealer_items where is_notfound = 1"; 
//print"<br>$query<br>";
$result = $db->getConnection()->query($query);
$itmcnt=0;    
  $srno=1;
  while ($obj = $result->fetch_object()) {    
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$rowCount,$srno); 
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$rowCount,$obj->itemcode); 
      $colCount=0;
      $rowCount++;
      $srno++;
      $itmcnt++;
  }
    //$dir="/var/www/weikfield_DT/home/Parsers/ItemNotFoundXLS/";
    $dir = DEF_DEALER_ITM_NTFOUND_PATH;
    $name="DealerItemnotfound_".$nowtime;
    $Ext=".xls";
    $Filename=$dir.$name.$Ext;

    if(trim($itmcnt>0)){
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter->save('php://output');
        $objWriter->save($Filename);
        print"<br>excel created";
    }else{
        print"<br>excel not created as no missing items today";
    }
  
}catch (Exception $xcp) {
    print $xcp->getMessage();
}
