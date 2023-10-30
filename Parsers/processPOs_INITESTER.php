<?php
require_once("../../it_config.php");
require_once("../Parsers/headerReaderParser.php");
require_once("lib/db/DBConn.php");
require_once("../Parsers/processPoToDb.php");
require_once "lib/core/Constants.php";
require_once "../Parsers/initester.php";
//$file_pdf = "HERITAGE.PDF";
//$file_text = "H.TXT";
$source = "/var/www/weikfield_DT_AutoChk/home/Parsers/movedFiles/";

$dirs = scandir($source);
print_r($dirs);
$db = new DBConn();

foreach($dirs as $dir){
    //step 1: chain folders
    if(trim($dir)!="" && trim($dir)!="." && trim($dir)!=".." ){
        print "<br> SUB dir".$dir;
        
        $c = str_replace("_", " ", $dir);
        $chain_name = $db->safe(trim($c));
        $master_dealer_id = 0;
        $query = "select * from it_master_dealers where name = $chain_name";
        print "<br>$query";
        $cobj = $db->fetchObject($query);
        print "<br>";
        print_r($cobj);
        print "<br>";
        
        if(isset($cobj)){
            $master_dealer_id = $cobj->id;
//            $buParser = "cls_".$cobj->id."_buParser.php";
//            require_once("../Parsers/buParsers/".$buParser);
//            
            $chain_folder_path = $source.$dir."/";
//
            $sub_dirs = scandir($chain_folder_path);
//
            print "<br>";
            print_r($sub_dirs);
            
         // step 2: New POs of each chain
            $ndir = $chain_folder_path."newPOs/";
            print "<br>NDIR: $ndir <br>";
            $newPOsFiles = scandir($ndir);
            print "<br>NEW PO FILES: <br>";
            print_r($newPOsFiles);
            
            foreach($newPOsFiles as $newPo){
             $shipping_address = "";
             print "<br> NEW PO: $newPo <br>"   ;
//             $pdfname=$newPo;
//             print"<br>PDFNAME=$pdfname<br>";
             if(trim($newPo)!="" && trim($newPo)!="." && trim($newPo) != ".."){   
                print "<br> INSIDE IF <br>"   ;
                $clsname = "cls_".$cobj->id."_buParser";
                print "<br> CLSNAME: $clsname <br>";
                if(file_exists("../Parsers/buParsers/".$clsname.".php")){
                    print "<br> Print 1 <br>"   ;
                    require_once "../Parsers/buParsers/$clsname.php";
                    print "<br> Print 2 : $newPo <br>"   ;
                    $narr = explode(".", $newPo);
                    print "<br> NARR: ";
                    print_r($narr);
                    if(trim($narr[1])=="txt"){
    //                    if (!@include("../Parsers/buParsers/$clsname.php")) {
    ////    //                    $query = "update it_invoices set status=".Status::STATUS_MISSING_PARSER.", status_msg='Missing invoiceParser. Contact administrator.' where id=$distid";
    ////    //                    $db->execUpdate($query);
    ////    //                    return false;
    //                    print "<br>BU Parser not found <br>";
    //                    }else{
    //                        print "<br> TTTT <br>";
    //                    }
                        print "<br> IN TXT <br>";
                        $fpath = $ndir.$newPo;
                        print "<br>FPATH: $fpath <br>";

                        //send only txt files to parser
                        $parser = new $clsname();
                        $shipping_address = $parser->process($fpath);//inimaster_dealer_id from obj
                                                
                        $shipping_obj = fetchINI($shipping_address,$master_dealer_id,$fpath,$newPo);// inimaster_dealer_id
//                        if(isset($shipping_obj)){
//                            // found ini file details
//                           $responsearr = callProcessor($shipping_obj,$fpath);
//                           //new fn processorResponseAction($responsearr,$chain_folder_path,$ndir,$newPO)
//                           $rarr = explode("::", $responsearr);
//                           $response = $rarr[0];
//                           $notifiaction = $rarr[1];
//                           print "<br> RESPONSE : $response <br>";
//                           if($notifiaction==1){
//                               //movetoEANMISSING
//                               moveToEanMissing($chain_folder_path,$ndir,$newPo);
//                           }
//                           if(trim($response)==1){
//                               moveToProcessed($chain_folder_path,$ndir,$newPo);
//                           }else if(trim($response)==2){
//                               moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo);
//                           }else{
//                               moveToIssue($chain_folder_path,$ndir,$newPo);
//                           } 
//                        }
                        if(isset($shipping_obj)){
                            // found ini file details
                            $iniid=$shipping_obj->id;
                            $responsearr = callProcessor($shipping_obj,$fpath);
                            $rarr = explode("::", $responsearr);
                            $response = $rarr[0];
                            $notifiaction = $rarr[1];
                            print "<br> RESPONSE : $response <br>";
                            if($notifiaction==1){
                               //movetoEANMISSING
                               $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo);
                               insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSIMG_EAN);
                               
                            }
                            $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo);
                            insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response);
                            
                        }
                        else{ 
                            //move to unrecognized buunit
                            $pdfname=moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo); 
                            print"<br>PDFNAME=$pdfname<br>";
                            insertstatus($db,$pdfname,$master_dealer_id,-1, POStatus::STATUS_UNRECOGNIZED_BU);
                        }
//                        else{
//                            //move to unrecognized buunit
//                         $udir = $chain_folder_path."unrecognizedBussinessUnit/";
//                            if (!file_exists($udir)) {
//                                mkdir($udir,  0777 , true);
//                            }
//                            $file_pdf = $narr[0].".pdf";
//                            $file_text = $narr[0].".txt";
//
//                            $delete =  array();
//
//                            //first move pdf file
//                            if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//                                $delete[] = $ndir.$file_pdf;
//                            }
//
//                            //than move txt file
//                            if (copy($ndir.$file_text, $udir.$file_text)) {
//                                $delete[] = $ndir.$file_text;
//                            }
//
//                            // unlink files
//                            if(! empty($delete)){
//                                foreach ($delete as $file_pdf) {
//                                    if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//                                       unlink($file_pdf);
//                                    }
//                                }
//                            } 
//                        }
                    }else{
                        print "<br> IN ELSE : <br>";
                        continue;
                    }
             }
              }
            }
        }
    }
    
}

