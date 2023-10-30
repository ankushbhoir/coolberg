<?php 

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/core/strutil.php';
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
     $db = new DBConn();
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt = $today_dt . " 23:59:59 ";
    $ed_dt_db = $db->safe(trim($ed_dt));


$query="select ip.id as poid,ip.invoice_no,ip.ctime as Intouch, ip.shipping_id as sid, ipt.master_item_id ,imd.id as master_dealer_id,imd.displayname as chain_name, idt.itemcode as articleno, d.code as distid,sh.dc_name as name, bu.code as vendorcode,ipt.po_eancode,ipt.tot_qty,ipt.cost_price,ipt.mrp as po_mrp,ipt.amt,idt.is_vlcc from  it_master_dealers imd, it_dealer_items idt,it_shipping_address sh, it_distributors d, it_business_unit bu, it_po ip, it_po_items ipt  where ip.id=ipt.po_id  AND  ip.dist_id=d.id and d.bu_id= bu.id and sh.id = ip.shipping_id and ip.master_dealer_id=imd.id AND idt.id= ipt.dealer_item_id AND ip.ctime between $st_dt_db and $ed_dt_db and ip.status  in (11) and ip.is_active=1 order by chain_name,ip.invoice_no";  
   
    echo $query."\n";
     $db = new DBConn();
   $result = $db->getConnection()->query($query);
   $obj='';
    $srno = 1;
    while ($obj = $result->fetch_object()) {

   $sql_for_ean_sku="select * from it_ean_sku_mapping where ean='$obj->po_eancode' and master_dealer_id='$obj->master_dealer_id'";
       // echo "<br>";
         $result_ean_sku = $db->getConnection()->query($sql_for_ean_sku);
         $obj_sku = $result_ean_sku->fetch_object();
         if(!empty($obj_sku) ){


  if($obj_sku->mrp!=$obj->po_mrp)
          {
              $sql_to_check_mrp="select * from it_missing_mrp where po_no='$obj->invoice_no' and ean='$obj->invoice_no' and created_at like $st_dt_db";
              $obj_check_mrp= $db->fetchObject($sql_to_check_mrp);
              if(isset($obj_check_mrp) && !empty($obj_check_mrp)){
                echo "MRP EAN exist";
              }else {

             $query1="INSERT INTO `it_missing_mrp` (`id`, `po_no`, `ean`,`pomrp`,`master_mrp`,`site_code`,`chain_name`,`created_at`) VALUES (NULL, '$obj->invoice_no','$obj->po_eancode','".$obj->po_mrp."','".$obj_sku->mrp."','".$obj->vendorcode."','".$obj->chain_name."','".date('d-m-Y h:i:s')."')";
            $db->execInsert($query1);
            echo "<br>";
            echo "update it_po set status=9 where id=$obj->poid";
            echo "<br>";
            $db->execUpdate("update it_po set status=9 where id=$obj->poid");
          }

          }
   
              
    

              $sql_to_check_mrp="select * from it_missing_mrp where po_no='$obj->invoice_no' and ean='$obj->invoice_no'";
              $obj_check_base_mrp= $db->fetchObject($sql_to_check_mrp);
              if(isset($obj_check_base_mrp) && !empty($obj_check_base_mrp)){
                echo "dontcheck";
              }
              else{
         echo   $base_price=$obj_sku->purchase_rate_gst;
        echo "\n";
             echo $obj->tot_qty;
             echo "\n";
            echo $cal_cost=$base_price*$obj->tot_qty;
           
            
         echo "\n";
            
             $cal_cost = round($cal_cost,2);
           echo "\n";
           if($obj->master_dealer_id==7){
          echo  $test_rate=round($obj->cost_price,2);
            if($base_price==$obj->cost_price)
                $res=$base_price-$obj->cost_price;

           }
       else{
           echo  $test_rate=round($obj->amt,2);
           
            echo $res=  $test_rate-$cal_cost;
            echo "<br>";
            }
               if($res>5)
          {
              
              echo  $query12="INSERT INTO `it_missing_base_price` (`id`, `po_no`, `ean`,`porate`,`cal_rate`,`margine`,`mastermrp`,`site_code`,`chain_name`,`created_at`) VALUES (NULL, $obj->invoice_no,$obj->po_eancode,'".$cal_cost."','".$test_rate."','".$obj->margin."','".$mrp."','".$obj->vendorcode."','".$obj->chain_name."','".date('d-m-Y h:i:s')."')";
                 $db->execInsert($query12);
                 echo "update it_po set status=13 where id=$obj->poid";
              $db->execUpdate("update it_po set status=13 where id=$obj->poid");

          }
        }

}
}
?>