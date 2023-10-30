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
    $objPHPExcel->getActiveSheet()->setTitle('Master Items');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'EAN Code');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Itemname');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Category');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'SKU');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'FG Code');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'MRP');
   
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    
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
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();  
       
    $query = "select i.*,c.category from it_master_items i,it_category c where c.id=i.category_id";

    echo $query;
    $result = $db->getConnection()->query($query);
     
        $srno = 1;
        while ($obj = $result->fetch_object()) {
 

       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->itemcode);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->itemname);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->category);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->sku);      
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->product_code);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->mrp);
       
       $colCount = 0;
        $rowCount++;
        $srno++;   

}

     $nowtime = date('Y-m-d');
    $name = "Master_item_list";
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
