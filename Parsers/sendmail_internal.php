<?php
require_once("../../it_config.php");
require_once 'lib/db/DBConn.php';
require_once 'lib/core/Constants.php';
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "lib/email/EmailHelper.php";

try {
    $sucess = 0;
    $sucess = sendDailyParsedPOEmail1();
    if ($sucess == 1) {
        emptyINFandPPOfolder1();
    }
} catch (Exception $xcp) {
    print $xcp->getMessage();
}

function sendDailyParsedPOEmail1() {
    $db = new DBconn();
    $fpatharr = array();
    $emailHelper = new EmailHelper();
    $dirPPO = DEF_PARSED_EXL_PATH;
    $dirDPO = DEF_PARSED_DAILY_EXL_PATH;
    $dirDFO = DEF_DATA_FIXING_EXL_PATH;
    $xlsNF = "";
    $xlsPO = "";

    $today_dt = date('Y-m-d');
    $today_dt1 = date('d/m/Y');
    $get_cuttent_time = date('Y-m-d H:i:s');
    $time = date('Y-m-d 14:00:00');
    $st_dt = $today_dt . " 00:00:00 ";    
    $ed_dt = $today_dt . " 23:59:59 ";
        
    $bd = "";
     if($get_cuttent_time < $time){
         $st_dt = $today_dt . " 00:00:00 ";
          $subject = "VLCC Internal: MTD and Daily report ($today_dt1)";          
    }else{
        $st_dt = $today_dt . " 14:00:00 ";
         $subject = "VLCC Internal: Daily report 4:00pm ($today_dt1)";         
    }
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt_db = $db->safe(trim($ed_dt));
    
    if (file_exists($dirPPO)) {
        $xlsPO = "MTDInternal_" . $today_dt . ".xls";
    }

    if (file_exists($dirDPO)) {
        $xlsDPO = "DailyPOReportInternal_" . $today_dt . ".xls";
    }
    
     if (file_exists($dirDFO)) {
        $xlsDFO = "DataFixing_" . $today_dt . ".xls";
    }            

    $dirPPOnm = DEF_PARSED_EXL_MAILED_PATH . $xlsPO;
    $dirDPOnm = DEF_PARSED_DAILY_EXL_MAILED_PATH . $xlsDPO;
    $dirDFOnm = DEF_DATA_FIXING_EXL_PATH . $xlsDFO;    
    
    array_push($fpatharr, $dirPPOnm);
    array_push($fpatharr, $dirDPOnm);
    array_push($fpatharr, $dirDFOnm);        
   
    $body = "<br>Dear Sir,<br><br>";
    $body .= "<ul><li>Please find attached report.</li>";          

    $toArray = array(
                     "npande@intouchrewards.com",       
                     "igoyal@intouchrewards.com",
                     "dsonar@intouchrewards.com"
    );
    $errormsg = $emailHelper->send($toArray, $subject, $body, $fpatharr);
    if ($errormsg != "0") {
        $errors['mail'] = " <br/> Error in sending mail, please try again later.";
        return -1;
    } else {
        return 1;
    }
}

function emptyINFandPPOfolder1() {

    $dirR = DEF_PARSED_EXL_PATH;
    $dirW = DEF_PARSED_EXL_MAILED_PATH;
    moveToMailed1($dirR, $dirW);

    $dirR = DEF_PARSED_DAILY_EXL_PATH;
    $dirW = DEF_PARSED_DAILY_EXL_MAILED_PATH;
   moveToMailed1($dirR, $dirW);
   
}

function moveToMailed1($dirR, $dirW) {
    if (file_exists($dirR)) {
        $xlsfile = scandir($dirR);

        foreach ($xlsfile as $readfile) {
            if (trim($readfile) != "" && trim($readfile) != "." && trim($readfile) != "..") {
                if (copy($dirR . $readfile, $dirW . $readfile)) {
                    $delete[] = $dirR . $readfile;
                }
            }
        }
    }
    if(! empty($delete)){
        foreach ($delete as $file_xls) {
            if(trim($file_xls)!="" && trim($file_xls)!="." && trim($file_xls) != ".."){  
               unlink($file_xls);
               print"<br>empty<br>";
            }
        } 
    }     
}
