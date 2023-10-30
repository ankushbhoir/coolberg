<?php
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
// ini_set('max_execution_time', 180);  
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";


try {
    $db = new DBConn();
    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 00:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 00:00:00 ";
    //$st_dt = $srt_dt . " 00:00:00 ";
    }
      // $st_dt = "2020-05-26 00:00:00 ";
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));


    //original query mayur
//$st_dt_db=$db->safe('2020-08-30 15:00:00');
//$ed_dt_db=$db->safe('2020-08-30 23:59:59');


         $query="select ip.id,ip.invoice_no, ip.invoice_date, ip.delivery_date, ip.expiry_date, ip.tqty, ip.tamt,ip.ctime, ip.shipping_id as sid,ipt.mrp, ipt.qty,ipt.tot_qty, ipt.ttk_qty, ipt.ttk_uom,ipt.pack_type as CAR,ipt.cost_price,ipt.vat, ipt.amt, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as dealername,imd.show_code as showflag, idt.itemcode as articleno,idt.itemname as description, d.code as distid, d.name as distname, sh.dc_address as address,sh.dc_state as state,sh.dc_city as city,sh.dc_name as name, bu.code as vendorcode,sh.customer_code,ipt.po_itemname as description_po,ipt.po_eancode,idt.is_vlcc, istp.ship_to_party, ivpm.plant, iesm.sku, ipt.po_hsn
    
    from  
    it_master_dealers imd, 
    it_dealer_items idt,
    it_shipping_address sh, 
    it_distributors d, 
    it_business_unit bu, 
    it_po ip, 
    it_po_items ipt, 
    it_ship_to_party istp, 
    it_ean_sku_mapping iesm, 
    it_vendor_plant_mapping ivpm  

    where  
    ivpm.master_dealer_id = imd.id and 
    ivpm.vendor_number = d.code and 
    iesm.ean = ipt.po_eancode and 
    istp.master_dealer_id = imd.id and 
    istp.site = bu.code and 
    ip.id=ipt.po_id  AND  
    ip.dist_id=d.id and 
    d.bu_id= bu.id and 
    sh.id = ip.shipping_id and 
    ip.master_dealer_id=imd.id AND 
    idt.id= ipt.dealer_item_id AND 
    ip.ctime between $st_dt_db and 
    $ed_dt_db and 
    ip.status not in (10,3,7,5,4,8,21) and 
    ip.is_active=1 

    order by dealername,ip.invoice_no";

    $result = $db->getConnection()->query($query);
     if( $result->num_rows==0)
       exit;

    $srno = 1;
$cdata = '<br>';

               


                
                // $header = $envelope->addChild("HEADER");
                // $header->addChild("TALLYREQUEST", "Import Data");
                // $body = $envelope->addChild("BODY");
                // $importdata = $body->addChild("IMPORTDATA");
                // $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                // $reqdesc->addChild("REPORTNAME", "Sales Voucher");
                // $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                // $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                // //echo "gererrere";
                // $reqdata = $importdata->addChild("REQUESTDATA");


    while ($obj = $result->fetch_object()) {
        $EAN="";
        $sku="";  
        $productgrp=""; 
        $category= "";
        $address = $obj->address;//str_replace("                                          ", " ", $obj->address);
        $itemname_po = $obj->description;
     //   $itemname_po = $obj->description_po;
        $inv_no= (string)$obj->invoice_no;
        $PO_date = explode(" ", $obj->invoice_date);
        $Del_date = explode(" ", $obj->delivery_date);
        $newDate = date("d/m/Y", strtotime($obj->delivery_date));  
        $Exp_date = explode(" ", $obj->expiry_date);

        $email_date = explode(" ", $obj->ctime);
        $new_email_date= date("d/m/Y", strtotime($obj->ctime));
        $po_date123 = date("d/m/Y", strtotime($obj->invoice_date));
        $City = strtoupper($obj->city);  // providing city name in capital
        $State = strtoupper($obj->state); //providing state name in capital
        $State_str = str_replace(" ","",$State);
        $is_vlcc = $obj->is_vlcc;
        $flag = " ";


        
        $mid=$obj->master_item_id;
    
        

        $obj->ttk_uom='';


            if($obj->master_dealer_id==5){
                $cal_quantity=$obj->qty;
            }
            else{
                
                $cal_quantity=$obj->ttk_qty;
            }
        // print_r($obj->name);
            $test="\n";

                $tvat=$obj->vat/2;
                 echo $query3="INSERT INTO `it_ttk_master_xml` (`id`, `Chain_Name`, `Document_Type`, `Site`, `TTK_ShipTo_Party`, `EAN_Number`, `TTK_SKU`, `Delivery_Date`, `Email_Date`, `PO_Number`, `PO_Date`, `Total_Base_Cost`, `Net_Price`, `Vendor_Code`, `TTK_Plant_Code`, `Cust_Article_Number`, `Cust_HSN_Code`, `Cust_Quantity`, `Cust_UOM`, `TTK_Quantity`, `TTK_UOM`, `MRP`, `CGST`, `SGST`, `IGST`, `UGST`, `CESS`, `po_no`, `created_at`) 

     VALUES (NULL, 
     '".$obj->dealername."',
      NULL, 
      '".$obj->vendorcode."',
       '$obj->ship_to_party', 
       '$obj->po_eancode', 
       '$obj->sku', '$newDate', 
       '$new_email_date', 
       '$inv_no',
        '$po_date123', 
        '".$obj->cost_price."',
         '".$obj->cost_price*$cal_quantity."',  
         '$obj->distid', 
         '$obj->plant',
          '$obj->articleno', 
          '$obj->po_hsn', 
          '$obj->qty',
         '$obj->CAR', 
      '$obj->ttk_qty',
     '$obj->ttk_uom', 
        '$obj->mrp', 
        '$tvat', 
        '$tvat', 
        '0', 
        '0', 
        '0',
        '$obj->id' ,
         '".date('y-m-d h:i:s')."');
";
echo "<br>";
echo $srno;
echo "\n";
// echo $query3;
// exit;
             $db->execInsert($query3);



        
    }

   // header('Content-Disposition: attachment;filename='."TTK_Daily_PO.xml");
    
    // header('Content-Type: text/xml');
    // print($ENVELOPE->asXML());
    // $fname="aniket.xml";
    // header('Content-Disposition: attachment;filename=' . $fname);
    // header('Content-Type: application/xml; charset=utf-8');
   

   
} catch (Exception $xcp) {
    print($xcp->getMessage());
}
?>