<?php
   require_once("../../it_config.php");
   require_once "lib/db/DBConn.php";
   require_once "lib/php/Classes/PHPExcel.php";
   require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
  
   
try{
   $db = new DBConn();
   $sheetIndex=0;
   // Create new PHPExcel object
   $objPHPExcel = new PHPExcel();
   // Create a first sheet, representing points by dealer data
   $objPHPExcel->setActiveSheetIndex($sheetIndex);
   
    $objPHPExcel->getActiveSheet()->setTitle('WeikField_DT_Excel_SKU_isNull');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 's.No');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Product Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Category');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'SKU');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Pack Type');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Case Size');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Bar Code(EAN)');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Product Code');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Length(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Width(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Height(mm)');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Shelf life');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'Front Image');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'Back Image');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    
    $styleArray = array(
    'font'  => array(
        'bold'  => false,
//        'color' => array('rgb' => 'FF0000'),
        'size'  => 11,
    ));
   $headerstyleArray = array(
    'font'  => array(
        'bold'  => true,
//        'color' => array('rgb' => 'FF0000'),
        'size'  => 11,
    ));
   
   $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->applyFromArray($headerstyleArray);
   $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
   $objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);
    
   //$colCount=0;
   $srNo=0;
   $rowCount=2;
   $query="select itemname, (select category from it_category where id=category_id) Category, sku, ".
       " pack_type, case_size, itemcode, product_code, mrp, length, width, height, shelf_life ".
       " from it_master_items ".
       " where sku is null and is_weikfield=1";
   $result = $db->getConnection()->query($query);
    while ($obj = $result->fetch_object()) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, ++$srNo);               
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$rowCount, $obj->itemname);         
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->Category);             
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->sku); 
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->pack_type);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->case_size);
	        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount,$obj->itemcode);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount,$obj->product_code);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount,$obj->mrp);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount,$obj->length);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount,$obj->width);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount,$obj->height);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount,$obj->shelf_life);
        //  $colCount=0;
          $rowCount++;
 } 
   
   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="SKUIsNull.xls"');
   header('Cache-Control: max-age=0');
   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
   $objWriter->save('php://output');
   }catch(Exception $xcp){
      print $xcp->getMessage();
   }
   $colCount=0;
   $rowCount=2;
?>
