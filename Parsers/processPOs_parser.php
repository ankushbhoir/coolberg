<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once("headerReaderParser.php");
require_once("lib/db/DBConn.php");
require_once("processPoToDb.php");
require_once "lib/core/Constants.php";
require_once "initester.php";
//$file_pdf = "HERITAGE.PDF";
//$file_text = "H.TXT";
//$source = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/movedFiles/";
$source = DEF_PROCESS_PATH;
callProcessPos($source);

//spl chk for ABRL Hyper
$abh =  DEF_PROCESS_PATH."ABRL_Hyper/newPOs/";
print "<br> ABR PATH : $abh <br>";
if(file_exists($abh)){
    $dirs = scandir($abh);
    print_r($dirs);
    //if(count($dirs) > 0 && !in_array(".", $dirs) && !in_array("..", $dirs)){
    if(count($dirs) > 0 ){
        print "<br>in call again <br>";
        callProcessPos($source);
    }
}


function callProcessPos($source){
$db = new DBConn();
$dirs = scandir($source);
print_r($dirs);
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
//            require_once("buParsers/".$buParser);
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
             $narr = explode(".",$newPo);
             //$newPOpdf = $narr[0].".pdf";
              if(trim($narr[1])!="txt"){
                $Ext = ".".$narr[1];
                $newPOpdf = $narr[0].$Ext;    
                print"<br>EXT=$Ext<br>";
             }
             print "<br>File name -> $newPOpdf<br>"; 
             //print "<br>PDF name -> $newPOpdf<br>"; 
//             $pdfname=$newPo;
//             print"<br>PDFNAME=$pdfname<br>";
             if(trim($newPo)!="" && trim($newPo)!="." && trim($newPo) != ".."){   
                print "<br> INSIDE IF <br>"   ;
                $clsname = "cls_".$cobj->id."_buParser";
                print "<br> CLSNAME: $clsname <br>";
                if(file_exists("buParsers/".$clsname.".php")){
                    print "<br> Print 1 <br>"   ;
                    require_once "buParsers/$clsname.php";
                    print "<br> Print 2 : $newPo <br>"   ;
                    $narr = explode(".", $newPo);
                    print "<br> NARR: ";
                    print_r($narr);
                    if(trim($narr[1])=="txt"){
    //                    if (!@include("buParsers/$clsname.php")) {
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
                        //$shipping_address = $parser->process($fpath);
                        
                        $buParserResponse = $parser->process($fpath);
                        $responseArray = explode("::",$buParserResponse);
                        $shipping_address = $responseArray[0];
                        $iniMasterDealerId= $responseArray[1];
                        print "Old Directory : $fpath";
                        print "<br>Master Dealer ID: $master_dealer_id <br/>";
                        print "<br>INI Master Dealer ID: $iniMasterDealerId <br/>";
                        if(trim($iniMasterDealerId) != trim($master_dealer_id)){
                            //$fpath=moveToABRLH($source,$chain_folder_path,$iniMasterDealerId,$newPo);
                            $rp=moveToABRLH($source,$chain_folder_path,$iniMasterDealerId,$newPo,$Ext);
                            //print "New Directory : $fpath";
                            if(trim($rp)== 1){
                                continue;
                            }
                        }
                                             
                        ///////////////////// => new ini check logic starts here
                        $response='';
                        $iniids=array();
                        print "<br> SHIPPING ADDRESS INITIAL : $shipping_address <br>";
                       // $db = new DBConn();
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
                            //multiple inis fetch all
                            $iquery = " select * from it_inis where id in ( $sobj->ini_id ) ";
                            $iniobjs = $db->fetchAllObjects($iquery);
                            $at_issue=FALSE;
                            foreach($iniobjs as $iniobj){
                                 // found ini file details
                                $iniid=$iniobj->id;
                                $responsearr = callProcessor($iniobj,$fpath,$master_dealer_id,$newPOpdf);
                                $rarr = explode("::", $responsearr);
                                $response = $rarr[0];
                                $notifiaction = $rarr[1];
                                print "<br> RESPONSE : $response <br>";
                                if($notifiaction==1){
                                   //movetoEANMISSING
                                   $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
                                   insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);

                                }
                                if($response != POStatus::STATUS_ISSUE_AT_PROCESSING && $response != POStatus::STATUS_UNRECOGNIZED_BU ){//responce blank chk?
                                    $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                                    insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
                                    $at_issue=FALSE; //at iss=false 
                                }else{
                                    $at_issue=TRUE; //at iss=tru;
                                    continue;
                                }
                            }
                            //chk at issuu and move aacording to responce
                        }else{
                            //one ini process by it 
                            $iquery = " select * from it_inis where id in ( $sobj->ini_id ) ";
                            $iniobj = $db->fetchObject($iquery);
                             $at_issue=FALSE;//at iss=false;
                            //foreach($iniobjs as $iniobj){
                                 // found ini file details
                                $iniid=$iniobj->id;
                                $responsearr = callProcessor($iniobj,$fpath,$master_dealer_id,$newPOpdf);
                                $rarr = explode("::", $responsearr);
                                $response = $rarr[0];
                                $notifiaction = $rarr[1];
                                print "<br> RESPONSE : $response <br>";
                                if($notifiaction==1){
                                   //movetoEANMISSING
                                   $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
                                   insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);

                                }
                                if($response != POStatus::STATUS_ISSUE_AT_PROCESSING && $response != POStatus::STATUS_UNRECOGNIZED_BU ){
                                    $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                                    insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
                                    $at_issue=FALSE;
                                }else{
                                    $at_issue=TRUE;  //at iss=tru;
                                    //continue;
                                }
                           // }
                        }
                        if( $at_issue==TRUE){
                            $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                            insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
                        }
                        ///////////////////////// => new ini check logic end here
                        
                       /////////////////////////////// => old logic
                       // $shipping_obj = fetchINI($shipping_address,$iniMasterDealerId);
//                         
//                        if($master_dealer_id==3 || $master_dealer_id==4){
//                            $clsprsrname = "cls_".$master_dealer_id."_parser";
//                            print "<br> PARSERNAME: $clsprsrname <br>";
//                            if(file_exists("ABRLParsers/".$clsprsrname.".php")){
//                                print "<br> Print 1 <br>"   ;
//                                require_once "ABRLParsers/$clsprsrname.php";
//                                $parserobj = new $clsprsrname();
//                                $clsprsrnameresp=$parserobj->process($fpath);// pass file path to parse
//                            }
//
//                        }
//                        else{
//                            $shipping_obj = fetchINI($shipping_address,$master_dealer_id,$fpath,$newPo);
//                            if(isset($shipping_obj)){
//                                // found ini file details
//                                $iniid=$shipping_obj->id;
//                                $responsearr = callProcessor($shipping_obj,$fpath,$master_dealer_id,$newPOpdf);
//                                $rarr = explode("::", $responsearr);
//                                $response = $rarr[0];
//                                $notifiaction = $rarr[1];
//                                print "<br> RESPONSE : $response <br>";
//                                if($notifiaction==1){
//                                   //movetoEANMISSING
//                                   $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
//                                   insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);
//
//                                }
//
//                                $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
//                                insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
//
//                            }
//                            else{ 
//                                //move to unrecognized buunit
//                                $pdfname=moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo,$Ext); 
//                                print"<br>PDFNAME=$pdfname<br>";
//                                insertstatus($db,$pdfname,$master_dealer_id,-1, POStatus::STATUS_UNRECOGNIZED_BU,$newPOpdf);
//                            }
//                        }
                        /////////////////////////////// old logic ends here
                    }else{
                        print "<br> IN ELSE : <br>";
                        //
                        continue;
                    }
             }
              }
            }
            foreach($newPOsFiles as $newPo)
            {
                if(trim($newPo)!="" && trim($newPo)!="." && trim($newPo) != ".."){
                 //insertstatus($db,$ndir.$newPOpdf,$master_dealer_id,-1, POStatus::STATUS_NEW_PO);
                    if(file_exists($ndir.$newPo)){
                     insertstatus($db,$ndir.$newPOpdf,$master_dealer_id,-1, POStatus::STATUS_NEW_PO,$newPOpdf);
                    }
                }
            }
        }
    }
    
}
}
//Making new folder for ABRL_Hyper
function moveToABRLH($source,$chain_folder_path , $iniMasterDealerId , $newPo ,$Ext){  
    print "<br>IN FN moveToABRLH<br>";
    // $folderPath = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/movedFiles/"; 
     $query = "select * from it_master_dealers where id=$iniMasterDealerId ";
      print "<br>$query";
      $db=new DBConn();
      $delete1 =  array();
      $pobj = $db->fetchObject($query);
      print "<br>";
      print_r($pobj);
      if(isset($pobj)){
            $ch = str_replace(" ", "_",$pobj->name);
           // $dirpath = $folderPath.$ch."/";  
            //step 1 : create ABRL Hyper Chain folder , if not exist
           $new_dirpath = $source.$ch."/";          
            print "<br>DIR PATH: $new_dirpath ";
            // first chain dir
            if (!file_exists($new_dirpath)) {
                mkdir($new_dirpath,  0777 , true);

            }
            
            //Then new POs folder under ABRL Hyper
            $sub_folder = "newPOs/";
            //print "<br>FPATH : $chain_folder_path";
            $npodirpath = $new_dirpath.$sub_folder;          
            print "<br>DIR PATH SUB FOLDER : $npodirpath";
            if (!file_exists($npodirpath)) {
                mkdir($npodirpath,  0777 , true);

            }
            
            //file names
            $narr = explode(".",$newPo);
            $file_pdf = $narr[0].$Ext;
            $file_text = $narr[0].".txt";
            
            //fetch ABRL Super path
            $readFromDir = $chain_folder_path.$sub_folder;
            
            //$pdfname=$dirpaths.$file_pdf;
           // $pdfname=$readFromDir.$file_pdf;
            //$pdfnew = $dirpaths.$file_pdf; 
//            print"<br>Read From pdf name=$pdfname<br><br>";  
       //move the pdf
       //if (copy($fpath,$dirpaths.$file_pdf)) {
        if (copy($readFromDir.$file_pdf,$npodirpath.$file_pdf)) {    
            //$delete1[$fpath] = $fpath;
            $delete1[$readFromDir.$file_pdf] = $readFromDir.$file_pdf;
        }
        
        //move the txt file
        if (copy($readFromDir.$file_text, $npodirpath.$file_text)) {
         $delete1[$readFromDir.$file_text] = $readFromDir.$file_text;
        }
        $db->closeConnection();
        
        // unlink files
    if(! empty($delete1)){
        foreach ($delete1 as $file_pdf) {
            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
               unlink($file_pdf);
            }
        } 
    }
   /* $response_arr = array();
    
    //Step 3.1 : Rename Chain folder path
    $chain_folder_path = $new_dirpath;
    
    //step 3.2 : Rename ndir path
    $ndir = $npodirpath;
    
    //step 3.3 is change the spc file path
    $fpath = $npodirpath.$file_text; */
    
    print "<br>NEW PO DIR IN FN: $npodirpath <br/>";
       // return $npodirpath.$file_text; 
    return 1;
}
}

