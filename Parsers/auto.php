<?php
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once("../../it_config.php");

//include("/home/ykirad/dev/subversion/onlinePOS/vlcc_dt/home/Parsers/processMaildir.php");
//sleep(20);

if(count(scandir(DEF_READ_PATH))>2 ){
    include("Parsers/sendACKmail.php");
    sleep(30);
include(ROOTPATH."Parsers/convertFiles.php");
sleep(30);
if(is_dir(DEF_PROCESS_PATH."Walmart/newPOs/")){
    include(ROOTPATH."util/SplitFile.php");
    sleep(10);
}
if(is_dir(DEF_PROCESS_PATH."Spencers_Retail_Limited/newPOs/")){
    include(ROOTPATH."util/SplitFile_spencers.php");
    sleep(10);
}
if(is_dir(DEF_PROCESS_PATH."Metro_Cash_&_Carry/newPOs/")){  
    include(ROOTPATH."util/SplitFile_metro.php");
    sleep(10);
}
if(is_dir(DEF_PROCESS_PATH."H&G/newPOs/")){
    include(ROOTPATH."util/SplitFile_handg.php");
    sleep(10);
}

include(ROOTPATH."Parsers/processPOs.php");
sleep(10);
//include("/vlcc/public_html/vlcc_dt/util/moveToNewPOs.php");
//sleep(10);
//include_once("/ykirad/dev/subversion/onlinePOS/vlcc_dt/Parsers/processPOs.php");
//sleep(10);


include(ROOTPATH."util/datafixing.php");
sleep(10);
//include(ROOTPATH."util/getVLCCShippingAddresses.php");
//include("/ykirad/dev/subversion/onlinePOS/vlcc_dt/Parsers/checkGR.php");
//sleep(10);
include(ROOTPATH."Parsers/issueExcel.php");
sleep(10);
//include(ROOTPATH."Parsers/statusReportMTD.php");
//sleep(10);
//include(ROOTPATH."Parsers/statusReportDaily.php");
//sleep(10);
//create EANnotfound and articlenot found excel if EAN missing or article missing
// $chaindirs =  scandir(DEF_PROCESS_PATH);
// $Iflag = 0;
// $Dflag = 0;
//print_r($chaindirs);
// foreach($chaindirs as $chaindir){
//     if(trim($chaindir)!="" && trim($chaindir)!="." && trim($chaindir) != ".."){
//         $statusdirs = scandir(DEF_PROCESS_PATH.$chaindir);
//     //    print_r($statusdirs);
//         foreach($statusdirs as $statusdir){
//             if(trim($statusdir)!="" && trim($statusdir)!="." && trim($statusdir) != ".."){
//     //            print"\ndirname=$statusdir\n";
//                 if(strcasecmp($statusdir, 'eanMissingPOs')==0 || strcasecmp($statusdir, 'missingEAN')==0){
//                     if($Iflag == 0){
//                         print"\n eanMissing\n"; 
//                         include(ROOTPATH."Parsers/ItmNtFnd.php");
//                         $Iflag = 1;    
//                     }
//                 }         
//                 if(strcasecmp($statusdir, 'ArticleNoMissingPOs')==0 || strcasecmp($statusdir, 'missingArticleNo')==0){
//                     if($Dflag == 0){
//                         print"\n Article missing\n";
//                         include(ROOTPATH."Parsers/DealerItmNtFnd.php");
//                         $Dflag = 1;
//                     }
//                 }
//             }
//         }
//     }
// }
//sleep(10);
echo "\n";
echo "*******************START OF MISSIN MASTER************************";
include(ROOTPATH."Parsers/missingmasters.php");
sleep(40);
include(ROOTPATH."Parsers/missingvendoremail.php");
sleep(10);
include(ROOTPATH."Parsers/sendEanmail.php");
sleep(10);
include(ROOTPATH."Parsers/shiptopartymissemail.php");
sleep(20);
include(ROOTPATH."Parsers/checkubu.php");
sleep(20);
include(ROOTPATH."Parsers/insert_into_master.php");
sleep(20);
//include(ROOTPATH."Parsers/DailyPOExcel.php");

include(ROOTPATH."Parsers/DailyPO_XML.php");
include(ROOTPATH."Parsers/unrecognizechain.php");
// include(ROOTPATH."Parsers/move_to_ftp.php");

//sleep(10);


include(ROOTPATH."Parsers/sendAlertMail.php");
//sleep(10);
//include(ROOTPATH."Parsers/PO_ExcelInternal.php");

//include(ROOTPATH."util/getEANMismatch.php");
// sleep(10);// sleep(seconds)
// //include(ROOTPATH."Parsers/sendmail.php");
// sleep(10);
//include(ROOTPATH."Parsers/sendmail_internal.php");
}else{
    echo "NOTICE::POs not found in receivedPOs folder\n";
    //include(ROOTPATH."Parsers/sendmailNoPos.php");
}