function fetchINI($shipping_address,$master_dealer_id,$fpath,$newPo){
    $iniids=array();
    print "<br> SHIPPING ADDRESS INITIAL : $shipping_address <br>";
    $db = new DBConn();
    $no_spaces = str_replace(" ", "", $shipping_address);
    $no_spaces_db = $db->safe(trim($no_spaces));
    print "<br> NO SPACE: $no_spaces <br>";
    $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
    print "<br> CHECK : $check <br>";
    $query = " select * from it_shipping_address where $check";// and master_dealer_id = $master_dealer_id ";
    print "<br> QUERY: $query <br>";
    $sobj = $db->fetchObject($query); // isset?
    $iniid=$sobj->ini_id;
    print"<br>INIID=$iniid<br>";
    $iniids=explode(",",$iniid);
    print"<br>INIID ARRAY=";
    print_r($iniids);
    if(count($iniids)>1){
         //$iniids=$explode(",",$iniid);
            $sobj=fetchCorrectini($fpath,$newPo,$iniids,$shipping_address);   // call fetchCorrectINI
            return $sobj;
    }
    else{
        $query= "select * from it_inis where id=$iniid";
        $sobj = $db->fetchObject($query);
        return $sobj;
    }
    $db->closeConnection();    
}

//fn fetchcorrectINI{
//loop in for ini
//}
  function fetchCorrectini($fpath,$newPo,$iniids,$shipping_address){
      $db = new DBConn();
      foreach ($iniids as $id){ 
        $query="select * from it_inis where id=$id"; 
        print"QUERY-$query";
        $gobj = $db->fetchObject($query);
        $initxt=$gobj->ini_text;
        print"<br>initext=$initxt<br>";
        print"<br>FILE Directory path =$fpath<br>";
//        $filecont=$ndir ; //.$newPo;
//        print"<br>Filepath=$filecont <br>";
       // $rows= file_get_contents($filecont);
        //print"<br>filecontents=<br>$rows<br>";
        $initest=new initester();
        $shipping_address1=$initest->getIni($initxt,$fpath);
        print "<br>BU PARSER SHP ADDR: $shipping_address<br>";
        print "<br>INI TESTOR SHP ADDR: $shipping_address1<br>";
        $no_spaces1 = str_replace(" ", "", $shipping_address1);
        $no_spaces = str_replace(" ", "", $shipping_address);
        if(strcasecmp($no_spaces1, $no_spaces)==0){
            print "<br>MATCHED<br>";
            $query = " select * from it_inis where id=$id ";
            $iobj = $db->fetchObject($query);
            return $iobj;
             //break;
        }
        print "<br>NOT MATCHED<br>";
     }
     
      return null; // failure case
 }
            


