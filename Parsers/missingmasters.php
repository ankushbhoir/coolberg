<?php
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
// ini_set('max_execution_time', 180);  
// ini_set('memory_limit', '-1'); //For allowing unlimited memory on server
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';



try {
    $db = new DBConn();
    $today_dt = date('Y-m-d');
    $srt_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
//    echo "Current time: ".$get_cuttent_time."<br>";
    $time = date('Y-m-d 14:00:00');
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
    //$st_dt_db=$db->safe('2021-10-29 00:00:00');
    //$ed_dt_db=$db->safe('2021-10-29 23:59:59');

     $destination = DEF_PROCESS_PATH;
    //original query mayur
      echo $query= "select ip.id,ip.invoice_no,ip.ctime as Intouch, ip.shipping_id as sid, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as chain_name, idt.itemcode as articleno, d.code as distid,sh.dc_name as name, bu.code as vendorcode,ipt.po_eancode,idt.is_vlcc from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id and ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status not in (10,3) and ip.is_active=1 order by chain_name,ip.invoice_no";
      
         //exit;
         echo "<br>";
       // exit;
    $result = $db->getConnection()->query($query);
    // print_r($result);
    // exit;
           if( $result->num_rows==0){
        echo "nothing to check";
        return 0;
}
    $srno = 1;

  // while ($obj = $result->fetch_object()) {
  //      echo  $obj->vendorcode;

 
  // }
              

         //exit;       
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
            $db->execUpdate("update it_po set site_code='".$obj->vendorcode."'where id=$obj->id");
            if(!empty($obj->po_eancode)){
             $srno;

           
        echo  $sql_for_ean_sku="select * from it_ean_sku_mapping where ean=$obj->po_eancode and master_dealer_id=$obj->master_dealer_id";
        
       // echo "<br>";
         $result_ean_sku = $db->getConnection()->query($sql_for_ean_sku);
         $obj_sku = $result_ean_sku->fetch_object();
         if(empty($obj_sku)){

           // echo "missing EAN :".$obj->po_eancode;
           // echo "<br>";
             // $ch = str_replace(" ", "_",$obj->foldername);
             //  echo $dirpath = $destination.$ch."/";
             //  exit;
           // echo "done";
              $db->execUpdate("update it_po set status=7 where id=$obj->id");

             $sql_to_check_ean="select po_no from it_missing_ean where po_no=$obj->invoice_no and site_code=$obj->vendorcode and ean=$obj->po_eancode";
         //   echo "<br>";
              $obj_check_ean = $db->fetchObject($sql_to_check_ean);
             // print_r($obj_check_ean);
             
             if(isset($obj_check_ean) && !empty($obj_check_ean)){
                echo "exist";

             }else{
                   $query1="INSERT INTO `it_missing_ean` (`id`, `po_no`, `ean`,`site_code`,`chain_name`,`created_at`) VALUES (NULL, $obj->invoice_no,$obj->po_eancode,'".$obj->vendorcode."','".$obj->chain_name."','".date('d-m-Y h:i:s')."')";
             $db->execInsert($query1);
             }
            
         }
      }
      else{
         $db->execUpdate("update it_po set status=7 where id=$obj->id");
      }
      if(!empty($obj->distid)){
        echo $sql_for_vendor_plant="select plant from it_vendor_plant_mapping where vendor_number=$obj->distid and master_dealer_id=$obj->master_dealer_id";
         
         $result_vendor_plant = $db->getConnection()->query($sql_for_vendor_plant);
         $obj_vendor_plant = $result_vendor_plant->fetch_object();
         if(empty($obj_vendor_plant)){
            echo "missing Vendor :".$obj->distid;
            echo "\n";
             
              $sql_to_check_vendor="select po_no from it_missing_vendor where po_no=$obj->invoice_no and site_code=$obj->vendorcode";
              $obj_vendor = $db->fetchObject($sql_to_check_vendor);
              if(isset($obj_vendor) && !empty($obj_vendor)){
                echo "exist";
         }  else{
              $query2="INSERT INTO `it_missing_vendor` (`id`, `po_no`, `vendor`,`site_code`,`chain_name`,`created_at`) VALUES (NULL, $obj->invoice_no,$obj->distid,'".$obj->vendorcode."','".$obj->chain_name."','".date('d-m-Y h:i:s')."')";
             $db->execInsert($query2);
         }
          $db->execUpdate("update it_po set status=5 where id=$obj->id");
     }

}
else{
    $db->execUpdate("update it_po set status=5 where invoice_no=$obj->id");
}
             if(!empty($obj->vendorcode)){

         $main_query="select category from it_ean_sku_mapping where ean ='".$obj->po_eancode."'";

         $result_id = $db->getConnection()->query($main_query);
         $obj_res = $result_id->fetch_object();
         // echo $obj_res->category;
         // exit;
         if(!empty($obj_res)){
          echo  $sql_for_shipparty="select ship_to_party from it_ship_to_party where site='".$obj->vendorcode."' and master_dealer_id=$obj->master_dealer_id ";
          echo "\n";
         $result_shipparty = $db->getConnection()->query($sql_for_shipparty);

         $obj_ship_party = $result_shipparty->fetch_object();
         if(empty($obj_ship_party)){
           echo "missing site :".$obj->vendorcode;
           // echo "<br>";
              $db->execUpdate("update it_po set status=6 where invoice_no=$obj->invoice_no and status!=7");
              $sql_to_check_site="select po_no from it_missing_site where po_no=$obj->invoice_no";
              $obj_site = $db->fetchObject($sql_to_check_site);
              if(isset($obj_site) && !empty($obj_site)){
                echo "exist";

         }else{
            echo $obj->vendorcode;
            echo "\n";
             $sql_to_ean="select po_no from it_missing_ean where po_no=$obj->invoice_no";
              $obj_ean_in_table = $db->fetchObject($sql_to_ean);
               if(isset($obj_site) && !empty($obj_site)){
                echo "do not insert";
               }else{
             echo $query3="INSERT INTO `it_missing_site` (`id`, `po_no`, `site`,`chain_name`,`created_at`) VALUES (NULL, $obj->invoice_no, '".$obj->vendorcode."', '".$obj->chain_name."','".date('d-m-Y h:i:s')."')";

             $db->execInsert($query3);
             }
         }
         }  
     }
     else{
         $db->execUpdate("update it_po set status=6 where id=$obj->id and status!=7");
     }
}
     

     
   
    }



} catch (Exception $xcp) {
    print($xcp->getMessage());
}
?>









