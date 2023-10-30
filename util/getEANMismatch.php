<?php
//require_once("../../it_config.php");
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

$db = new DBConn();
$arr = array();

$srt_dt = date('Y-m-d');
 $get_cuttent_time = date('Y-m-d H:i:s');
    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00";
    }else{
        $st_dt = $srt_dt . " 14:00:00";	
    }
$qry = "select id,master_dealer_id,invoice_no from it_po where ctime >= '$st_dt' and status not in (10,3)";
print $qry."\n";
$objs = $db->fetchAllObjects($qry);
if(isset($objs) && !empty($objs)){
foreach($objs as $obj){
    $invoice_id = $obj->id;
    $master_dealer_id = $obj->master_dealer_id;
    $inv_itm_id = $db->fetchAllObjects("select * from it_po_items where po_id=$invoice_id");
    $dealerName = $db->fetchObject("select name from it_master_dealers where id=$master_dealer_id");
    
    foreach($inv_itm_id as $inv_itm){
        $dealer_item_id = $inv_itm->dealer_item_id;
        
        if(isset($dealer_item_id) && !empty($dealer_item_id)){
            $dealer = $db->fetchObject("select * from it_dealer_items where id=$dealer_item_id");
            
            if(isset($dealer) && !empty($dealer)){
                $master_item_id = $dealer->master_item_id;
                
                if(isset($master_item_id) && !empty($master_item_id)){
                    $master_item = $db->fetchObject("select * from it_master_items where id=$master_item_id");
                    $master_eancode = $master_item->itemcode;
                    $po_eancode = $inv_itm->po_eancode;
                    
                    if(trim($po_eancode)!=trim($master_eancode)){
                     //   echo "PO eancode: $po_eancode<>Master eancode: $master_eancode\n";
                        array_push($arr,$dealerName->name."<>".$obj->invoice_no."<>".$po_eancode."<>".$master_eancode);
                    }
                }else{
                  //  echo "Master articleno mapping remaining: $dealer->itemcode<>$dealer->master_dealer_id\n";
                }
            }else{
              //  echo "Dealer item id not exist in db: $dealer_item_id\n";
            }
        }else{
           // echo "Dealer item id not in PO items: $inv_itm->dealer_item_id\n";
        }
    }
}
}else{
  //  echo "PO not present in current date\n";
}


$sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('EAN Mismatch');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'PO EAN Code');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Master EAN Code');
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
 
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
    
     $colCount = 0;
    $rowCount = 2;
    
    foreach($arr as $ar){
        $data = explode("<>",$ar);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $data[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $data[1]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $data[2]);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $data[3]);
        $colCount = 0;
        $rowCount++;
    }

 $nowtime = date('Y-m-d');
    $name = "EANMisamtch_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_EAN_MISMATCH_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