/* comented for multiple ini change -> 16-08-2016
function fetchINI($shipping_address,$master_dealer_id){
    print "<br> SHIPPING ADDRESS INITIAL : $shipping_address <br>";
    $db = new DBConn();
    $no_spaces = str_replace(" ", "", $shipping_address);
    $no_spaces_db = $db->safe(trim($no_spaces));
    print "<br> NO SPACE: $no_spaces <br>";
    $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
    print "<br> CHECK : $check <br>";
    $query = " select * from it_inis where $check and master_dealer_id = $master_dealer_id ";
    print "<br> QUERY: $query <br>";
    $sobj = $db->fetchObject($query);
    $db->closeConnection();
   // call fetchCorrectINI
    return $sobj;
}*/

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
            require_once("util/itemsINI.php");
            $chkitem= new itemsINI();
            $items= $chkitem->iprocess($fpath,$initxt);
             //if cond to chk items
            //itm>0{
            print"ITEMS RETURN BY ITEMSINI:<br>";
            print_r($items); print"<br>";
            $noofitems=0; 
            $noofitems=count($items);
            print"<br>no of items in PO=$noofitems<br>";
            if($noofitems>0){
                   print"<br>Item present<br>";
            $query = " select * from it_inis where id=$id ";
            $iobj = $db->fetchObject($query);
            return $iobj;
             //break;
        }
        }
        print "<br>NOT MATCHED<br>";
     }
     
      return null; // failure case
 }
