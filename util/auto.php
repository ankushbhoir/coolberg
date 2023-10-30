<?php
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once('/home/vlcc/public_html/vlcc_dt/it_config.php');
//include("/home/vlcc/public_html/vlcc_dt/home/Parsers/processMaildir.php");
//sleep(20);
if(count(scandir(DEF_READ_PATH)) > 2){
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/convertFiles.php");
sleep(180);
if(is_dir(DEF_PROCESS_PATH."Walmart/newPOs/")){
    include("/home/vlcc/public_html/vlcc_dt/home/util/SplitFile.php");
    sleep(10);
}
if(is_dir(DEF_PROCESS_PATH."Spencers_Retail_Limited/newPOs/")){
    include("/home/vlcc/public_html/vlcc_dt/home/util/SplitFile_spencers.php");
    sleep(10);
}
if(is_dir(DEF_PROCESS_PATH."Metro_Cash_&_Carry/newPOs/")){
    include("/home/vlcc/public_html/vlcc_dt/home/util/SplitFile_metro.php");
    sleep(10);
}
if(is_dir(DEF_PROCESS_PATH."H&G/newPOs/")){
    include("/home/vlcc/public_html/vlcc_dt/home/util/SplitFile_handg.php");
    sleep(10);
}

include("/home/vlcc/public_html/vlcc_dt/home/Parsers/processPOs.php");
sleep(10);
//include("/home/vlcc/public_html/vlcc_dt/home/util/moveToNewPOs.php");
//sleep(10);
//include_once("/home/vlcc/public_html/vlcc_dt/home/Parsers/processPOs.php");
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/util/datafixing.php");
sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/util/getVLCCShippingAddresses.php");
sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/checkGR.php");
sleep(10);
//include("/home/vlcc/public_html/vlcc_dt/home/Parsers/checkGR.php");
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/issueExcel.php");
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/statusReportMTD.php");
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/statusReportDaily.php");
//sleep(10);
//create EANnotfound and articlenot found excel if EAN missing or article missing
$chaindirs =  scandir(DEF_PROCESS_PATH);
$Iflag = 0;
$Dflag = 0;
//print_r($chaindirs);
foreach($chaindirs as $chaindir){
    if(trim($chaindir)!="" && trim($chaindir)!="." && trim($chaindir) != ".."){
        $statusdirs = scandir(DEF_PROCESS_PATH.$chaindir);
    //    print_r($statusdirs);
        foreach($statusdirs as $statusdir){
            if(trim($statusdir)!="" && trim($statusdir)!="." && trim($statusdir) != ".."){
    //            print"\ndirname=$statusdir\n";
                if(strcasecmp($statusdir, 'eanMissingPOs')==0 || strcasecmp($statusdir, 'missingEAN')==0){
                    if($Iflag == 0){
                        print"\n eanMissing\n"; 
                        include("/home/vlcc/public_html/vlcc_dt/home/Parsers/ItmNtFnd.php");
                        $Iflag = 1;    
                    }
                }         
                if(strcasecmp($statusdir, 'ArticleNoMissingPOs')==0 || strcasecmp($statusdir, 'missingArticleNo')==0){
                    if($Dflag == 0){
                        print"\n Article missing\n";
                        include("/home/vlcc/public_html/vlcc_dt/home/Parsers/DealerItmNtFnd.php");
                        $Dflag = 1;
                    }
                }
            }
        }
    }
}
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/sendAlertMail.php");
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/DailyPOExcel.php");
//sleep(10);
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/PO_Excel.php");
sleep(10);// sleep(seconds)
include("/home/vlcc/public_html/vlcc_dt/home/Parsers/sendmail.php");
}else{
    echo "NOTICE::POs not found in receivedPOs folder\n";
}
