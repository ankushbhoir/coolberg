<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 120);  
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
    $objPHPExcel->getActiveSheet()->setTitle('ProductData');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Intouch');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Vendor Name');
    //$objPHPExcel->getActiveSheet()->setCellValue('E1', 'TIN No.');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'DC Location / Store Location');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'City');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'State');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'PO Date');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Delivery Date');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'PO Expiry Date');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Article No');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'Description');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'SKU Units');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Brand Weikfield/EcoValley');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Product Group');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'Product Category');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', 'CAR');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', 'Total QTY');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'BASIC Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', 'VAT');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', 'Total Amount');
//    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'Supplier id');
//    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Supplier Name');
//     $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'vendor code');
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    //$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($headerstyleArray);
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
    //$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
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
    $objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray);
//    $objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
//    $objPHPExcel->getActiveSheet()->getStyle('AA')->applyFromArray($styleArray);
//    $objPHPExcel->getActiveSheet()->getStyle('AB')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
    $objPHPExcel->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-01');
    $st_dt = $srt_dt . " 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));

    $query = "select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch,"
           . " ip.shipping_id as sid, ip.dist_id as distid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,"
           . "ipt.vat, ipt.amt, imd.name as dealername, idt.itemcode as articleno,imt.itemcode as EAN, "
           . "imt.itemname as description,imt.sku as sku ,imt.product_code as productgrp, "
           . "sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name"
           . " from it_po ip, it_po_items ipt, it_master_dealers imd, it_dealer_items idt,"
           . " it_master_items imt, it_shipping_address sh where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND "
           . "idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and"
           . " imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and "
           . "ip.status = " . POStatus::STATUS_PROCESSED . " and sh.id = ip.shipping_id order by dealername,ip.invoice_no";
//$query = "select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch,"
//           . " ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,"
//           . "ipt.vat, ipt.amt, imd.name as dealername, idt.itemcode as articleno,imt.itemcode as EAN, "
//           . "imt.itemname as description,imt.sku as sku ,imt.product_code as productgrp, d.code as distid, d.name as distname, "
//           . "c.category as category,sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode"
//           . " from it_po ip, it_po_items ipt, it_master_dealers imd, it_dealer_items idt,"
//           . " it_master_items imt,it_category c, it_shipping_address sh, it_distributors d, it_business_unit bu where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND "
//           . "idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and imt.category_id = c.id and ip.dist_id=d.id and d.bu_id= bu.id and"
//           . " imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and "
//           . "ip.status = " . POStatus::STATUS_PROCESSED . " and sh.id = ip.shipping_id order by dealername,ip.invoice_no";   
echo $query."<br/>";
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {
        
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
        $prodname = $obj->description;
        $brand = explode(" ", $prodname);
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        $Del_date = explode(" ", $obj->delivery_date);
        $Exp_date = explode(" ", $obj->expiry_date);
        $City = strtoupper($obj->city);  // providing city name in capital
        $State = strtoupper($obj->state); //providing state name in capital
    
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->Intouch);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->dealername);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->name);
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $TinNo);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $address);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $City);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $State);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $inv_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $PO_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $Del_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $Exp_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $obj->articleno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $obj->EAN);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $obj->description);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $obj->sku);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $brand[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $obj->productgrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, 0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $obj->mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $obj->CAR);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $obj->cost_price);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $rowCount, $obj->vat);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $rowCount, $obj->amt);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $rowCount, $obj->distid);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->distname);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $rowCount, $obj->vendorcode);
        $colCount = 0;
        $rowCount++;
        $srno++;
    }
    //$nowtime=date('Y-m-d H:i:s');
    $nowtime = date('Y-m-d');
    $name = "MTD_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_PARSED_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
