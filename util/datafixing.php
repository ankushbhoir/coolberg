<?php
require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

$db = new DBConn();
$arr = array();
$arr1 = array();

$srt_dt = date('Y-m-d');
 $get_cuttent_time = date('Y-m-d H:i:s');
    $time = date('Y-m-d 14:00:00');
    if($get_cuttent_time < $time){
         $st_dt = $srt_dt . " 00:00:00 ";
    }else{
        $st_dt = $srt_dt . " 14:00:00 ";	
    }
$qry = "select id,master_dealer_id,invoice_no,tqty,tamt,tvat_amt,invoice_text,filename,ctime from it_po where ctime >= '$st_dt' and status not in (10)";
print $qry;
$objs = $db->fetchAllObjects($qry);
//print_r($objs);
foreach($objs as $obj){
//    echo "<br>walmart bahu233::".$obj->tamt;
    $mid = $obj->master_dealer_id;
    if($mid == 2){//future retail
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);        
        for ($lineno = 0; $lineno < $numlines; $lineno++){
            $line = trim($lines[$lineno]);
            $regex1 = "/Terms\s*&\s*Conditions\s*:\s*/";
             if(preg_match($regex1,$line,$matches)){
                $end_line_no = $lineno;   
                for($i= $end_line_no; $i>0; $i--){
                    $line1 = trim($lines[$i]);
                    $regex2="/Total/";
                    if(preg_match($regex2,$line1,$matches)){
                        $line = trim($lines[$i]);
                        $regex = "/Total\s+(\d\S+)/";
                        if(preg_match($regex,$line,$matches)){
                            $invno = str_replace(",","",$matches[1]);
                            $invarr = explode(".",$invno);
                            $qty= $invarr[0];
                            if($qty==$obj->tqty){
                            //    print "\n $qty==$obj->tqty";
                            }else{                               
                             //    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";                                
                            }
                            $diff = abs($qty-$obj->tqty);
                            if(trim($diff)>=1){
                                array_push($arr1,$obj->id);
                            }
                            array_push($arr,"Future Retail<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$qty);
                            break;
                        }                         
                        break;    
                    }
                }
            }           
        }
    }
    if($mid == 3){// ABRL Super
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
             $regex1 = "/Total\s*Qty\s*:\s*(\d\S*)\s+Total\s*:\s+\d\S*\s+\d\S*/i";
            if(preg_match($regex1,$line,$matches)){
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
               // print "\n $invarr[0]==$obj->tqty";
                }else{
                 //   print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";                    
                }
                 $diff = abs($qty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"ABRL Super<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$qty);
                break;
            }
        }
    }
    if($mid == 3){// ABRL Super
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines); 
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Qty\s*:\s*(\d\S*)/i";
            if(preg_match($regex1,$line,$matches)){
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
              //  print "\n $invarr[0]==$obj->tqty";
                }else{
               //     print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";                    
                }
                $diff = abs($qty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"ABRL Hyper<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$qty);
                break;
            }
        }
    }
     if($mid == 5){// Reliance
         
              
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Order\s*Value\s*:\s*INR\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
               //  $obj->tvat = $obj->tamt*VAT/100;
                
//                $obj->tamt = $obj->tamt*(1+18/100);
                
//                echo"<br><br><br><br><br><br><br><br>Tamount".$obj->tamt;
                print_r( $matches);
                if(round($tamt)==round($obj->tamt)){                     
           //     print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
//                 $diff = abs($tamt-$obj->tamt);
                                
                 $amt_vat = $obj->tamt+$obj->tvat_amt;
                 $diff = abs($tamt - $amt_vat);
                 
//                 echo"<br><br><br><br><br><br><br><br>ErrorTamount.amt_vat:".$amt_vat;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.tamt".$tamt;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.diff".$diff;
                
                if(trim($diff)>=1){
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:diff".$diff;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount::obj->tamt".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:obj->tvat_amt".$obj->tvat_amt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:amt_vat".$amt_vat;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:tamt".$tamt;
                    array_push($arr1,$obj->id);
                }
//                array_push($arr,"Vishal<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                 array_push($arr,"Reliance<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
   
    
    
    
//         
//        $invtext = $obj->invoice_text;
//        $lines = explode("\n", $invtext);
//        $numlines = count($lines);  
//        for ($lineno = 0; $lineno < $numlines; $lineno++) {
//            $line = trim($lines[$lineno]);
//            $regex1 = "/Total\s*Order\s*Value\s*:\s*INR\s*(\d\S*)/";
//            if(preg_match($regex1,$line,$matches)){
//                $invamt = str_replace(",","",$matches[1]);
//                $tamt=$invamt;                
//                $obj->tamt = $obj->tamt*(1+18/100);
//                if(round($tamt)==round($obj->tamt)){                     
//              //  print "\n $tamt==$obj->tamt";
//                }else{
//                //    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
//                }
//                $diff = abs($tamt-$obj->tamt);
//                if(trim($diff)>=1){
//                    array_push($arr1,$obj->id);
//                }
//                array_push($arr,"Reliance<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
//                break;
//            }
//        }
    }

     if($mid == 55){// Reliance
         
              
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Order\s*Value\s*:\s*INR\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
               //  $obj->tvat = $obj->tamt*VAT/100;
                
//                $obj->tamt = $obj->tamt*(1+18/100);
                
//                echo"<br><br><br><br><br><br><br><br>Tamount".$obj->tamt;
                print_r( $matches);
                if(round($tamt)==round($obj->tamt)){                     
           //     print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
//                 $diff = abs($tamt-$obj->tamt);
                                
                 $amt_vat = $obj->tamt+$obj->tvat_amt;
                 $diff = abs($tamt - $amt_vat);
                 
//                 echo"<br><br><br><br><br><br><br><br>ErrorTamount.amt_vat:".$amt_vat;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.tamt".$tamt;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.diff".$diff;
                
                if(trim($diff)>=1){
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:diff".$diff;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount::obj->tamt".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:obj->tvat_amt".$obj->tvat_amt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:amt_vat".$amt_vat;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:tamt".$tamt;
                    array_push($arr1,$obj->id);
                }
//                array_push($arr,"Vishal<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                 array_push($arr,"Reliance<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
   
    
    
    

    }
     if($mid == 56){// Reliance
         
              
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Order\s*Value\s*:\s*INR\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
               //  $obj->tvat = $obj->tamt*VAT/100;
                
//                $obj->tamt = $obj->tamt*(1+18/100);
                
//                echo"<br><br><br><br><br><br><br><br>Tamount".$obj->tamt;
                print_r( $matches);
                if(round($tamt)==round($obj->tamt)){                     
           //     print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
//                 $diff = abs($tamt-$obj->tamt);
                                
                 $amt_vat = $obj->tamt+$obj->tvat_amt;
                 $diff = abs($tamt - $amt_vat);
                 
//                 echo"<br><br><br><br><br><br><br><br>ErrorTamount.amt_vat:".$amt_vat;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.tamt".$tamt;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.diff".$diff;
                
                if(trim($diff)>=1){
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:diff".$diff;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount::obj->tamt".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:obj->tvat_amt".$obj->tvat_amt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:amt_vat".$amt_vat;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:tamt".$tamt;
                    array_push($arr1,$obj->id);
                }
//                array_push($arr,"Vishal<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                 array_push($arr,"Reliance<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
   
    
    
    

    }
      if($mid == 7){// Walmart
          echo "walmart bahu in:".$obj->tamt;
          $tamt=0;
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+Order\s+Amount\s+\(\s*Before\s*Adjustments\s*\)\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
//                    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tqty";
                    //array_push($arr,"Walmart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                }
                //echo "walmart bahu1".$obj->tamt;
                 $diff = abs($tamt-$obj->tamt);
                 
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Walmart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);                
                break;
            }else if(preg_match('/Total\s+PO\s+AMOUNT\s+including\s+Taxes\s+\(INR\)\s+(\S+)/',$line,$matches)){                   
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
//                    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tqty";                   
                }  
                echo "walmart bahu2".$obj->tamt;
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Walmart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);                
                break;
            }else if(preg_match('/total\s+units\s+ordered\s*:?\s*\d+\s*\(Before\s+Adj\s*\)\s*:?\s+(\d\S*)/i',$line,$matches)){
                 $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
//                    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tqty";                   
                }  
                echo "walmart bahu3".$obj->tamt;
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Walmart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);                
                break;
            }
             
        }
    }
    
       if($mid == 8){// Spencer's
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
        $regex = "/Total\s+\d\S+\s+\d\S*\s+\d\S*\s+\d\S*\s+(\d\S*)/i";
        $regex1 = "/Total\s+\d\S*\s+(\S+)/i";
            if(preg_match($regex,$line,$matches) || preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                //$obj->tamt = $obj->tamt*(1+18/100);
                if(round($tamt)==round($obj->tamt)){
//                print "\n $tamt==$obj->tamt";
                }else{
                    print"\n Issue invoice:-    $obj->invoice_no  :::::::Inv id: $obj->id     :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                                   
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                 array_push($arr,"Spencers<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
      
    if($mid==11){ //Max
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:\s*\d+\.\d+\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
             //   print "\n $tamt==$obj->tamt";
                }else{
                 //   print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Max<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
      if($mid == 14){// Metro
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/TOTAL\s*AMOUNT\s*(\d\S*)/i";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=round($invamt,2);
                if($tamt==$obj->tamt){                     
            //    print "\n $tamt==$obj->tamt\n";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt\n";
                   // array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=3){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);                
                break;
            }else if(preg_match('/Total\s+Value\s*\(\s*excl.\s*Tax\s*\)\s*:?\s*(\S+)/i',$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=round($invamt,2);
                if($tamt==$obj->tamt){                     
           //     print "\n $tamt==$obj->tamt\n";
                }else{
              //      print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime   $tamt==$obj->tamt\n";
                 //   array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=3){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);                
                break;
            }else if(preg_match('/Total\s+order\s+amount\s+INR\s*:?\s*(\d\S+)/i',$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=round($invamt,2);
                if($tamt==$obj->tamt){                     
            //        print "\n $tamt==$obj->tamt\n";
                }else{
           //         print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime   $tamt==$obj->tamt\n";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);                
                break;
            }
            
        }
    }    
     if($mid == 15){// Trent
        $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);    
        $qty = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                print_r($qty);
               echo"qty--------------------------:".$obj->tqty;
                if($qty==$obj->tqty){                      
               print "\n ffffffffff $invarr[0]==$obj->tqty";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";
                }
                 $diff = abs($qty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Trent<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$qty);
                break;
            }
        }
    }
    if($mid == 16){// FIORA
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines); 
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
           //     print "\n $invarr[0]==$obj->tqty";
                }else{
           //         print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";                
                }
                 $diff = abs($qty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                 array_push($arr,"FIORA<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$qty);
                 break;
            }
        }
    }
      
     if($mid==20){ //Aadhar
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+Amount\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
            //    print "\n $tamt==$obj->tamt";
                }else{
           //         print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Aadhar<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
    
     if($mid==21){ //Ratandeep
         
         
       
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Grand\s+Total\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
//                echo "gmmm :".$obj->tamt;
                $obj->tamt = $obj->tamt*(1+18/100);
//                echo "<br> gmmmssss :".$obj->tamt;
                   // $obj->tamt = $obj->tamt-2;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n gmmm $tamt==$obj->tamt";                
                }else{
             //       print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=3){
                    echo "gmm bro";
                    array_push($arr1,$obj->id);
                }
                print_r($arr1);
                echo "<br>gmm brooo";
                array_push($arr,"Ratandeep<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
     if($mid==22){ //H&G
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
            //    print "\n $tamt==$obj->tamt";
                }else{
             //       print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"H&G<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
    /* if($mid==23){ //Tesco
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Grand\s+Total\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                $obj->tamt = $obj->tamt*(1+18/100);
                if(round($tamt)==round($obj->tamt)){                     
                print "\n $tamt==$obj->tamt";
                }else{
                    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Ratandeep<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }*/
   if($mid==24){ //H&B
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Grand\s+Total\s+Quantity\s*\=?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $tqty = str_replace(",","",$matches[1]);                
                if(round($tqty)==round($obj->tqty)){                     
            //    print "\n $tqty==$obj->tqty";
                }else{
           //         print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tqty==$obj->tqty";                    
                }
                 $diff = abs($tqty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"H&B<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$tqty);
                break;
            }
        }
    }
     
      if($mid==26){ //Vishal
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);  
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Gross\s+Value\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
               //  $obj->tvat = $obj->tamt*VAT/100;
                
//                $obj->tamt = $obj->tamt*(1+18/100);
                
//                echo"<br><br><br><br><br><br><br><br>Tamount".$obj->tamt;
                print_r( $matches);
                if(round($tamt)==round($obj->tamt)){                     
           //     print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
//                 $diff = abs($tamt-$obj->tamt);
                                
                 $amt_vat = $obj->tamt+$obj->tvat_amt;
                 $diff = abs($tamt - $amt_vat);
                 
//                 echo"<br><br><br><br><br><br><br><br>ErrorTamount.amt_vat:".$amt_vat;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.tamt".$tamt;
//                echo"<br><br><br><br><br><br><br><br>ErrorTamount.diff".$diff;
                
                if(trim($diff)>=1){
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:diff".$diff;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount::obj->tamt".$obj->tamt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:obj->tvat_amt".$obj->tvat_amt;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:amt_vat".$amt_vat;
//                    echo"<br><br><br><br><br><br><br><br>ErrorTamount:tamt".$tamt;
                    array_push($arr1,$obj->id);
                }
//                array_push($arr,"Vishal<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                array_push($arr,"Vishal<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$amt_vat."<>".$tamt);
                break;
            }
        }
    }
    
    if($mid==27){ //lullu
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines); 
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+Invoice\s+Cost\s*:\s*(\d\S*)/i";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){     
            //        print "\n $tamt==$obj->tamt";
                }else{
             //       print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Lullu<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
     if($mid==28){ //Guardian
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Order\s+Amount\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
           //         print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Guardian<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
     if($mid==30){ //WH Smith
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\d\S*\s+\d\S*\s+\d\S*\s+\d\S*\s+(\d\S*)/i";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
           //         print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                   
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                 array_push($arr,"WH Smith<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
      if($mid==31){  //bismi
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines); 
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
            //    print "\n $tamt==$obj->tamt";
                }else{
           //         print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Bismi<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
   /* if($mid==32){  //Vijetha
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines); 
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
                print "\n $tamt==$obj->tamt";
                }else{
                    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Bismi<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }*/
    
       if($mid==33){ //CP wholesale
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\S+\s+\S+\s+\S+\s+\S+\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
          //      print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"CP Wholesale<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
     if($mid==40){ //TNSI
       $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\d\S*\s+\d\S*\s+\d\S*\s+(\d\S*)/i";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
            //        print "\n $tamt==$obj->tamt";
                }else{
           //         print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"TNSI<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
      if($mid==41){ //Nyasa
       $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:?\s*(\d\S*)\s+\d\S*/i";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tqty=$invamt;
                if(round($tqty)==round($obj->tqty)){                     
            //        print "\n $tqty==$obj->tqty";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tqty==$obj->tqty";                    
                }
                 $diff = abs($tqty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Nyasa<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$tqty);
                break;
            }
        }
    }        
       if($mid==44){ //Bakshish
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Net\s+PO\s+Amount\s*:\s*(\S+)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
            //    print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                   
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                 array_push($arr,"Bakshish<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }
    
     if($mid==46){ //SNNR
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/TOTAL\s+QTY\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tqty=$invamt;
                if(round($tqty)==round($obj->tqty)){                     
             //   print "\n $tqty==$obj->tqty";
                }else{
             //       print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tqty==$obj->tqty";                    
                }
                 $diff = abs($tqty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"SNNR<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$tqty);
                break;
            }
        }
    }
     if($mid==47){ //Market 99
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/TOTAL\s+QTY\s*:?\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
            //    print_r($matches);
                $invamt = str_replace(",","",$matches[1]);
                $tqty=$invamt;
                if(round($tqty)==round($obj->tqty)){                     
            //         print "\n $tqty==$obj->tqty";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tqty==$obj->tqty";                    
                }
                 $diff = abs($tqty-$obj->tqty);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"Market 99<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tqty."<>".$tqty);
                break;
            }
        }
    }
    
     if($mid==49){ //V mart
         $invtext = $obj->invoice_text;
        $lines = explode("\n", $invtext);
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Net\s+Amount\s*:?\s*(\d\S*)/i";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
		$obj->tamt = $obj->tamt*(1+18/100);
                if(round($tamt)==round($obj->tamt)){                     
            //          print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"V Mart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }
        }
    }  

         if($mid==54){ //D mart
         $invtext = $obj->invoice_text;
         $lines = explode("\n", $invtext);
       
        $numlines = count($lines);
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            echo $lineno."<>". $line ;
           echo "<br>";
            $regex1 = "/Total\s+\S+\s+(\d\S*)/mi";
           // print_r($matches);
           
            if(preg_match($regex1,$line,$matches)){
                print_r($matches);
               // exit;
                echo $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                $obj->tamt = $obj->tamt;
                if(round($tamt)==round($obj->tamt)){                     
            //          print "\n $tamt==$obj->tamt";
                }else{
            //        print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";                    
                }
                 $diff = abs($tamt-$obj->tamt);
                if(trim($diff)>=1){
                    array_push($arr1,$obj->id);
                }
                array_push($arr,"D Mart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->filename."<>".$obj->tamt."<>".$tamt);
                break;
            }

        }
        // exit;
    }   


}

foreach($arr1 as $obb){
    $inv_id = $obb;
    $po_file = $db->fetchObject("select filename from it_po where id=$inv_id");
   // echo "select filename from it_po where id=$inv_id\n";
     $db->execUpdate("update it_po set status= ".POStatus::STATUS_ISSUE_AT_PROCESSING.", status_msg='" . POStatus::getStatusMsg( POStatus::STATUS_ISSUE_AT_PROCESSING) ."',issue_reason = 'Wrong Data in Excel' where id=$inv_id");
       $fileName = $db->safe($po_file->filename);                         
    $selqry ="select * from it_process_status where filename =$fileName  and status  in (".POStatus::STATUS_PROCESSED.",".POStatus::STATUS_ARTICLE_NO_MISSING.") and is_current_status=1";
   // echo $selqry;
    $selpoobj=$db->fetchObject($selqry);
     if(isset($selpoobj)){
    $poid = $selpoobj->id;
    $path = $selpoobj->pdfname;
    $pdfname = movetoissue1($path);
    $pdfname_db= $db->safe($pdfname);
    $updtQ = "update it_process_status set pdfname= $pdfname_db, status= ".POStatus::STATUS_ISSUE_AT_PROCESSING.", issue_reason = 'Wrong Data in Excel' where id = $poid";
    $db->execUpdate($updtQ);               
                        }
}

function movetoissue1($path){
    print"\n in move folder\n";
    $pathparts= pathinfo($path);
   print_r($pathparts);
    $srcdir = $pathparts['dirname'];
    $filename = $pathparts['filename'];
    $file_pdf = $pathparts['basename'];
    $file_text = $filename.".txt";
      
    $pparts=pathinfo($srcdir);    
    $destpath=$pparts['dirname'];
    $destdir = $destpath."/".statusFolder::getStatusMsg(POStatus::STATUS_ISSUE_AT_PROCESSING)."/";
    print"\n destdir:$destdir\n";
    print"\n src: $srcdir";
    if (!file_exists($destdir)) {
        mkdir($destdir,  0777 , true);
    }
    $pdfname=$destdir.$file_pdf;                    
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
    return $pdfname;
}



$sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('DataFixing');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Issue In');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'PO ID');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'PO Filename');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'From Database');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'From Invoice Text');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Difference');
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('h')->setWidth(20);
 
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
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
    
     $colCount = 0;
    $rowCount = 2;
    
    foreach($arr as $ar){
        $data = explode("<>",$ar);
        $diff = $data[5]-$data[6];
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $data[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $data[1]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $data[2]);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $data[3]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $data[4]); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $data[5]); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $data[6]); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $diff); 
        
        $colCount = 0;
        $rowCount++;
    }

 $nowtime = date('Y-m-d');
    $name = "DataFixing_" . $nowtime;
    $Ext = ".xls";
    $Filename = DEF_DATA_FIXING_EXL_PATH . $name . $Ext;
   
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($Filename);
    print"<br>excel created";
