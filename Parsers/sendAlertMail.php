<?php
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once("../../it_config.php");
require_once 'lib/db/DBConn.php';
require_once 'lib/core/Constants.php';
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "lib/email/EmailHelper.php";

try {
        $sucess = 0;
        $sucess =  sendAlertEmail();
        if($sucess==1){ 
//          print"<br>empty folders<br>";
          emptyStatusRepandIssuedir();
        }        
    } catch (Exception $xcp) {
        print $xcp->getMessage();
    }

function sendAlertEmail(){
    $emailHelper = new EmailHelper();
    $db= new DBConn();
    $unBUcnt=0;
    $unfiles= array();
    $today_dt = date('Y-m-d');
    $get_cuttent_time = date('Y-m-d H:i:s');
    $time = date('Y-m-d 14:00:00');
     if($get_cuttent_time < $time){
         $srtdt = $today_dt . " 00:00:00 ";         
    }else{
        $srtdt = $today_dt . " 00:00:00 ";         
    }
    
 //   $srtdt = date('Y-m-d');
    //$st_dt_db=$db->safe('2020-08-30 15:00:00');
//$ed_dt_db=$db->safe('2020-08-30 23:59:59');
  echo   $srtdt_db = $db-> safe($srtdt);
        
   
    $dirStatusRep = DEF_STATUS_REPORT_EXL_PATH;
    //$dirStatusRepMail = DEF_STATUS_REPORT_EXL_MAILED_PATH;
    $dirIssuexls = DEF_ISSUE_EXL_PATH;
    //$dirIssuexlsmail = DEF_ISSUE_EXL_MAILED_PATH;    
    
    if (file_exists($dirStatusRep)){
        $xlsMTDnm = "statusReport_MTD_".$srtdt.".xls";
        $xlsDailynm  = "statusReportDaily_".$srtdt.".xls";
        $xlsMTDpath = $dirStatusRep.$xlsMTDnm;
        $xlsDailypath = $dirStatusRep.$xlsDailynm;
        if(file_exists($xlsMTDpath)){
            array_push($unfiles,$xlsMTDpath);
        }
        if(file_exists($xlsDailypath)){
            array_push($unfiles,$xlsDailypath);
        }
    }
    if(file_exists($dirIssuexls)){
        $xlsIsuenm = "issueExcle_".$srtdt.".xls";
        $dirIssuexlspath = $dirIssuexls.$xlsIsuenm;
        if(file_exists($dirIssuexlspath)){
            print"\nIssue Excel present\n";
            array_push($unfiles,$dirIssuexlspath); 
        }       
    }
//    print_r($unfiles);
//    print"<br>************************************<br>";
    if (!file_exists(DEF_ALERT_MAIL)) {
        mkdir(DEF_ALERT_MAIL,  0777 , true);
        chmod(DEF_ALERT_MAIL, 0777);
    }
    $UBUquery= "select * from it_process_status where status = ".POStatus::STATUS_UNRECOGNIZED_BU." and is_current_status = 1 and createtime >= $srtdt_db"; //Unrecognised Business Unit
    $unBUfilesobj= $db->fetchAllObjects($UBUquery);
    if(isset($unBUfilesobj)){
        foreach ($unBUfilesobj as $value) {
            //array_push($unfiles,$value->pdfname);
            $pdf = $value->pdfname;
            $master_id = $value->master_dealer_id;
            $destination = DEF_ALERT_MAIL."unrecognizedBussinessUnit/";
            savePDF($master_id, $pdf,$db,$destination);
            $unBUcnt++;
            $pdf="";
        }
    } 
    $issueAtProcesscnt=0;
    $IAPquery= "select * from it_process_status where status = ".POStatus::STATUS_ISSUE_AT_PROCESSING." and is_current_status = 1 and createtime >= $srtdt_db";// Issue At Processing
    $issuesobj= $db->fetchAllObjects($IAPquery);
    if(isset($issuesobj)){
        foreach ($issuesobj as $value) {
            //array_push($unfiles,$value->pdfname);
            $pdf = $value->pdfname;
            $master_id=$value->master_dealer_id;
            $destination = DEF_ALERT_MAIL."issueAtProcessing/";
            savePDF($master_id, $pdf,$db,$destination);
            $issueAtProcesscnt++;
            $pdf="";
        }
    }     
    $unChaincnt=0;
    $UCquery= "select * from it_process_status where status = ".POStatus::STATUS_UNRECOGNIZED_CHAIN." and is_current_status = 1 and createtime >= $srtdt_db"; //Unrecognised Chain
    $unChainfilesobj= $db->fetchAllObjects($UCquery);
    if(isset($unChainfilesobj)){
        foreach ($unChainfilesobj as $value) {
            $pdf = $value->pdfname;
//            print"<br>pdf=$pdf<br>";
            $destination = DEF_ALERT_MAIL."unrecognizedChain/";
            //$dirpath = $destination;    
//            print"<br>source=".dirname($pdf)."<br>";
            $dir=dirname($pdf);
            $files = scandir($dir); 
//            print"<br>no of files=".count($files)."<br>";
            if(count($files) >= 2){ 
//                print "<br>DIR PATH: $destination ";
                // first chain dir
                if (!file_exists($destination)) {
                    mkdir($destination,  0777 , true);
                }
                $filepatharr= explode("/",$pdf);
                $filepatharr1=array_reverse($filepatharr);
                $filename=$filepatharr1[0];
//                print"<br>filename=$filename<br>";
                //print"<br>$pdf<br>";
                if(file_exists($pdf)){
                   copy($pdf,$destination.$filename); 
                }  
                //savePDF($master_id, $pdf,$db,$destination); as no master dealer known
                $unChaincnt++;
                $pdf="";
            }
        }
    }
      // Get real path for our folder
        $rootPath = realpath(DEF_ALERT_MAIL);
        $zipfile= 'alertmail_'.$srtdt.'.zip';
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
//        print"<br>zipfilepath=$zipfilepath<br>";
  
        // Zip archive will be created only after closing object
        $zip->close();
       
   // $unfiles=array($zipfilepath);
        if(file_exists($zipfilepath)){
             print"\n Zip folder present\n";
             array_push($unfiles,$zipfilepath);
        }
     
//    print"<br>contents=<br>"; 
//    print_r($unfiles);
    //if(count($unfiles)>0){
        if($unBUcnt!=0 OR $unChaincnt!=0 OR $issueAtProcesscnt!=0){
        $subject = "TTK:Alert";
        $body = "<br>Dear Sir<br><br><br>";
        $body .= "<p>This email provides Count and PDF of Unrecognized Business Unit and  Unrecognized Chain  </p>";
        $body .= "<p>And Also Provide the Status Report of PO's</p>";
        $body .= "<p>Unrecognized Business Unit : $unBUcnt</p>";
        $body .= "<p>Unrecognized Chain : $unChaincnt</p>";
        $body .= "<p>Issue At Processing : $issueAtProcesscnt</p>";
//        if(file_exists($zipfilepath)){
//        $body .= "<b><br><br></b>";
//        }
        $body .= "<b><br><br>Please Find The Attachments</b>";
        $body .= "<br/>";
        
        $toArray = array(//"nshirude@intouchrewards.com",
                  //"ukirad@intouchrewards.com",
                   "aashtekar@intouchrewards.com",            
                    "mmasurkar@intouchrewards.com",
                    "vsingh@intouchrewards.com"
                 //"anant@intouchrewards.com",
                //"unmesh@intouchrewards.com",
                //"igoyal@intouchrewards.com",
//                "ykirad@intouchrewards.com",
                //"dsonar@intouchrewards.com",
		//"igoyal@intouchrewards.com"       
        );
        print"<br>Email ID:<br>";
        print_r($toArray);
        
        $errormsg = $emailHelper->send($toArray, $subject, $body ,$unfiles);
        if ($errormsg != "0") {
            $errors['mail'] = " <br/> Error in sending mail, please try again later.";
            return -1;
        } 
        else{
            print"<br>Mail send successfully";
            return 1;
        } 
 }
 }
 
 function savePDF($master_id,$pdf,$db,$destination){
//    print"<br>destination=$destination<br>";
//    print"<br>source=".dirname($pdf)."<br>";
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

function emptyStatusRepandIssuedir(){
    $dirR=DEF_STATUS_REPORT_EXL_PATH;
    $dirW=DEF_STATUS_REPORT_EXL_MAILED_PATH ;
    moveToMail($dirR,$dirW);

    $dirR=DEF_ISSUE_EXL_PATH ;
    $dirW=DEF_ISSUE_EXL_MAILED_PATH ;
    moveToMail($dirR,$dirW);
}

function moveToMail($dirR, $dirW) {
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
