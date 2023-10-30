<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try {
    $sheetIndex=0;
//$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
//$cacheSettings = array( 'memoryCacheSize' => '1500MB');
//PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $db = new DBConn();
    
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Master Complete Data');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Master_ean');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Master Item Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'FG Code');
    
    $query="select * from it_master_dealers";
    $result1 = $db->getConnection()->query($query);
    $char='D';
    $col_cnt=3;
    $i=0;
    while ($obj = $result1->fetch_object())
    {
       $objPHPExcel->getActiveSheet()->setCellValue($char.'1', $obj->name);
        $objPHPExcel->getActiveSheet()->getColumnDimension($char)->setWidth(30);
   
        $itemcodequery="select * from it_master_items";
    $result2 = $db->getConnection()->query($itemcodequery);

    $rowCount=2;
    while ($obj1 = $result2->fetch_object())
       {
        if($i==0){
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj1->itemcode);  }      
      
        
        
        $query="select * from it_dealer_items where master_dealer_id=$obj->id and master_item_id=$obj1->id";
        echo $query;
          $result3 = $db->getConnection()->query($query);
         // print_r($result1->fetch_object());
          while ($obj1 = $result3->fetch_object())
       {// print_r($obj1);
       
           //   echo"hiii";
        //  $objPHPExcel->getActiveSheet()->setCellValue($char.'$i', $obj1->itemname);     
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_cnt, $rowCount, $obj1->itemname);  
          }
      // echo $rowCount."\n";
         $rowCount++; 
    }
        $col_cnt++;
        $char++;
         $i++;
    }
    
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
   
    
    
    
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
  
   
    $colCount = 0;
    $rowCount = 2;
   

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');

    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 14:00:00 ";
	
    }

    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));    

//    $itemcodequery="select itemcode from it_master_items";
//    $result = $db->getConnection()->query($itemcodequery);
//
//    
//    while ($obj = $result->fetch_object())
//       {       
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->itemcode);       
//        $colCount = 0;
//        $rowCount++;
//        
//    }
    
    $itemcodequery="select * from it_master_items";
        //echo $itemcodequery."<br/>";
    $result3 = $db->getConnection()->query($itemcodequery);
$rowCount = 2;
   
    while ($obj = $result3->fetch_object())
       {  
        //$colIndex = PHPExcel_Cell::columnIndexFromString($cell->getColumn());
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->itemname);       
       // $colCount = 0;
       $rowCount++;
        
    }
    $itemcodequery="select product_code from it_master_items";
        //echo $itemcodequery."<br/>";
    $result = $db->getConnection()->query($itemcodequery);

    $rowCount = 2;
    while ($obj = $result->fetch_object())
       {       
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->product_code);       
        $colCount = 0;
        $rowCount++;
        
    } 
    
    $nowtime = date('Y-m-d');
    $name = "MasterPOReport_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
}   catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
