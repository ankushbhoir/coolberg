<?php
require_once("../../it_config.php");
require_once 'lib/db/DBConn.php';
require_once 'lib/core/Constants.php';
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "lib/email/EmailHelper.php";

try {
    $sucess = 0;
    $sucess = sendDailyParsedPOEmail();
    if ($sucess == 1) {
//        print"<br>empty folders<br>";
        emptyINFandPPOfolder();
    }
} catch (Exception $xcp) {
    print $xcp->getMessage();
}

function sendDailyParsedPOEmail() {
    $db = new DBconn();
    $fpatharr = array();
    $emailHelper = new EmailHelper();
    $dirINF = DEF_ITM_NTFOUND_PATH;
    $dirDINF = DEF_DEALER_ITM_NTFOUND_PATH; 
    $dirPPO = DEF_PARSED_EXL_PATH;
    $dirDPO = DEF_PARSED_DAILY_EXL_PATH;
    $dirDFO = DEF_DATA_FIXING_EXL_PATH;
    $dirMCC = DEF_MISSING_CUST_XLS;
    $dirEANMismatch = DEF_EAN_MISMATCH_EXL_PATH;
    $xlsNF = "";
    $xlsPO = "";

    $today_dt = date('Y-m-d');
    $today_dt1 = date('d/m/Y');
    $get_cuttent_time = date('Y-m-d H:i:s');
    $time = date('Y-m-d 14:00:00');
    $st_dt = $today_dt . " 00:00:00 ";    
    $ed_dt = $today_dt . " 23:59:59 ";
    
        
    $bd = "";
    $mid_dt_db="";
     if($get_cuttent_time < $time){
         $st_dt = $today_dt . " 00:00:00 ";
          $subject = "VLCC : MTD and Daily report ($today_dt1)";
          $bd = " MTD and Daily";         
    }else{
        $st_dt = $today_dt . " 00:00:00 ";
         $subject = "VLCC : Daily report 4:00pm ($today_dt1)";
         $bd = " Daily";
    }
    $st_dt_db = $db->safe(trim($st_dt));
    $ed_dt_db = $db->safe(trim($ed_dt));
    

//    if (file_exists($dirINF)) {
//        $xlsNF = "Itemnotfound_" . $today_dt . ".xls";
//        $dirINFnm = DEF_ITM_NTFOUND_PATH . $xlsNF;
////        print"<br> dirINFnm:$dirINFnm<br>";
//    }
//    
//    if (file_exists($dirDINF)) {
//        $xlsNF = "DealerItemnotfound_" . $today_dt . ".xls";
//        $dirDINFnm = DEF_DEALER_ITM_NTFOUND_PATH . $xlsNF;
////        print"<br> dirDINFnm:$dirDINFnm<br>";
//    }
    
    if (file_exists($dirPPO)) {
        $xlsPO = "MTD_" . $today_dt . ".xls";
    }

    if (file_exists($dirDPO)) {
        $xlsDPO = "DailyPOReport_" . $today_dt . ".xls";
    }
    
     /*if (file_exists($dirDFO)) {
        $xlsDFO = "DataFixing_" . $today_dt . ".xls";
    }*/
    
      if (file_exists($dirMCC)) {
        $xlsMCC = "Missing_customer_code_" . $today_dt . ".xls";
    }
    
      if (file_exists($dirEANMismatch)) {
        $xlsEAN = "EANMisamtch_" . $today_dt . ".xls";
    }
    

    $dirPPOnm = DEF_PARSED_EXL_PATH . $xlsPO;
    $dirDPOnm = DEF_PARSED_DAILY_EXL_PATH . $xlsDPO;
  //  $dirDFOnm = DEF_DATA_FIXING_EXL_PATH . $xlsDFO;
    $dirMCCnm = DEF_MISSING_CUST_XLS . $xlsMCC;
    $dirEANMismatchmm = DEF_EAN_MISMATCH_EXL_PATH . $xlsEAN;

//    print"<br> dirPPOnm:$dirPPOnm<br>";
//    print"<br> dirDPOnm:$dirDPOnm<br>";
    
    array_push($fpatharr, $dirPPOnm);
    array_push($fpatharr, $dirDPOnm);
//    array_push($fpatharr, $dirDFOnm);
    array_push($fpatharr, $dirMCCnm);
    array_push($fpatharr, $dirEANMismatchmm);
    
//    if (file_exists($dirINFnm)) {
//        array_push($fpatharr, $dirINFnm);
//    }
//     if (file_exists($dirDINFnm)) {
//        array_push($fpatharr, $dirDINFnm);
//    }
   
    $dir_name = "ArticleNo_missing/";
    $destination_zip = DEF_PROCESS_PATH.$dir_name;
    if (!file_exists($destination_zip)) {
        mkdir($destination_zip,  0777 , true);
    }    
    
    $query = "select * from it_process_status where status in(" . POStatus::STATUS_MISSING_EAN . "," . POStatus::STATUS_ARTICLE_NO_MISSING . ")and createtime >= $st_dt_db and createtime <= $ed_dt_db"; //and is_current_status = 1
    //print"<br>**********$query<br>";
    $EANMissfilesobj = $db->fetchAllObjects($query);
    if (isset($EANMissfilesobj)) {
//        print"<br>Query success<br>";
        foreach ($EANMissfilesobj as $value) {
            //array_push($fpatharr, $value->pdfname);            
           copy($value->pdfname,$destination_zip.$value->filename);                 
        }
    }
    
    // Get real path for our folder
        $rootPath = realpath($destination_zip);
        $zipfile= 'ArticleNo_missing.zip';
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as  $file){//$name =>
           // Skip directories (they would be added automatically)
            if (!$file->isDir()){
               // Get real and relative path for current file
               $filePath = $file->getRealPath();
               $relativePath = substr($filePath, strlen($rootPath) + 1);
               // Add current file to archive
               $zip->addFile($filePath, $relativePath);
            }
        }        
          $zipfilepath=$zip->filename;
        // Zip archive will be created only after closing object
        $zip->close();
          
        if(file_exists($zipfilepath)){
             print"\n Zip folder present\n";
             array_push($fpatharr,$zipfilepath);
        }
    

    print"<br>Contents<br>";
    print_r($fpatharr);

    $cntarr = array();
    $countQuery = "select imt.name as chainname, count(*) count from it_master_dealers imt ,it_process_status ipt where imt.id =ipt.master_dealer_id and status in(" . POStatus::STATUS_MISSING_EAN . "," . POStatus::STATUS_ARTICLE_NO_MISSING . ") and ipt.createtime >= $st_dt_db and ipt.createtime <= $ed_dt_db group by master_dealer_id
