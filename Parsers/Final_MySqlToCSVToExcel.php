<?php
   ini_set('max_execution_time', 120);  
 
   require_once("../../it_config.php");
   require_once "lib/db/DBConn.php";
   require_once "lib/core/Constants.php";
   require_once "lib/php/Classes/PHPExcel.php";
   require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

function MySqlToCSV($csvFilename){
        $db = new DBConn();
      //$sheetIndex=0;
       // $query = "SELECT * FROM it_users";
        $today_dt = date('Y-m-d');
        //$st_dt = $today_dt." 00:00:00 ";
        $st_dt = "2016-09-01 00:00:00 ";
        $st_dt_db = $db->safe(trim($st_dt));
        $ed_dt = $today_dt . " 23:59:59 ";
        $ed_dt_db = $db->safe(trim($ed_dt));
        
       //--old $query = "select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, imd.name as dealername, idist.name as distname, idist.code, idist.address, idist.city, idist.state, idt.itemcode as articleno,imt.itemcode as EAN, imt.itemname as description,imt.sku as sku ,imt.product_code as productgrp, c.category as category from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_category c where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and imt.category_id = c.id and imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and ip.status = " . POStatus::STATUS_PROCESSED . " order by dealername,ip.invoice_no"; //and ip.master_dealer_id in(2,3,4,5,8,12)
        $query = "select ip.ctime as Intouch,  imd.name as dealername, idist.name as distname, idist.code,  idist.address, idist.city, idist.state, ip.invoice_no, ip.invoice_date,  ip.delivery_date, ip.expiry_date, idt.itemcode as articleno, imt.itemcode as EAN, imt.itemname as description, imt.sku as sku , imt.product_code as productgrp, c.category as category, ipt.mrp, ipt.qty , ipt.pack_type as CAR, ipt.tot_qty, ipt.cost_price, ipt.vat, ipt.amt from it_po ip, it_po_items ipt, it_master_dealers imd, it_distributors idist, it_dealer_items idt, it_master_items imt,it_category c where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND ip.dist_id=idist.id AND idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and imt.category_id = c.id and imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and ip.status = " . POStatus::STATUS_PROCESSED . " order by dealername,ip.invoice_no"; //and ip.master_dealer_id in(2,3,4,5,8,12)
        $result = $db->getConnection()->query($query);
        
        if (!$result) die('Couldn\'t fetch records');
        
        $headers = $result->fetch_fields();
        foreach($headers as $header) {
             $head[] = $header->name;
        }
        
        $fp = fopen($csvFilename, 'w');
        if ($fp && $result) {
            // header('Content-Type: text/csv');
            //header('Content-Disposition: attachment; filename="Krish.csv"');
            // header('Pragma: no-cache');
            //header('Expires: 0');
            
           $header = array('Sr.No','Intouch','Chain Name','Vendor Name','TIN No.','DC Location / Store Location','City','State','PO Number','PO Date','Delivery Date','PO Expiry Date','Article No','EAN','Description','SKU Units','Brand Weikfield/EcoValley','Product Group','Product Category','MRP','Qty','CAR','Total QTY','BASIC Rate','VAT','Total Amount'); 
          // array_unshift($head, "Sr.No");
          fputcsv($fp, array_values($header)); 
          $srNo=1;
          while ($row = $result->fetch_array(MYSQLI_NUM)) {
              $brand=array('');
              
            foreach ($row as $k => &$item) {
                if($k==4){
                    $address = str_replace("                                          ", " ", (string)$row[4]);   //for address at index 4 in array $row  
                    $row[4]=$address;
                }
                else if($k==12){                            //EAN index in $row  = 12
                    $ean=$row[12];                                                  
                    $row[12]=$ean;
                }
                else if($k==13){                            //description at index 13
                    $prodname = (string)$row[13];
                    $brand = explode(" ", $prodname);       //extracted brand from productname i.e description    
                    if($brand[0]==""){
                        $brand=array(' ');
                    }
                    else{
                        $brand=array($brand[0]);
                    } 
                  
                }
                else if ($k==8) {
                    $PO_date = explode(" ", (string)$row[8]);    //PO Date at index 4 
                    $row[8]=$PO_date[0];                         //taken only date in PO Date(time removed) 
                }
                else if($k==9){
                     $Del_date = explode(" ", (string)$row[9]);  //Delivery Date at index 9    
                     $row[9]=$Del_date[0];                       //taken only date in PO Date (time removed)
                }
                else if($k==10){
                    $Exp_date = explode(" ", (string)$row[10]);
                    $row[10]=$Exp_date[0];
                }
                else if($k==5){
                    $City = strtoupper((string)$row[5]);        // providing city name in capital
                    $row[5]=$City;
                }
                else if($k==6){
                    $State = strtoupper($row[6]);               //providing state name in capital
                    $row[6]=$State;
                }else if($k==3){
                     if (preg_match("/^[0-9]/", trim((string)$row[3]))) {   
                              $TinNo = $row[3];
                     } else {
                              $TinNo = " ";
                     }
                     $row[3]=$TinNo;
                }
                
                /*if ($k==9) {
                  $date=explode(" ",(string)$row[9]);
                  $row[9]=$date[0]; 
                }*/
            }
          array_splice( $row, 15, 0, $brand );
          array_unshift($row, $srNo);   
          fputcsv($fp, array_values($row));
          $srNo++;
        }
        fclose($fp);
       $db->closeConnection();
       }
      return 0;
    }
    
    function CSVToExcel($csvFilename,$Filename){
        
        $objReader = PHPExcel_IOFactory::createReader('CSV');
        // If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
        //$objReader->setDelimiter("\t");
        // If the files uses an encoding other than UTF-8 or ASCII, then tell the reader
        //$objReader->setInputEncoding('UTF-16LE');
        $objPHPExcel = $objReader->load($csvFilename);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
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
        
        
        $styleArray = array(
             'font' => array(
                'bold' => false,
       //       'color' => array('rgb' => 'FF0000'),
                'size' => 10,
          ));
        $headerstyleArray = array(
            'font' => array(
                 'bold' => true,
                 'color' => array('rgb' => '008080'),
                 'size' => 11,
        ));
        $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($headerstyleArray);
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
        $objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
        //$objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        
        //$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
       
        $objWriter->save($Filename);
        return "Success";
    }

try{
     $nowtime = date('Y-m-d');
        $name = "MTD_" . $nowtime;
        $Ext = ".xls";
        //$DEF_PARCED_EXL_PATH="";
        $csvFileName=DEF_PARSED_EXL_PATH . $name .".csv";
        $Filename = DEF_PARSED_EXL_PATH . $name . $Ext;
    $succ=1;
      $file_exists=file_exists($csvFileName);
           if($file_exists)
               chmod($csvFileName,0777);
           ini_set('memory_limit', '-1'); //For allowing unlimited memory on server

           $succ = MySqlToCSV($csvFileName);
           $file_exists=file_exists($Filename);
           if($file_exists)
               chmod($Filename,0777);
           
           if( $succ == 0) {
                  
                  $succ1= CSVToExcel($csvFileName,$Filename);
                  print "$succ1";
         
           }
 
    

}catch(Exception $xcp){
      print $xcp->getMessage();
 }
?>