<?php
//require_once("../../it_config.php");
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

$db = new DBConn();
$arr = array();
$dealers = $db->fetchAllObjects("select id from it_master_dealers");
foreach($dealers as $dealer){
    $master_dealer_id = $dealer->id;

//$master_dealer_id = $argv[1];
$date = date('Y-m-d');
$qry = "select id,master_dealer_id,invoice_no,tqty,tamt,invoice_text,filename,ctime from it_po where status not in (10) and master_dealer_id = $master_dealer_id and ctime >= '$date 00:00:00'";
//print $qry;
$objs = $db->fetchAllObjects($qry);
//print_r($objs);
foreach($objs as $obj){
    $mid = $obj->master_dealer_id;
    if($mid == 2){//future retail
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);        
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++){
            $line = trim($lines[$lineno]);
            $regex1 = "/Terms\s*&\s*Conditions\s*:\s*/";
             if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $obj->invoice_no \n ";
                $end_line_no = $lineno;   
//                print "<br> End LINE NO: $end_line_no <br>";
                for($i= $end_line_no; $i>0; $i--){
                    $line1 = trim($lines[$i]);
                    $regex2="/Total/";
                    if(preg_match($regex2,$line1,$matches)){
//                        print "<br> START LINE NO $i: $line1 <br>";
//                        $start_line_no = $i;
                        $line = trim($lines[$i]);
                        $regex = "/Total\s+(\d\S+)/";
                        if(preg_match($regex,$line,$matches)){
            //                print "\n $line";    
            //                print_r($matches);
                            $invno = str_replace(",","",$matches[1]);
                            $invarr = explode(".",$invno);
                            $qty= $invarr[0];
//                            print "\n $invarr[0]==$obj->tqty";
                            if($qty==$obj->tqty){

                            }else{
                               // print"\n Issue invoice:-       $obj->invoice_no";
                               // print "\n $qty==$obj->tqty";
                                array_push($arr,"Future Retail<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tqty."<>".$qty);
                            }
                            break;
                        }else{
                            print"\n Not Matched $obj->invoice_no";
                            print "\n $line";
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
//        print "\n $obj->invoice_no";    
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Qty\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
//                print "\n $invarr[0]==$obj->tqty";
                }else{
                  //  print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";
//                    print "\n $qty==$obj->tqty";
                    array_push($arr,"ABRL Super<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tqty."<>".$qty);
                }
            }
        }
    }
    if($mid == 4){// ABRL Super
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);    
//        print "\n $obj->invoice_no";    
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Qty\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
//                print "\n $invarr[0]==$obj->tqty";
                }else{
                    //print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";
//                    print "\n $qty==$obj->tqty";
                    array_push($arr,"ABRL Hyper<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tqty."<>".$qty);
                }
            }
        }
    }
     if($mid == 15){// Trent
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);    
//        print "\n $obj->invoice_no";    
//        print "\n CNT: $numlines";
        $qty = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