";
//    print"<br>+++++++++++$countQuery <br>";
    $PDFcntobj = $db->fetchAllObjects($countQuery);
    if (isset($PDFcntobj)) {
        foreach ($PDFcntobj as $value) {
            $chain_name = $value->chainname;
            $count = $value->count;
            //array_push($cntarr,$chain_Name."->".$count);
            $cntarr[$chain_name] = $count;
        }
    }

    $totalchain = count($cntarr);
    print"<br>tchain=$totalchain<br>";
    print_r($cntarr);
    print"<br>";

   
    $body = "<br>Dear Sir,<br>";
    $body .= "<ul><li>Please find attached $bd report.</li>";
    $body .= "<li>Please find attached EAN mismatch report.</li>";
    if (count($cntarr) > 0) {
        $body .= "<li> Zip contain Article Number Missing POs.</li>";
      //  $body .= "<p># EAN Missingg / Aricle Number Missing PO's Count and POs </p>";

       // foreach ($cntarr as $key => $val) {
       //     $body .= "<br>$key : $val<br>";
       // }
    }
    
    if (file_exists(DEF_ALERT_MAIL)) {
        mkdir(DEF_PENDING_POS,  0777 , true);
        chmod(DEF_PENDING_POS, 0777);
    }
    $issuesobj = $db->fetchAllObjects("select pdfname,filename from it_process_status where status in (".POStatus::STATUS_UNRECOGNIZED_BU.",".POStatus::STATUS_ISSUE_AT_PROCESSING.") and createtime >= $st_dt_db and is_current_status = 1 and createtime <= $ed_dt_db");
//    echo "select pdfname from it_process_status where status in (".POStatus::STATUS_UNRECOGNIZED_BU.",".POStatus::STATUS_ISSUE_AT_PROCESSING.") and createtime >= $st_dt_db and is_current_status = 1 and createtime <= $ed_dt_db";
    
    if(isset($issuesobj) && !empty($issuesobj)){
        foreach ($issuesobj as $value) {
            $pdf = $value->pdfname;
            $filename = $value->filename;
            copy($pdf,DEF_PENDING_POS.$filename);
        }
        
         // Get real path for our folder
        $rootPath1 = realpath(DEF_PENDING_POS);
        $zipfile1= 'Pending_POs.zip';
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zipfile1, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath1),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as  $file){//$name =>
           // Skip directories (they would be added automatically)
            if (!$file->isDir()){
               // Get real and relative path for current file
               $filePath = $file->getRealPath();
               $relativePath = substr($filePath, strlen($rootPath1) + 1);
               // Add current file to archive
               $zip->addFile($filePath, $relativePath);
            }
        }
        
        $zipfilepath1=$zip->filename;
