<?php ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
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
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Supplier id');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Supplier Name');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'vendor code');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Vendor Name');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'DC Location / Store Location');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'City');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'State');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'PO Date');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Delivery Date');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'PO Expiry Date');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'Article No');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'EAN');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Description');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'SKU Units');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'Brand Weikfield/EcoValley');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'Product Group');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', 'Product Category');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', 'MRP');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', 'CAR');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', 'Total QTY');
    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'BASIC Rate');
    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'VAT');
    $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'Total Amount');

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(60);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
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
    $objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AA')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('AB')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
    $objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    $colCount = 0;
    $rowCount = 2;
    $db = new DBConn();
    
    $issue_inv= array();
    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $st_dt = $srt_dt . " 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));

//    $query = "select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch,"
//           . " ip.shipping_id as sid, ip.dist_id as distid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,"
//           . "ipt.vat, ipt.amt, imd.name as dealername, idt.itemcode as articleno,imt.itemcode as EAN, "
//           . "imt.itemname as description,imt.sku as sku ,imt.product_code as productgrp, "
//           . "c.category as category,sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name from it_po ip, it_po_items ipt, it_master_dealers imd, it_dealer_items idt,"
//           . " it_master_items imt,it_category c, it_shipping_address sh where ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND "
//           . "idt.id= ipt.dealer_item_id AND imt.id= ipt.master_item_id and imt.category_id = c.id and"
//           . " imt.is_weikfield = 1  and ip.ctime >= $st_dt_db and ip.ctime <= $ed_dt_db and "
//           . "ip.status = " . POStatus::STATUS_PROCESSED . " and sh.id = ip.shipping_id order by dealername,ip.invoice_no";
// $query="select distinct ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, "
//        . "ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,"
//        . "ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.name as dealername,imd.show_code as showflag,"
//        . " idt.itemcode as articleno,idt.itemname as description,  d.supplier_id as distid, d.name as distname,"
//        . " sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode"
//        . " from  it_master_dealers imd, it_dealer_items idt, it_master_items imt,"
//        . "it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt "
//        . " where ip.ctime >= $st_dt_db and ip.ctime <=$ed_dt_db and ip.status =" . POStatus::STATUS_PROCESSED." and "
//        . "ip.id=ipt.po_id AND ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and idt.is_weikfield = 1 AND "
//        . " ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id order by dealername,ip.invoice_no;";
echo $query="select ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime as Intouch, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.name as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description,  d.supplier_id as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id  and  ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id and  ip.ctime between $st_dt_db and $ed_dt_db and ip.status not in (10) order by dealername,ip.invoice_no;";
//   echo $query."<br/>";
 
   $result = $db->getConnection()->query($query);
   $result1 = $db->getConnection()->query($query);
    while ($obj = $result->fetch_object()) {
        $inv_no= (string)$obj->invoice_no;
        $vatarr= array(0,5,12,18,28);
        $cal = doubleval(round(round($obj->qty) * $obj->cost_price,2)); 
        $diff = doubleval(round($cal - $obj->amt ,2));
        $per = doubleval(round(($diff / $cal) * 100 , 2));
//        if($obj->mrp > 50000 || $obj->qty > 10000 || $obj->tot_qty > 10000 || $per < -(DEF_TAX) || $obj->mrp < 0 || $obj->qty < 0 || $obj->tot_qty < 0 || $obj->cost_price < 0 || $obj->amt < 0 || $obj->vat <0 || $obj->vat >30){
        if($obj->mrp > 50000 || $obj->qty > 10000 || $obj->tot_qty > 10000 || $obj->mrp < 0 || $obj->qty < 0 || $obj->tot_qty < 0 || $obj->cost_price < 0 || $obj->amt < 0 || $obj->vat <0 || $obj->vat >30){   
        array_push($issue_inv, $inv_no);
//            print"\n Issue Found \n";
        }

        if(in_array($obj->vat,$vatarr)){
            //do nithing
          }else{
            print"in vat arr issue";
            array_push($issue_inv, $inv_no);
        }
    }
    //print_r($issue_inv);
    foreach($issue_inv as $invno){
//        print"in foreach\n";
        $selQ = "select * from it_po where invoice_no= '$invno' and status =". POStatus::STATUS_PROCESSED;
//        print"selQ=$selQ\n";
        $selobj = $db->fetchObject($selQ);
        if(isset($selobj)){
//            print"in sel obj\n";
            $id = $selobj->id;
            $filename= $db->safe($selobj->filename);
            //$path= $selobj->pdfname;
            $updateQ = "update it_po set status= ".POStatus::STATUS_ISSUE_AT_PROCESSING.", status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason = 'Wrong Data in Excel' where id = $id";
            $db->execUpdate($updateQ);
//            print"\n updateQ:$updateQ\n";
            $selqry ="select * from it_process_status where filename = $filename and status =".POStatus::STATUS_PROCESSED;
//            print"\n selqry=$selqry \n";
            $selpoobj=$db->fetchObject($selqry);
            if(isset($selpoobj)){
                $poid = $selpoobj->id;
                $path = $selpoobj->pdfname;
                $pdfname = movetoissue($path);
                $pdfname_db= $db->safe($pdfname);
                $updtQ = "update it_process_status set pdfname= $pdfname_db, status= ".POStatus::STATUS_ISSUE_AT_PROCESSING.", issue_reason = 'Wrong Data in Excel' where id = $poid";
//                print"\n updtQ:$updtQ\n";
                $db->execUpdate($updtQ);               
            }
        }       
    }