//fn fetchcorrectINI{
//loop in for ini
//}

function callProcessor($shipping_obj,$fpath,$master_dealer_id,$newPOpdf){
   $db = new DBConn();
   print "<br> SHIPPING OBJ: <br>";
   print_r($shipping_obj);
   $initxt = $shipping_obj->ini_text;
   $iniid = $shipping_obj->id;
   //$pg = DEF_SITEURL."/Parsers/processPoToDb/initxt=".$initxt;
   
   $processor = new processPOToDB();
   $response = $processor->process($fpath, $initxt,$master_dealer_id,$iniid,$newPOpdf);
   
   return $response;
}
function processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext){
    
             if(trim($response)== POStatus::STATUS_PROCESSED){
                $pdfname=moveToProcessed($chain_folder_path,$ndir,$newPo,$Ext);
                
             }else if(trim($response)==POStatus::STATUS_NOT_WEIKFIELD){
                   $pdfname=moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo,$Ext);
            }else if(trim($response) == POStatus::STATUS_MISSING_EAN){
                   $pdfname=moveToEanMissingR($chain_folder_path,$ndir,$newPo,$Ext);
             }else if(trim($response) == POStatus::STATUS_DUPLICATE_PO){
                   $pdfname=moveToDuplicatePO($chain_folder_path,$ndir,$newPo,$Ext);
             }else{
                          $pdfname=moveToIssue($chain_folder_path,$ndir,$newPo,$Ext);
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

function  moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."eanMissingPOs/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
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


function  moveToEanMissingR($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."eanMissingPOs/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
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

//     unlink files
    if(! empty($delete)){
        foreach ($delete as $file_pdf) {
            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
               unlink($file_pdf);
            }
        } 
    }
    print"<br>PDFNAME-MTEANmiss=$pdfname<br>";
    return $pdfname;
}

function moveToProcessed($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."processed/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
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

function moveToDuplicatePO($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."duplicatePO/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
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

function moveToIssue($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."issueAtProcessing/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
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


function moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."notWeikfieldPO/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
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

function moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo,$Ext){
      $udir = $chain_folder_path."unrecognizedBussinessUnit/";
          if (!file_exists($udir)) {
                mkdir($udir,  0777 , true);
                }
                $narr = explode(".",$newPo);
                $file_pdf = $narr[0].$Ext;
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
/*function insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response){ 
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $statusinsertQ="insert into it_process_status set  pdfname='$pdfname', master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
    print"<br>q=$statusinsertQ<br>";
    $db->execInsert($statusinsertQ); 
    
}*/

function insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf){ 
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $filename=$newPOpdf;
    print"<br>Filename=$filename<br>";
    $filenameparts= explode("/",$filename);
    $filenamepartsrev= array_reverse($filenameparts);
    $id_fname=$filenamepartsrev[0];
    print"<br>id_fname=$id_fname<br>";
    $filename_db=$db->safe($id_fname);
    $statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
    print"<br>process_status=$statusinsertQ<br>";
    $db->execInsert($statusinsertQ);     
}