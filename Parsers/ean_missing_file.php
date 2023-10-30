<?php 
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';


try {
    $sheetIndex=0;

// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Daily EAN Missing Data');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No.');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'EAN');
      $objPHPExcel->getActiveSheet()->setCellValue('E1', 'SKU');
    
    
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    
    
    
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

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 14:00:00 ";
    //$st_dt = $srt_dt . " 00:00:00 ";
    }
      // $st_dt = "2020-05-26 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));


    //original query mayur
   echo $query="select * from it_missing_ean where created_at like '".date('Y-m-d')."%'";
    

   // echo $query."<br/>";
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        //$address = $obj->address;//str_replace("                                          ", " ", $obj->address);

     
        
        
          

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->po_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->chain_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->ean);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, '');
        

        $colCount = 0;
        $rowCount++;
        $srno++;
    }

    //$nowtime=date('Y-m-d H:i:s');
     $nowtime = date('Y-m-d');
    $name = "Daily_EAN_SKU_MISSING" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>

?>