<?php

require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

extract($_GET);
$chain_id = ($_GET['chain_id']);

$errors = array();
$chkbxarr = array();
try {
  
    $db = new DBConn();
    $addedWhere = "";

    if ($chain_id != -1) {
        $addedWhere .= "and m.id = $chain_id";
    }
    
    $query="select esm.id, m.displayname,esm.sku, esm.ean,esm.product_name,esm.mrp,esm.gst, esm.category,esm.inner_size,esm.outer_size,esm.purchase_rate_gst,esm.moq from it_ean_sku_mapping esm, it_master_dealers m where esm.master_dealer_id = m.id $addedWhere";
//    print_r($query);exit();
    $oobjs = $db->fetchAllObjects($query);
    $sheetIndex = 0;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Customers list');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'SKU');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Classification');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Product Name');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'GST');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Inner Size');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Outer Size');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Purchase Rate W/O GST');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'MOQ');



    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);

    $styleArray = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
        )
    );
    $headerStyle = array(
        'font' => array(
            'bold' => true,
            'size' => '10',
            'align' => 'center',
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($headerStyle);
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

    $colCount = 0;
    $rowCount = 2;
    $srno = 1;
    if (isset($oobjs)) {
        foreach ($oobjs as $obj) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->displayname);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->sku);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->ean);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->category);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->product_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->mrp);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->gst);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->inner_size);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->outer_size);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->purchase_rate_gst);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $obj->moq);

            $colCount = 0;
            $rowCount++;
            $srno++;
        }
        $filename = 'SKU_Master_details.xls';
        header('Content-Disposition: attachment;filename=' . $filename);
         ob_end_clean();
    ob_start();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }else{
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, "Data not found for this chain.");
    }
    $db->closeConnection();
} catch (Exception $xcp) {
    print $xcp->getMessage();
}