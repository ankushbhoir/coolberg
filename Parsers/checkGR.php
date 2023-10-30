<?php
require_once("../../it_config.php");
require_once("checkGRParser.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";
//require_once("processPoToDb.php");
//require_once("processPOs.php");
//$file_pdf = "HERITAGE.PDF";
//$file_text = "H.TXT";
//$source = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/movedFiles/";
$source = DEF_PROCESS_PATH;

$gr = new checkGRParser();
$dirs = scandir($source);
//print_r($dirs);
$db = new DBConn();

foreach($dirs as $dir){
    //step 1: chain folders
    if(trim($dir)!="" && trim($dir)!="." && trim($dir)!=".." ){
//        print "<br> SUB dir".$dir;  
        $c = str_replace("_", " ", $dir);
        $chain_name = $db->safe(trim($c));
        $master_dealer_id = 0;
        $query = "select * from it_master_dealers where name = $chain_name";
        //print "<br>$query";
        $cobj = $db->fetchObject($query);
//        print "<br>";
//        print_r($cobj);
//        print "<br>";       
        if(isset($cobj)){
            $master_dealer_id = $cobj->id;
//            $buParser = "cls_".$cobj->id."_buParser.php";
//            require_once("buParsers/".$buParser);
//            
            $chain_folder_path = $source.$dir."/";
//
            $sub_dirs = scandir($chain_folder_path);
//
//            print "<br>";
//            print_r($sub_dirs);
            
         // step 2: New POs of each chain
            $urdir = $chain_folder_path."unrecognizedBusinessUnit/";
            //$urdir = $chain_folder_path."issueAtProcessing/";
            //print "<br>UDIR: $urdir <br>";
            if(file_exists($urdir)){
                $unrecognizedFiles = scandir($urdir);
    //          print "<br>PO FILES: <br>";
              //print_r($unrecognizedFiles);
                foreach($unrecognizedFiles as $urPo){
                    $shipping_address = "";
       //           print "<br> UR PO: $urPo <br>"   ;
                    if(trim($urPo)!="" && trim($urPo)!="." && trim($urPo) != ".."){   
       //               print "<br> INSIDE IF <br>"   ;
                        $narr = explode(".", $urPo);
       //               print "<br> NARR: ";
       //               print_r($narr);
                        if(trim($narr[1])!="txt"){
                           $Ext = ".".$narr[1];  
       //                    print"<br>EXT=$Ext<br>";
                        }
                        if(trim($narr[1])=="txt"){
                            $file_path =  $urdir.$urPo;
                          // print "<br>FILE PATH: $file_path <br>";
                            $notifiaction = $gr->grParser($file_path);
                            if(trim($notifiaction)==1){ // means its GR
                                $pdfname=moveToGR($chain_folder_path,$urdir,$urPo,$Ext);
                                $chkQuery= "select * from it_process_status where  pdfname='$pdfname' and master_dealer_id=$master_dealer_id and status=". POStatus::STATUS_UNRECOGNIZED_BU ;
//                                print"<br>CHKQUERY=$chkQuery<br>";
                                $chkentry=$db->fetchObject($chkQuery);
                                if(isset($chkentry)){
                                    $statusid=$chkentry->id;
//                                    print"<br>statusid= $statusid<br><br>";
                                    $updatestatus="update it_process_status set status=". POStatus::STATUS_GR ." where id=$statusid";
                                    print"<br>UpdatestatusQuery=$updatestatus<br><br>";
                                    $db->execUpdate($updatestatus);
                                }
                            }
                        }else{
       //                    print "<br> IN ELSE : <br>";
                           continue;
                        }
                    }
                }
            }
        }
    }
    
}

function moveToGR($chain_folder_path,$urdir,$urPo,$Ext){
   $ugdir = $chain_folder_path."GR/";
    if (!file_exists($ugdir)) {
        mkdir($ugdir,  0777 , true);
    }
    $narr = explode(".",$urPo);
    //$file_pdf = $narr[0].".pdf";
    $file_pdf = $narr[0].$Ext;
    $file_text = $narr[0].".txt";

    $pdfname=$urdir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";
    
    $delete =  array();

    //first move pdf file
    if (copy($urdir.$file_pdf, $ugdir.$file_pdf)) {
        $delete[] = $urdir.$file_pdf;
    }

    //than move txt file
    if (copy($urdir.$file_text, $ugdir.$file_text)) {
        $delete[] = $urdir.$file_text;
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

