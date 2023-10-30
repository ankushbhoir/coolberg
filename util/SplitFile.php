<?php
require_once("../../it_config.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";
//require('fpdf.php');

$dt=date('Y-m-d');
$filePath = DEF_PROCESS_PATH."Walmart/newPOs/";

$db = new DBConn();

$files = scandir($filePath);
//$pdf = new FPDF();

//print_r($files);
//echo "<br><br>";
$po_arr = array();

foreach($files as $file){
   // $ext = pathinfo($file, PATHINFO_EXTENSION);
    $path_parts = pathinfo($file);
    $fileName = $path_parts['filename'];
//    echo $ext;
    if($path_parts['extension']=='txt'){
        $working_file = $file;
    //    echo $working_file;
        $data = file_get_contents($filePath.$working_file);
//        print_r($data);
        $num_lines = explode("\n",$data);
        $nolines = count($num_lines);
       /* for($i=0;$i<$nolines;$i++){
            echo "$num_lines[$i]<br><br>";
            $line = trim($num_lines[$i]);
            if(preg_match('/Purchase\s+Order\s+Number\s+(\S+)/',$line,$matches)){
                $po_no = trim($matches[1]);
                if(!in_array($po_no,$po_arr)){
                    array_push($po_arr,$po_no);
                }
            }
        }*/
      //  print_r($po_arr);
       
        
         $po_cnt = 0;
         for($i=0;$i<$nolines;$i++){
            $line = trim($num_lines[$i]);
            if(preg_match('/Total\s+Units\s+Ordered/',$line)==1){
                $po_cnt++;
            }
         }
         echo "PO cnt:".$po_cnt;
          $po_data = "";
        $cnt = 1;
        if($po_cnt>1){  
        for($i=0;$i<$nolines;$i++){
            $line = trim($num_lines[$i]);
//            echo "$i<>$line<br>";
          /*  if(preg_match('/Walmart\s+Purchase\s+Order/',$line)==1){
               $complete_po = $line."<br>".$po_data;
               //echo "$complete_po";
               $po_data = "";
            //   echo "***********************************************************";
            }else{
                $po_data .= "$line<br>";
            }*/
            
//            if(preg_match('/page\s+\d+\s+of\s+\d+/',$line)==1){
            
            if(preg_match('/Total\s+Units\s+Ordered/',$line)==1){
                $po_data .= "$line\n";
                $complete_po = $po_data;
            //   echo "$complete_po";
               $fname = $db->safe($fileName."_".$dt."_".$cnt.".pdf");
               $db->execInsert("insert into it_receivedpos set filename=$fname,type='application/pdf',master_dealer_id=7,createtime=now()");
               file_put_contents(DEF_PROCESS_PATH."/Walmart/newPOs/".$fileName."_".$dt."_".$cnt.".pdf", $complete_po);
               file_put_contents(DEF_PROCESS_PATH."/Walmart/newPOs/".$fileName."_".$dt."_".$cnt.".txt", $complete_po);
//               $pdf->Cell(40,10,$complete_po);
//               $pdf->Output();
               $po_data = "";
               $cnt++;
           //    echo "***********************************************************<br>";
            }else{
                $po_data .= "$line\n";
            }
                
            
        }
        $fileName_db = $db->safe($fileName);
$db->execUpdate("updte it_po set is_active=0,status=".POStatus::STATUS_DUPLICATE_PO." where filename=$fileName_db");
        }
    }
}




