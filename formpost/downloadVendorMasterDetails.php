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

// echo "chain_id=".$chain_id;

$errors = array();
$chkbxarr = array();
try {
  
    $db = new DBConn();
 $addedWhere = "";

    if ($chain_id != -1) {
        $addedWhere .= "and m.id = $chain_id";
    }

    $query="select s.id, s.vendor_number, m.displayname, s.plant,s.storage_location_code, s.createtime, s.updatetime from it_vendor_plant_mapping s, it_master_dealers m where s.master_dealer_id = m.id $addedWhere";
    
    // echo $query;
    
    $oobjs = $db->fetchAllObjects($query);
    $sheetIndex = 0;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Customers list');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Plant Postal Number');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Master Dealer');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Plant Code');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Storage Location Code');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Created DateTime');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Updated DateTime');



    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

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
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);

    $colCount = 0;
    $rowCount = 2;
    $srno = 1;
    if (isset($oobjs)) {
        foreach ($oobjs as $obj) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->vendor_number);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->displayname);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->plant);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->storage_location_code);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->createtime);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->updatetime);

            $colCount = 0;
            $rowCount++;
            $srno++;
        }
        $filename = 'Vendor_Master_details.xls';
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