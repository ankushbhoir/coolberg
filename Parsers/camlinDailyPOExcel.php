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
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Daily Complete Data');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Temporary SO Id');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Customer Id');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Cruser Id');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Division');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Material code');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Quantity');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Change date');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Change Time');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Customer Material Code / Barcod');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
    
    
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
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

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

    //left outer join but its not working
    // $query = "select p.invoice_no,sh.customer_code, di.itemcode, pi.tot_qty, p.expiry_date, cim.camlin_itemcode from it_po p, it_shipping_address sh, it_po_items pi, it_dealer_items di left join it_camlin_item_master cim on di.itemcode = cim.cust_itemcode where p.shipping_id = sh.id and pi.po_id = p. id and pi.dealer_item_id = di.id and p.ctime like '%2020-05-22%'";

    $query = "select p.invoice_no,sh.customer_code, cim.cust_itemcode, pi.tot_qty, p.expiry_date, cim.camlin_itemcode from it_po p, it_shipping_address sh, it_po_items pi, it_dealer_items di, it_camlin_item_master cim where p.shipping_id = sh.id and pi.po_id = p.id and pi.dealer_item_id = di.id and di.itemcode = cim.cust_itemcode and p.master_dealer_id = cim.master_dealer_id and p.status not in (10,3) and p.ctime > $st_dt_db and p.ctime < $ed_dt_db";

    print_r($query);
    print_r("\n");
    
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {

        $datePartes = explode(" ",$obj->expiry_date) ;
    
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->invoice_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->customer_code);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->customer_code);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, "75");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->camlin_itemcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $datePartes[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $datePartes[1]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->cust_itemcode);
        
        $colCount = 0;
        $rowCount++;
        $srno++;
    }
    
    $nowtime = date('Y-m-d');
    $name = "camlinDailyPOReport_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"excel created\n";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
