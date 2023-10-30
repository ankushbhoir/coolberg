<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try {
    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Addresses');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Customer code');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Shipping Address');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'DC Address');
     $objPHPExcel->getActiveSheet()->setCellValue('E1', 'DC City');
      $objPHPExcel->getActiveSheet()->setCellValue('F1', 'DC State');
   
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    
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
  
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();  
    
   // $no_space = $db->safe(str_replace(" ","",$obj->dc_address));
    $query = "select * from it_shipping_address where shipping_address!=dc_address";

    echo $query;
    $result = $db->getConnection()->query($query);
     
        $srno = 1;
        while ($obj = $result->fetch_object()) {
 

             $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->id);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->customer_code);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->shipping_address);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->dc_address);      
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->dc_city);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->dc_state);
       
       $colCount = 0;
        $rowCount++;
        $srno++;   

}

     $nowtime = date('Y-m-d');
    $name = "Missing_customer_code_".$nowtime;
    $Ext = ".xls";
   // $Filename = $name . $Ext;
   $Filename = DEF_MISSING_CUST_XLS . $name . $Ext;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
