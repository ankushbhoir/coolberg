<?php
ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
ini_set('max_execution_time', 180);  
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Date');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Expiry Date');    
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Chain Name');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Store Name');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Store Code');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'CFA Location');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'State');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Zone');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'PO NO');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'PO Value');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Supplied Value (incl. GST)');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Billing Date');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '% Fill Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'Status');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'PO Type');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Remarks');    
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);    
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
    
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:AB1')->applyFromArray($headerstyleArray);
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);    
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);//H
    $objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);//M
    $objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);

    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-01');
    $st_dt = $srt_dt . " 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));

$query = "select round(sum(ipt.amt),2) as po_value,ip.invoice_no, ip.invoice_date, ip.expiry_date,imd.name as dealername, sh.dc_state as state,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip,it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id  and  ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and  ip.ctime between $st_dt_db and $ed_dt_db and ip.status =1 and idt.is_vlcc=1 group by ip.invoice_no order by imd.name";
//echo $query."<br/>";
//$qry = "select round(sum(ipi.amt),2) as po_value,ip.invoice_date as po_date,ip.expiry_date,d.name as chain_name,sh.dc_name as vendor_name,bu.code as vendor_code,sh.dc_state as state,ip.invoice_no as po_no from it_po ip,it_po_items ipi,it_master_dealers d,it_shipping_address sh,it_business_unit bu where ip.id=ipi.po_id and ip.status=1 and ip.master_dealer_id=d.id and sh.master_dealer_id=ip.master_dealer_id and bu.master_dealer_id=ip.master_dealer_id and sh.master_dealer_id=bu.master_dealer_id and ip.ctime between $st_dt_db and $ed_dt_db group by ip.id";
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {        
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);        
        $Exp_date = explode(" ", $obj->expiry_date);        
        $State = strtoupper($obj->state); //providing state name in capital       
        
        $State_db = $db->safe($State);
        $regions = $db->fetchObject("select * from it_regions where active=1 and name=$State_db and zone_id!=0 and parent_id=0");
        if(isset($regions) && !empty($regions) && $regions!=NULL){
            $zone = $db->fetchObject("select name from it_regions where id=$regions->zone_id");
            if(isset($zone) && !empty($zone) && $zone!=NULL){
                $zone_name = $zone->name;
            }else{
                $zone_name = "-";
            }
        }
        
        //Check for PO Type: Direct or Distributor
        if(preg_match('/vlcc\s+personal\s+care\s+ltd/i',$obj->invoice_text)==1){
            $po_type = "Direct";
          //array_push($arr,$obj->master_dealer_id."<>".$obj->invoice_no);
        }else{
            $po_type = "Distributor";
        }
    
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $PO_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $Exp_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->dealername);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->vendorcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $State);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $State);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $zone_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $inv_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->po_value);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, "-");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, "-");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, "-");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, "Pending");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $po_type);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, "-");
        
        $colCount = 0;
        $rowCount++;
        $srno++;
    }    
    $nowtime = date('Y-m-d');
    $name = "POWiseExcel_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_PARSED_DAILY_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
