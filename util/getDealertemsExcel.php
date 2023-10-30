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
    $db = new DBConn();  
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Master Items');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'VLCC Code');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Article Des');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'EAN Code');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Size');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Category');
    
    $objs = $db->fetchAllObjects("select * from it_master_dealers where id not in (1,6,9,10,12,13,17,18,19,29,34,35,36,37,38,39,44,25,3) order by name");
    
    $cell = "G";
    foreach($objs as $obj){
        $objPHPExcel->getActiveSheet()->setCellValue($cell."1", $obj->name);
        $cell++;
    }
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    
     $cell = "G";
    foreach($objs as $obj){
        $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setWidth(20);
        $cell++;
    }          
    
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
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    
     $cell = "G";
    foreach($objs as $obj){
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($styleArray);
        $cell++;
    }  
    
    $colCount = 0;
    $rowCount = 2;
           
    $query = "select i.*,c.category from it_master_items i,it_category c where c.id=i.category_id";

    echo $query;
    $result = $db->getConnection()->query($query);
     
        $srno = 1;
        while ($obj1 = $result->fetch_object()) {
 

       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj1->product_code);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj1->itemname);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj1->itemcode);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj1->sku);      
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj1->mrp);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj1->category);
       
       $cnt=6;
       foreach($objs as $obj){
          $dealer = $db->fetchObject("select * from it_dealer_items where master_dealer_id=$obj->id and (master_item_id=$obj1->id || master_item_id is null)");
        //  echo "select * from it_dealer_items where master_dealer_id=$obj->id and master_item_id=$obj1->id\n";
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cnt, $rowCount, $dealer->itemcode);       
        $cnt++;
       }
       
       $colCount = 0;
        $rowCount++;
        $srno++;   

}

     $nowtime = date('Y-m-d');
    $name = "Master_item_list";
    $Ext = ".xls";   
   $Filename = DEF_MISSING_CUST_XLS . $name . $Ext;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