//    reset($result);
    $srno = 1;
    while ($obj = $result1->fetch_object()) {
        $inv_no= (string)$obj->invoice_no;
        if(in_array($inv_no, $issue_inv)){
            $EAN="";
            $sku="";  
            $productgrp=""; 
            $category= "";
            $address = $obj->address;
            $itemname = $obj->description;
            $brand = explode(" ", $itemname);
            $inv_no= (string)$obj->invoice_no;
            $PO_date = explode(" ", $obj->invoice_date);
            $Del_date = explode(" ", $obj->delivery_date);
            $Exp_date = explode(" ", $obj->expiry_date);
            $City = strtoupper($obj->city);  // providing city name in capital
            $State = strtoupper($obj->state); //providing state name in capital
            $mid=$obj->master_item_id;
    //        echo "mid------$mid \n";
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
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->Intouch);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->dealername);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $sup_id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->distname);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->vendorcode);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->name); 
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $address);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $City);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $State);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $inv_no);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $PO_date[0]);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $Del_date[0]);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $Exp_date[0]);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $obj->articleno);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $EAN);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount,$itemname);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $sku);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $brand[0]);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $productgrp);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $category);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $obj->mrp);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $obj->qty);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $rowCount, $obj->CAR);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $rowCount, $obj->tot_qty);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $rowCount, $obj->cost_price);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->vat);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $rowCount, $obj->amt);
          
            $colCount = 0;
            $rowCount++;
            $srno++;
        }
    }
    $nowtime = date('Y-m-d');
    $name = "issueExcle_" . $nowtime;
    $Ext = ".xls";
    //$Filename="/var/www/weikfield_DT/home/Parsers/ParsedPOXLS/".$name.$Ext;
    $Filename = DEF_ISSUE_EXL_PATH . $name . $Ext;
    print"\n*******".count($issue_inv);
    if(count($issue_inv)>0){
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($Filename);
    }else{
        print"\n No Issue Found\n";
    }  
//    print"<br> Issue in";
//    print_r($issue_inv);
//    print"<br>excel created";    
}catch (Exception $xcp) {
    print $xcp->getMessage();
}

function movetoissue($path){
    print"\n in move folder\n";
    $pathparts= pathinfo($path);
   print_r($pathparts);
    $srcdir = $pathparts['dirname'];
    $filename = $pathparts['filename'];
    $file_pdf = $pathparts['basename'];
    $file_text = $filename.".txt";
      
    $pparts=pathinfo($srcdir);
    //print_r($pparts);
    $destpath=$pparts['dirname'];
    $destdir = $destpath."/".statusFolder::getStatusMsg(POStatus::STATUS_ISSUE_AT_PROCESSING)."/";
    print"\n destdir:$destdir\n";
    print"\n src: $srcdir";
    if (!file_exists($destdir)) {
        mkdir($destdir,  0777 , true);
    }
    $pdfname=$destdir.$file_pdf;
    print"<br>pdf name=$pdfname<br><br>";
                
    $delete =  array();

    //first move pdf file
    if (copy($srcdir."/".$file_pdf, $destdir.$file_pdf)) {
        $delete[] =$srcdir."/".$file_pdf;
    }

    //than move txt file
    if (copy($srcdir."/".$file_text, $destdir.$file_text)) {
        $delete[] = $srcdir."/".$file_text;
    }

    // unlink files
    if(! empty($delete)){
        foreach ($delete as $file_pdf) {
            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
               unlink($file_pdf);
            }
        } 
    } 
    //print"<br>PDFNAME-MTP=$pdfname<br>";
    return $pdfname;
}