//function fetchINI($shipping_address,$master_dealer_id){
//    print "<br> SHIPPING ADDRESS INITIAL : $shipping_address <br>";
//    $db = new DBConn();
//    $no_spaces = str_replace(" ", "", $shipping_address);
//    $no_spaces_db = $db->safe(trim($no_spaces));
//    print "<br> NO SPACE: $no_spaces <br>";
//    $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
//    print "<br> CHECK : $check <br>";
//    $query = " select * from it_inis where $check and master_dealer_id = $master_dealer_id ";
//    print "<br> QUERY: $query <br>";
//    $sobj = $db->fetchObject($query);
//    $db->closeConnection();
//   // call fetchCorrectINI
//    return $sobj;
//}

//fn fetchcorrectINI{
//loop in for ini
//}

function callProcessor($shipping_obj,$fpath){
   $db = new DBConn();
   print "<br> SHIPPING OBJ: <br>";
   print_r($shipping_obj);
   $initxt = $shipping_obj->ini_text;
   //$pg = DEF_SITEURL."/Parsers/processPoToDb/initxt=".$initxt;
   
   $processor = new processPOToDB();
   $response = $processor->process($fpath, $initxt);
   
   return $response;
}
function processorResponseAction($response,$chain_folder_path,$ndir,$newPo){
    
             if(trim($response)== POStatus::STATUS_PROCESSED){
                $pdfname=moveToProcessed($chain_folder_path,$ndir,$newPo);
                
             }else if(trim($response)==POStatus::STATUS_NOT_WEIKFIELD){
                   $pdfname=moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo);
                    }else{
                          $pdfname=moveToIssue($chain_folder_path,$ndir,$newPo);
                    } 
                    
                    return $pdfname;
}  

//function callProcessor($shipping_obj,$fpath){
//   $db = new DBConn();
//   print "<br> SHIPPING OBJ: <br>";
//   print_r($shipping_obj);
//   $initxt = $shipping_obj->ini_text;
//   //$pg = DEF_SITEURL."/Parsers/processPoToDb/initxt=".$initxt;
//   
//    $url = DEF_SITEURL."/Parsers/processPoToDb.php";
//    $fields = array( 'iniFile' => urlencode($initxt),
//                     'filename' => urlencode($fpath)
//            );
//
//    $fields_string="";
//    //url-ify the data for the POST
//    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
//    rtrim($fields_string, '&');
//
//    //open connection
//    $ch = curl_init();
//
//    //set the url, number of POST vars, POST data
//    curl_setopt($ch,CURLOPT_URL, $url);
//    curl_setopt($ch,CURLOPT_POST, count($fields));
//    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
//
//    //execute post
//    $result = curl_exec($ch);
//
//    print "<br>RESULT: $result <br>";
//    //close connection
//    curl_close($ch);
//   
//   
//}

