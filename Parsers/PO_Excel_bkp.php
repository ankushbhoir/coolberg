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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
//    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Intouch');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Chain Name');
    //$objPHPExcel->getActiveSheet()->setCellValue('E1', 'TIN No.');
//    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Supplier id');
//    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Supplier Name');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Customer code');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Store code');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Store Name');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'DC Location / Store Location');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'City');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'CFA Location');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'State');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'PO Type');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'PO Date');
   // $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Delivery Date');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'PO Expiry Date');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'FG code');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Article No');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'Description');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'SKU Units');
//    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'Brand Weikfield/EcoValley');
    
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'Product Category');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', 'Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'CAR');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', 'Total QTY');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', 'BASIC Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'Tax');
    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Total Amount');
//    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'Supplier id');
//    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Supplier Name');
//     $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'vendor code');
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    //$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
    
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
    //$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
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
    $objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AA')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AB')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AC')->applyFromArray($styleArray);
    
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();

    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-01');
    $st_dt = $srt_dt . " 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));

//    $query = "select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch,"
//           . " ip.shipping_id as sid, ip.dist_id as distid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,"
//           . "ipt.vat, ipt.amt, imd.name as dealername, idt.itemcode as articleno,imt.itemcode as EAN, "
//           . "imt.itemname as description,imt.sku as sku ,imt.product_code as productgrp, "
//           . "c.category as category,sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name"
//           . " from it_po ip, it_po_items ipt, it_master_dealers imd, it_dealer_items idt,"
//           . " it_master_items imt,it_category c, it_shipping_address sh where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND "
//           . "idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and imt.category_id = c.id and"
//           . " imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and "
//           . "ip.status = " . POStatus::STATUS_PROCESSED . " and sh.id = ip.shipping_id order by dealername,ip.invoice_no";

//$query = "select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch,"
//           . " ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,"
//           . "ipt.vat, ipt.amt, imd.name as dealername, idt.itemcode as articleno,imt.itemcode as EAN, "
//           . "imt.itemname as description,imt.sku as sku ,imt.product_code as productgrp, d.supplier_id as distid, d.name as distname, "
//           . "c.category as category,sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode"
//           . " from it_po ip, it_po_items ipt, it_master_dealers imd, it_dealer_items idt,"
//           . " it_master_items imt,it_category c, it_shipping_address sh, it_distributors d, it_business_unit bu where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND "
//           . "idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and imt.category_id = c.id and ip.dist_id=d.id and d.bu_id= bu.id and"
//           . " imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and "
//           . "ip.status = " . POStatus::STATUS_PROCESSED . " and sh.id = ip.shipping_id order by dealername,ip.invoice_no";   

//     $query="select distinct ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, "
//        . "ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,"
//        . "ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.name as dealername,imd.show_code as showflag,"
//        . " idt.itemcode as articleno,idt.itemname as description,  d.supplier_id as distid, d.name as distname,"
//        . " sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode"
//        . " from  it_master_dealers imd, it_dealer_items idt, it_master_items imt,"
//        . "it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt "
//        . " where ip.ctime >= $st_dt_db and ip.ctime <=$ed_dt_db and ip.status =" . POStatus::STATUS_PROCESSED." and "
//        . "ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and idt.is_weikfield = 1 AND "
//        . " ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id order by dealername,ip.invoice_no;";
$query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description,  d.supplier_id as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,ip.invoice_text from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id  and  ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and idt.is_vlcc = 1  AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status =1 order by dealername,ip.invoice_no;";
    echo $query."<br/>";
   $result = $db->getConnection()->query($query);

    $srno = 1;
    while ($obj = $result->fetch_object()) {
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
        $itemname = $obj->description;
        $brand = explode(" ", $itemname);
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        $Del_date = explode(" ", $obj->delivery_date);
        $Exp_date = explode(" ", $obj->expiry_date);
        $City = strtoupper($obj->city);  // providing city name in capital
        $State = strtoupper($obj->state); //providing state name in capital
        
        $mid=$obj->master_item_id;
       //echo "mid------$mid \n";
        if($mid != NULL){
            $mquery="select imt.*, c.category from it_master_items imt, it_category c where imt.id= $mid and imt.category_id= c.id";
            $mobj= $db->fetchObject($mquery);
            //echo"master--------$mquery\n\n";
            
            if(isset($mobj)){
//                echo"IN IF\n";
                $EAN=$mobj->itemcode;
                $itemname= $mobj->itemname;
                $sku=$mobj->sku;  
                $productgrp=$mobj->product_code; 
                $category=$mobj->category;
            }
        }
        
        $sup_id= trim($obj->distid);
        if(trim($obj->showflag == 0)){
            $sup_id=" ";
        }
        
        //Check for PO Type: Direct or Distributor
        if(preg_match('/vlcc\s+personal\s+care\s+ltd/i',$obj->invoice_text)==1){
            $po_type = "Direct";
          //array_push($arr,$obj->master_dealer_id."<>".$obj->invoice_no);
        }else{
            $po_type = "Distributor";
        }
    
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->Intouch);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->dealername);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $sup_id);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->distname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, "-");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->vendorcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->name); 
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $TinNo);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $address);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $City);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $State);
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $State);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $po_type);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $inv_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $PO_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $Del_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $Exp_date[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $productgrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $obj->articleno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $EAN);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $itemname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $sku);
//        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $brand[0]);
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $category);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $obj->mrp);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $obj->CAR);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $rowCount, $obj->tot_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $rowCount, $obj->cost_price);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $rowCount, $obj->vat);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->amt);
        
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
