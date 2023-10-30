<?php

require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

try{
   
    $sheetIndex = 0;
    $itemcode = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('ProductData');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'EAN No.');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
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
  $objPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
$srno=1;
$colCount=0;
$rowCount=2;
        
$db=new DBConn();

//$nowtime=date('Y-m-d H:i:s');
$nowtime=date('Y-m-d');
$today=$db->safe(date('Y-m-d 00:00:00'));
//$query= "select itemcode from it_master_items where is_notfound = 1 and updatetime >= $today"; 
$query= "select DISTINCT itemcode from it_master_items where is_notfound = 1 and updatetime >= $today";
//$query= "select DISTINCT itemcode from it_master_items where is_notfound = 1";
//print"<br>$query<br>";
$result = $db->getConnection()->query($query);

$itmcnt=0;    
  $srno=1;
  while ($obj = $result->fetch_object()) { 
      $itemcode = $obj->itemcode;
      //s$it =  number_format($itemcode,0,'','');
      
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$rowCount,$srno); 
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$rowCount,$itemcode.' ');
      
      $colCount=0;
      $rowCount++;
      $srno++;
      $itmcnt++;
  }
    //$dir="/var/www/weikfield_DT/home/Parsers/ItemNotFoundXLS/";
    $dir = DEF_ITM_NTFOUND_PATH;
    $name="Itemnotfound_".$nowtime;
    $Ext=".xls";
    $Filename=$dir.$name.$Ext;
    //print"<br>dest=$Filename<br>";
//for($i=0;$i<count($itemntfound);$i++){
//    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$rowCount,$srno);
//    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$rowCount,$itemntfound[$i]);
//    $colCount=0;
//    $rowCount++;
//    $srno++;
//}
    // Redirect output to a client’s web browser (Excel5)
    //header('Content-Type: application/vnd.ms-excel');
   // header('Content-Disposition: attachment;filename="'.$Filename.'"');
    //header('Cache-Control: max-age=0');
    if(trim($itmcnt>0)){
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter->save('php://output');
        $objWriter->save($Filename);
        print"<br>excel created";
    }else{
        print"<br>excel not created as no missing items today";
    }
    //moveToMailed();
}catch (Exception $xcp) {
    print $xcp->getMessage();
}

/*function moveToMailed()
{
 * // for below path use generic defination i.e. it_config logic
   $dirR="/var/www/weikfield_DT/home/Parsers/ItemNotFoundXLS/";
   $dirW="/var/www/weikfield_DT/home/Parsers/ItemNotFoundXLSMailed/";
   
   if (file_exists($dirR))
   {
   $xlsfile= scandir($dirR);
   print_r($xlsfile);print "<br>";
   
    foreach($xlsfile as $readfile){
        if(trim($readfile)!="" && trim($readfile)!="." && trim($readfile) != ".."){
            print"src:".$dirR.$readfile."<br>";
            print"dest:".$dirR.$readfile."<br>";
                if(copy($dirR.$readfile,$dirW.$readfile))
                {
                    $delete[]=$dirR.$readfile;
                }

         }
    }
    print"<br>Delete=><br>";print_r($delete); print"<br>";
   }
    if(! empty($delete)){
        foreach ($delete as $file_xls) {
            if(trim($file_xls)!="" && trim($file_xls)!="." && trim($file_xls) != ".."){  
               unlink($file_xls);
            }
        } 
    } 
 
}*/