//                print "\n $invarr[0]==$obj->tqty";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";
//                    print "\n $qty==$obj->tqty";
                    array_push($arr,"Trent<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tqty."<>".$qty);
                }
            }
        }
    }
    if($mid == 16){// Trent
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);    
//        print "\n $obj->invoice_no";    
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invno = str_replace(",","",$matches[1]);
                $invarr = explode(".",$invno);
                $qty= $invarr[0];
                if($qty==$obj->tqty){                      
//                print "\n $invarr[0]==$obj->tqty";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $invarr[0]==$obj->tqty";
//                    print "\n $qty==$obj->tqty";
                    array_push($arr,"FIORA<>Qty<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tqty."<>".$qty);
                }
            }
        }
    }
    if($mid == 14){// Metro
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/TOTAL\s*AMOUNT\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
                $tamt=round($invamt,2);
//                $tamt=$invamt;
                if($tamt==$obj->tamt){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                 //   print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
            }else if(preg_match('/Total\s+Value\s*\(\s*excl.\s*Tax\s*\)\s*:\s*(\S+)/',$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
                $tamt=round($invamt,2);
//                $tamt=$invamt;
                if($tamt==$obj->tamt){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                  //  print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime   $tamt==$obj->tamt";
                    array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
            }else if(preg_match('/Total\s+order\s+amount\s+INR\s*:\s*(\d\S+)/',$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
                $tamt=round($invamt,2);
//                $tamt=$invamt;
                if($tamt==$obj->tamt){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                  //  print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime   $tamt==$obj->tamt";
                    array_push($arr,"Metro<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
            }
        }
    }
    if($mid == 5){// Reliance
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*Order\s*Value\s*:\s*INR\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                  //  print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Reliance<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
            }
        }
    }
    if($mid == 8){// Spencer's
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+Value\s*:\s*(\d\S*)/";
//            $regex1 = "/Total\s+Value\s*:\s*(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                  //  print"\n Issue invoice:-    $obj->invoice_no  :::::::Inv id: $obj->id     :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                array_push($arr,"Spencers<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                    
                }
                break;
            }
        }
    }
     if($mid == 7){// Walmart
        $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
           // $regex1 = "/Total\s+Units\s+Ordered\s+(\d+)/";
            $regex1 = "/Total\s+Order\s+Amount\s+\(\s*Before\s*Adjustments\s*\)\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tqty";
                    array_push($arr,"Walmart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
    if($mid==11){
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s*:\s*\d+\.\d+\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                    //print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"MAX<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
     if($mid==26){ //vishal megamart
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Grand\s+Total\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Vishal megamart<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }if($mid==27){ //lullu
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+Invoice\s+Cost\s*:\s*(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Lullu<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }if($mid==24){ //H&B
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/TOTAL\s+ORDER\s+VALUE\s+INR\s+(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                   // print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"H&B<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
     if($mid==28){ //Guardian
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Order\s+Amount\s*:\s*(\S+)/";
            if(preg_match($regex1,$line,$matches)){
                $invamt = str_replace(",","",$matches[1]);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                    print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                }
                break;
            }
        }
    }
     if($mid==21){
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Grand\s*Total\s*:\s*(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                    //print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Ratandeep<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
      if($mid==31){
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+(\d\S*)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                    //print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Bismi<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
    
       if($mid==33){
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Total\s+\S+\s+\S+\s+\S+\s+\S+\s+(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                    //print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"CP_wholesale<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
    
    
       if($mid==44){
         $invtext = $obj->invoice_text;
//        print " text2 ==============\n";
        $lines = explode("\n", $invtext);
        $numlines = count($lines);   
//        print "\n $obj->invoice_no";   
//        print "\n CNT: $numlines";
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            $regex1 = "/Net\s+PO\s+Amount\s*:\s*(\S+)/";
            if(preg_match($regex1,$line,$matches)){
//                print "\n IN D IF: $line \n ";
                $invamt = str_replace(",","",$matches[1]);
//                $invarr = explode(".",$invno);
//                $tamt=round($invamt);
                $tamt=$invamt;
                if(round($tamt)==round($obj->tamt)){                     
//                print "\n $tamt==$obj->tamt";
                }else{
                    //print"\n Issue invoice:-    $obj->invoice_no      :::::::::  $obj->filename <><><><><>$obj->ctime  $tamt==$obj->tamt";
                    array_push($arr,"Bakshish<>Amt<>".$obj->id."<>".$obj->invoice_no."<>".$obj->tamt."<>".$tamt);
                }
                break;
            }
        }
    }
}
}

$sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('DataFixing');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chain');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Issue');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'PO ID');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'PO Number');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'PO data');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Processed data');
    
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
 
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
    //$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
    
     $colCount = 0;
    $rowCount = 2;
    
    foreach($arr as $ar){
        $data = explode("<>",$ar);
     $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $data[0]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $data[1]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $data[2]);        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $data[3]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $data[4]); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $data[5]); 
        
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


