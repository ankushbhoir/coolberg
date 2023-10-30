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
    $query="select s.id, s.ship_to_party, m.displayname, s.site,s.site_identifier_type, s.customer_name,s.distribution_channel,s.sales_document_type,s.distribution_channel_code,s.createtime, s.updatetime from it_ship_to_party s, it_master_dealers m  where s.master_dealer_id = m.id $addedWhere ";
    

    $oobjs = $db->fetchAllObjects($query);
    

    $sheetIndex = 0;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Customers list');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Ship To Party');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Master Dealer');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Site Identifier');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Site Identifier Type');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Customer Name');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Distribution Channel');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Sales Document Type');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Distribution Channel Code');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Created DateTime');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Updated DateTime');



    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);

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
    $colCount = 0;
    $rowCount = 2;
    $srno = 1;
    if (isset($oobjs)) {
        foreach ($oobjs as $obj) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->ship_to_party);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->displayname);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->site);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->site_identifier_type);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->customer_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->distribution_channel);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->sales_document_type);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->distribution_channel_code);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->createtime);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->updatetime);

            $colCount = 0;
            $rowCount++;
            $srno++;
        }
        $filename = 'ship_to_party_details.xls';
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