//        print"<br>zipfilepath=$zipfilepath<br>";
  
        // Zip archive will be created only after closing object
        $zip->close();
          if(file_exists($zipfilepath1)){             
             array_push($fpatharr,$zipfilepath1);
        }
        
        $body .= "<li> You will get details of below attached POs soon. (Pending POs)</li></ul>";
    }    
    
    //$body .= "<b><br><br>Please Find The Attachments</b>";
    $body .= "<br/>";

    $toArray = array("mmasurkar@intouchrewards.com",                    
//                     "dsonar@intouchrewards.com",		  
//		     "atul.mishra@vlccpersonalcare.com",
//                     "ankit.bhandari@vlccpersonalcare.com",
//                    "igoyal@intouchrewards.com",
//                    "ashutosh.khattar@vlccpersonalcare.com",
//                    "rahul.gupta@vlccpersonalcare.com",
//                    "deepak.jain@vlccpersonalcare.com",
//                    "mdeodhar@intouchrewards.com"
    );
//    print"<br>Email ID:<br>";
//    print_r($toArray);

    $errormsg = $emailHelper->send($toArray, $subject, $body, $fpatharr);
    if ($errormsg != "0") {
        $errors['mail'] = " <br/> Error in sending mail, please try again later.";
        return -1;
    } else {
        return 1;
    }
}

function emptyINFandPPOfolder() {
//    $dirR = DEF_ITM_NTFOUND_PATH;
//    $dirW = DEF_ITM_NTFOUND_MAILED_PATH;
//    moveToMailed($dirR, $dirW);
//    
//    $dirR = DEF_DEALER_ITM_NTFOUND_PATH;
//    $dirW = DEF_DEALER_ITM_NTFOUND_MAILED_PATH;
//    moveToMailed($dirR, $dirW);

    $dirR = DEF_PARSED_EXL_PATH;
    $dirW = DEF_PARSED_EXL_MAILED_PATH;
    moveToMailed($dirR, $dirW);

    $dirR = DEF_PARSED_DAILY_EXL_PATH;
    $dirW = DEF_PARSED_DAILY_EXL_MAILED_PATH;
    moveToMailed($dirR, $dirW);
}

function moveToMailed($dirR, $dirW) {
    if (file_exists($dirR)) {
        $xlsfile = scandir($dirR);
//        print_r($xlsfile);
//        print "<br>";

        foreach ($xlsfile as $readfile) {
            if (trim($readfile) != "" && trim($readfile) != "." && trim($readfile) != "..") {
//                print"src:" . $dirR . $readfile . "<br>";
//                print"dest:" . $dirR . $readfile . "<br>";
                if (copy($dirR . $readfile, $dirW . $readfile)) {
                    $delete[] = $dirR . $readfile;
                }
            }
        }
//        print"<br>Delete=><br>";
//        print_r($delete);
//        print"<br>";
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

 function savePDF1($master_id,$pdf,$destination){
        $dir=dirname($pdf);
        $files = scandir($dir); 
//        print"<br>no of files=".count($files)."<br>";
        if(count($files) > 2){   
            $MDquery = "select * from it_master_dealers where id=$master_id";
            $mobj = $db->fetchObject($MDquery);
            if(isset($mobj)){
                $ch = str_replace(" ", "_",$mobj->name);
                $dirpath = $destination.$ch."/";          
//                  print "<br>DIR PATH: $dirpath ";
                // first chain dir
                if (!file_exists($dirpath)) {
                    mkdir($dirpath,  0777 , true);
                    chmod($dirpath, 0777);
                }    
            }
            $filepatharr= explode("/",$pdf);  // explode $pdf and get file name then use $dirpath.filename
            $filepatharr1=array_reverse($filepatharr);
            $filename=$filepatharr1[0];
//            print"<br>filename=$filename<br>";
            if(file_exists($pdf)){
                copy($pdf,$dirpath.$filename);
            }
        }
}
