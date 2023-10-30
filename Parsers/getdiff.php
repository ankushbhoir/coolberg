
<?php

require_once("../../it_config.php");
//require_once("session_check.php");
require_once "lib/db/DBConn.php";
//equire_once "lib/logger/clsLogger.php";
//require_once "lib/invoices/clsInvoice.php";
//require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';


try{
    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('ProductData');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'PONumber(Database)');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'ItemsInserted');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'PONumber(POExcel)');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ItemInserted');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Difference');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

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
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($headerstyleArray);
$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
$colCount=0;
$rowCount=2;
$db=new DBConn();


$query1="select ip.invoice_no as PO_Number,count(*) as Itemcount from it_po ip, it_po_items ipt where ipt.ctime >='2017-03-29 00:00:00' and ip.id=ipt.po_id and ip.status=1 group by ip.invoice_no;
" ;
$result1 = $db->getConnection()->query($query1);
//final=== $query=" select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ipt.mrp, ipt.qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, imd.name as dealername, idist.name as distname, idist.code, idist.address, idist.city, idist.state, idt.itemcode as articleno,imt.itemcode as EAN, imt.itemname as description,imt.product_code as productgrp, imi.category_id as category from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_master_items imi where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id AND imi.id=ipt.master_item_id
//";

//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, imd.name as dealername, idist.name as distname, idist.code, idist.address, idist.city, idist.state, idt.itemcode as articleno,imt.itemcode as EAN, imt.itemname as description,imt.product_code as productgrp, imi.category_id as category from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_master_items imi where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id AND imi.id=ipt.master_item_id and ipt.ctime >='2016-07-21 00:00:00';
//";
//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, imd.name as dealername, idist.name as distname, idist.code, idist.address, idist.city, idist.state, idt.itemcode as articleno,imt.itemcode as EAN, imt.itemname as description,imt.product_code as productgrp, imi.category_id as category from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_master_items imi where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id AND imi.id=ipt.master_item_id and ip.ctime >='2016-07-21 17:17:00' and ip.ctime <='2016-07-23 00:00:00' and ip.status=1";
//$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, imd.name as dealername, idist.name as distname, idist.code, idist.address, idist.city, idist.state, idt.itemcode as articleno,imt.itemcode as EAN, imt.itemname as description,imt.product_code as productgrp, c.category as category from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_master_items imi where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id AND imi.id=ipt.master_item_id  and imi.category_id - c.id and ip.ctime >='2016-07-29 00:00:00'and ip.status=1 and ip.master_dealer_id in(3,4,5)";
$query="select ip.invoice_no as PO_Number,count(*) as Itemcount from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_master_items imi ,it_category c where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id AND imi.id=ipt.master_item_id  and imi.category_id = c.id and ip.ctime >='2017-03-29 00:00:00' and ip.ctime <='2017-03-30 23:59:59' and ip.status=1 group by invoice_no";//and ip.master_dealer_id in(2,3,4,5) group by invoice_no";
$result = $db->getConnection()->query($query);
  
  
  
  $srno=1;
  while ($obj = $result1->fetch_object()) {
           
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$rowCount,$srno); 
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$rowCount,$obj-> PO_Number); 
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$rowCount,$obj->Itemcount); 
                              
                $colCount=0;
                $rowCount++;
                $srno++;
    }
    $colCount=0;
    $rowCount=2;
     while ($obj = $result->fetch_object()) {
                               
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$rowCount,$obj->PO_Number); 
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$rowCount,$obj->Itemcount); 
                $colCount=0;
                $rowCount++;
               
    
    }
    
   
    $nowtime=date('Y-m-d H:i:s');
    $name="Diff_In_PO_".$nowtime;
    $Ext=".xls";
    $Filename="/var/www/Weikfield_DT_Test/home/Parsers/Difference/".$name.$Ext;
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename); 
    print"<br>excel created";
    // Redirect output to a client’s web browser (Excel5)
//    header('Content-Type: application/vnd.ms-excel');
//    header('Content-Disposition: attachment;filename="Parsed_PO.xls"');
//    header('Cache-Control: max-age=0');
//    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//    $objWriter->save('php://output');
   
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