function  moveToEanMissing($chain_folder_path,$ndir,$newPo){
    $udir = $chain_folder_path."eanMissingPOs/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].".pdf";
    $file_text = $narr[0].".txt";

    $pdfname=$udir.$file_pdf;
    print"<br>pdf name=$pdfname<br><br>";  
    //$delete =  array();

    //first move pdf file
    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
        //$delete[] = $ndir.$file_pdf;
    }

    //than move txt file
    if (copy($ndir.$file_text, $udir.$file_text)) {
       // $delete[] = $ndir.$file_text;
    }

    // unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    }
    print"<br>PDFNAME-MTEANmiss=$pdfname<br>";
    return $pdfname;
}

function moveToProcessed($chain_folder_path,$ndir,$newPo){
    $udir = $chain_folder_path."processed/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].".pdf";
    $file_text = $narr[0].".txt";

    $pdfname=$udir.$file_pdf;
    print"<br>pdf name=$pdfname<br><br>";
                
    $delete =  array();

    //first move pdf file
    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
        $delete[] = $ndir.$file_pdf;
    }

    //than move txt file
    if (copy($ndir.$file_text, $udir.$file_text)) {
        $delete[] = $ndir.$file_text;
    }

    // unlink files
    if(! empty($delete)){
        foreach ($delete as $file_pdf) {
            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
               unlink($file_pdf);
            }
        } 
    } 
    //print"<br>PDFNAME-MTP=$pdfname<br>";
    return $pdfname;
}

function moveToIssue($chain_folder_path,$ndir,$newPo){
    $udir = $chain_folder_path."issueAtProcessing/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].".pdf";
    $file_text = $narr[0].".txt";

    $pdfname=$udir.$file_pdf;
    print"<br>pdf name=$pdfname<br><br>"; 
    
    $delete =  array();

    //first move pdf file
    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
        $delete[] = $ndir.$file_pdf;
    }

    //than move txt file
    if (copy($ndir.$file_text, $udir.$file_text)) {
        $delete[] = $ndir.$file_text;
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


function moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo){
    $udir = $chain_folder_path."notWeikfieldPO/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].".pdf";
    $file_text = $narr[0].".txt";
    
    $pdfname=$udir.$file_pdf;
    print"<br>pdf name=$pdfname<br><br>";
    
    $delete =  array();

    //first move pdf file
    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
        $delete[] = $ndir.$file_pdf;
    }

    //than move txt file
    if (copy($ndir.$file_text, $udir.$file_text)) {
        $delete[] = $ndir.$file_text;
    }

    // unlink files
    if(! empty($delete)){
        foreach ($delete as $file_pdf) {
            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
               unlink($file_pdf);
            }
        } 
    } 
    //print"<br>PDFNAME-NOTWKF=$pdfname<br>";
    return $pdfname;
   
}

function moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo){
      $udir = $chain_folder_path."unrecognizedBussinessUnit/";
          if (!file_exists($udir)) {
                mkdir($udir,  0777 , true);
                }
                $narr = explode(".",$newPo);
                $file_pdf = $narr[0].".pdf";
                $file_text = $narr[0].".txt";
                
                $pdfname=$udir.$file_pdf;
                print"<br>pdf name=$pdfname<br><br>";
                
                $delete =  array();

                //first move pdf file
                if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
                    $delete[] = $ndir.$file_pdf;
                }
                 
                //than move txt file
                if (copy($ndir.$file_text, $udir.$file_text)) {
                    $delete[] = $ndir.$file_text;
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
function insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response){ 
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $statusinsertQ="insert into it_process_status set  pdfname='$pdfname', master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
    print"<br>q=$statusinsertQ<br>";
    $db->execInsert($statusinsertQ); 
    
}