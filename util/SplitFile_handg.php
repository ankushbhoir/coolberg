<?php
// require_once("../../it_config.php");
require_once(ROOTPATH."it_config.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";
//require('fpdf.php');

$dt=date('Y-m-d');
$filePath = DEF_PROCESS_PATH."H&G/newPOs/";

$db = new DBConn();

$files = scandir($filePath);
$po_arr = array();

foreach($files as $file){   
    $path_parts = pathinfo($file);
    $fileName = $path_parts['filename'];

    if($path_parts['extension']=='txt'){
        $working_file = $file;
    
        $data = file_get_contents($filePath.$working_file);
        $num_lines = explode("\n",$data);
        $nolines = count($num_lines);
         $po_cnt = 0;
         for($i=0;$i<$nolines;$i++){
            $line = trim($num_lines[$i]);
            if(preg_match('/HEALTH\s+\&\s+GLOW\s+PRIVATE\s+LIMITED /',$line)==1){
                $po_cnt++;
            }
         }
         
          $po_data = "";
        $cnt = 1;
        if($po_cnt>1){  
        for($i=0;$i<$nolines;$i++){
            $line = trim($num_lines[$i]);
            
            if(preg_match('/Buyer\'s\s+Signature\s+Vendor\'s\s+Signature/',$line)==1){
                $po_data .= "$line\n";
                $i++;$i++;
                $complete_po = $po_data;
               echo "$complete_po";
               $fname = $db->safe($fileName."_".$dt."_".$cnt.".pdf");
               $db->execInsert("insert into it_receivedpos set filename=$fname,type='application/pdf',master_dealer_id=22,createtime=now()");
               file_put_contents(DEF_PROCESS_PATH."/H&G/newPOs/".$fileName."_".$dt."_".$cnt.".pdf", $complete_po);
               file_put_contents(DEF_PROCESS_PATH."/H&G/newPOs/".$fileName."_".$dt."_".$cnt.".txt", $complete_po);
               $po_data = "";
               $cnt++;
               echo "***********************************************************<br>";
            }else{
                $po_data .= "$line\n";
            }
                
            
        }
        $fileName_db = $db->safe($fileName);
$db->execUpdate("updte it_po set is_active=0,status=".POStatus::STATUS_DUPLICATE_PO." where filename=$fileName_db");
        }
    }
}




