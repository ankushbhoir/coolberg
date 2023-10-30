<?php
require_once("../../it_config.php");
require_once("headerReaderParser.php");
require_once("lib/db/DBConn.php");
require_once("processPoToDb.php");
require_once "lib/core/Constants.php";
require_once "initester.php";

$source = DEF_PROCESS_PATH;
callProcessPos($source);

//spl chk for ABRL Hyper
$abh =  DEF_PROCESS_PATH."ABRL_Hyper/newPOs/";
//print "<br> ABR PATH : $abh <br>";
if(file_exists($abh)){
    $dirs = scandir($abh);
    //print_r($dirs);
    //if(count($dirs) > 0 && !in_array(".", $dirs) && !in_array("..", $dirs)){
    if(count($dirs) > 0 ){
//        print "<br>in call again <br>";
        callProcessPos($source);
    }
}

$folder = DEF_PROCESS_PATH;
$dirs = scandir($folder);

//print_r($dirs);

foreach($dirs as $dir){
    $ubu_folder = $folder.$dir."/unrecognizedBusinessUnit/";
    if(is_dir($ubu_folder)){    
       $cnt = count(scandir($ubu_folder));
       
       if($cnt > 2){
           moveFileBack($folder,$dir);
       }
    }
}
callProcessPos($source);


function moveFileBack($folder,$dir){
    $ubu_folder = $folder.$dir."/unrecognizedBusinessUnit/";
    $newpo = $folder.$dir."/newPOs/";    
//   echo $ubu_folder;
    $newpo = str_replace("&","\&",$newpo);
    $ubu_folder = str_replace("&","\&",$ubu_folder);
    $move = "mv ". $ubu_folder."*  ".$newpo;
    shell_exec($move); 
}

function callProcessPos($source){
$db = new DBConn();
$dirs = scandir($source);
//print_r($dirs);
foreach($dirs as $dir){
   // step 1:    chain folders
        
    if(trim($dir)!="" && trim($dir)!="." && trim($dir)!=".." ){
        //print "<br> SUB dir".$dir;
        
        $c = str_replace("_", " ", $dir);
        $chain_name = $db->safe(trim($c));
        $master_dealer_id = 0;
        echo $query = "select * from it_master_dealers where name = $chain_name";
      // print "<br>$query";
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
  //          print "<br>";
    //        print_r($sub_dirs);
            
         // step 2: New POs of each chain
            $ndir = $chain_folder_path."newPOs/";
      //      print "<br>NDIR: $ndir <br>";
            $newPOsFiles = scandir($ndir);
//            print "<br>NEW PO FILES: <br>";
        //    print_r($newPOsFiles);
            
            foreach($newPOsFiles as $newPo){
             $shipping_address = "";
          //   print "<br> NEW PO: $newPo <br>"   ;
             $narr = explode(".",$newPo);
             //$newPOpdf = $narr[0].".pdf";
              if(trim($narr[1])!="txt"){
                $Ext = ".".$narr[1];
                $newPOpdf = $narr[0].$Ext;    
            //    print"<br>EXT=$Ext<br>";
             }
            // print "<br>File name -> $newPOpdf<br>"; 
             //print "<br>PDF name -> $newPOpdf<br>"; 
//             $pdfname=$newPo;
//             print"<br>PDFNAME=$pdfname<br>";
             if(trim($newPo)!="" && trim($newPo)!="." && trim($newPo) != ".."){   
              //  print "<br> INSIDE IF <br>"   ;
                echo $clsname = "cls_".$cobj->id."_buParser";
                
                //print "<br> CLSNAME: $clsname <br>";
                if(file_exists("buParsers/".$clsname.".php")){
                  //  print "<br> Print 1 <br>"   ;
                    require_once "buParsers/$clsname.php";
                    //print "<br> Print 2 : $newPo <br>"   ;
                    $narr = explode(".", $newPo);
//                    print "<br> NARR: ";
//                    print_r($narr);
                    if(trim($narr[1])=="txt"){
                      //  print "<br> IN TXT <br>";
                        $fpath = $ndir.$newPo;
                       // print "<br>FPATH: $fpath <br>";

                        //send only txt files to parser
                        $parser = new $clsname();
                        //$shipping_address = $parser->process($fpath);
                        //**** $buParserResponse = $parser->process($fpath,$master_dealer_id);
                        $buParserResponse = $parser->process($fpath);
                        $responseArray = explode("::",$buParserResponse);
                        $shipping_address = $responseArray[0];
                        $iniMasterDealerId= $responseArray[1];
                       // print "Old Directory : $fpath";
                        //print "<br>Master Dealer ID: $master_dealer_id <br/>";
                        //print "<br>INI Master Dealer ID: $iniMasterDealerId <br/>";
                        if($master_dealer_id == 3){
                            if(trim($iniMasterDealerId) != trim($master_dealer_id)){
                          //      print"<br> its a ABRL_HYPER PO<br>";
                                //$fpath=moveToABRLH($source,$chain_folder_path,$iniMasterDealerId,$newPo);
                                $rp=moveToABRLH($source,$chain_folder_path,$iniMasterDealerId,$newPo,$Ext);
                                //print "New Directory : $fpath";
                                if(trim($rp)== 1){
                                    continue;
                                }
                            }
                        }
                        ///////////////////// => new ini check logic starts here
                        $response=  POStatus::STATUS_UNRECOGNIZED_BU;
                        
                        $iniids=array();
                       // print "<br> SHIPPING ADDRESS INITIAL : $shipping_address <br>";
                       // $db = new DBConn();
                        $no_spaces = str_replace(" ", "", $shipping_address);
                        $no_spaces_db = $db->safe(trim($no_spaces));
                       // print "<br> NO SPACE: $no_spaces <br>";
                        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
                       // print "<br> CHECK : $check <br>";
                        $query = " select * from it_shipping_address where $check and master_dealer_id = $master_dealer_id ";
                        print "<br>heeeeeeeeeeeeeeeeee QUERY: $query <br>";
                        $sobj = $db->fetchObject($query); // isset?

                        if(isset($sobj) && !empty($sobj) && $sobj!= NULL){// fetch shipping_id too
                            
                            $shipping_id= $sobj->id;
                            $iniid=$sobj->ini_id;
                            print"<br>INIID=$iniid<br>";
                            $iniids=explode(",",$iniid);
                            print"<br>INIID ARRAY=";
                         //   print_r($iniids);
                            if(count($iniids)>1){
                                //multiple inis fetch all
                                echo "<br>";
                                   echo "dadas###############################";
                                   echo "<pre>";
                                $iquery = " select * from it_inis where id in ( $sobj->ini_id ) ";
                                // print_r($iquery);
                                $iniobjs = $db->fetchAllObjects($iquery);
                                $at_issue=FALSE;
                                foreach($iniobjs as $iniobj){
                                     // found ini file details
                                    $iniid=$iniobj->id;
                                    $responsearr = callProcessor($iniobj,$fpath,$master_dealer_id,$newPOpdf,$shipping_address,$shipping_id);
                                  //  echo "<br>";
                                  // echo "dadas###############################";
                                   //echo "<pre>";
                                  //  print_r($responsearr);


                                    $rarr = explode("::", $responsearr);
                                    $response = $rarr[0];
                                    $notification = $rarr[1];
                                    if(isset($rarr[2])){
                                       $issuereason= $rarr[2];
                                    }
                             //       print "<br>in if RESPONSE : $response <br>";

                                    if($notification==1){
                                       //movetoEANMISSING
//                                        if($response == POStatus::STATUS_MISSING_EAN){
                                          print"<br> in sssmissing ean + success<br>";
                                            $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
                                            insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);
//                                        }
                                    }else if($notification==2){
//                                        if($response == POStatus::STATUS_ARTICLE_NO_MISSING){
                                            print"<br> in missing article + success<br>";
                                            $pdfname= moveToArticleMissing($chain_folder_path,$ndir,$newPo,$Ext);
                                            insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_ARTICLE_NO_MISSING,$newPOpdf);
//                                        }
                                    }
                                    
                                    if($response != POStatus::STATUS_ISSUE_AT_PROCESSING && $response != POStatus::STATUS_UNRECOGNIZED_BU ){//responce blank chk?
                                        $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                                        insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
                                        $at_issue=FALSE; //at iss=false 
                                         break;
                                    }else{
                                   //     print"<br> in if PO has some Issue or UBU set at_issue=TRUE";
                                        $at_issue=TRUE; //at iss=tru;
                                        continue;
                                    }
                                }
                            }else{
                                $notification = 0;
                                    //one ini process by it 
                                echo "only ine inii";
                                $iquery = " select * from it_inis where id in ( $sobj->ini_id ) ";
                                $iniobj = $db->fetchObject($iquery);
                                $at_issue=FALSE;//at iss=false;
                                //foreach($iniobjs as $iniobj){
                                     // found ini file details
                                    $iniid=$iniobj->id;
                                    $responsearr = callProcessor($iniobj,$fpath,$master_dealer_id,$newPOpdf,$shipping_address,$shipping_id);
                                    echo "###OOOO";
                                    echo "<br>";
                                    //print_r($responsearr);
                                    $rarr = explode("::", $responsearr);
                                    //print_r($rarr);
                                    $response = $rarr[0];
                                     $notification = isset($rarr[1])? ($rarr[1]) : 0;
                                     echo "noti".$notification;
                                    if(isset($rarr[2])){
                                       $issuereason= $rarr[2];
                                    }
                                    //print "<br> in else RESPONSE : $response <br>";
                                    if($notification==1){
                                       //movetoEANMISSING
    //                                   $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
    //                                   insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);
                                      //      print"<br> in missing ean + success<br>";
                                            $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
                                            insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);
                                    }else if($notification==2){
                                           print"<br> in missing article + success<br>";
                                            $pdfname= moveToArticleMissing($chain_folder_path,$ndir,$newPo,$Ext);
                                            insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_ARTICLE_NO_MISSING,$newPOpdf);
                                    }
                                    if($response != POStatus::STATUS_ISSUE_AT_PROCESSING && $response != POStatus::STATUS_UNRECOGNIZED_BU ){
                                     
                                        $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                                        
                                        //echo $db;
                                        echo $pdfname;
                                        echo $iniid;
                                        echo $response;
                                        echo $newPOpdf;
                                        insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
                                        $at_issue=FALSE;
                                        //break;
                                    }else{
                                        //print"<br>in else PO has some Issue or UBU  set at_issue=TRUE";
                                        $at_issue=TRUE;  //at iss=tru;
                                        //continue;
                                    }
                               // }
                            }
                    }else{
                        // print"<br>in UBU  set at_issue=TRUE";
                         $iniid=-1;
                         $at_issue=TRUE; 
                    }
                        if( $at_issue==TRUE){
                          //  print"<br>at issue PO has some Issue or UBU";
                            // print "<br> at issue RESPONSE : $response <br>";
                            $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                            insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf,$issuereason);
                        }
                        ///////////////////////// => new ini check logic end here
                        
                       /////////////////////////////// => old logic
                       // $shipping_obj = fetchINI($shipping_address,$iniMasterDealerId);
                       /* $shipping_obj = fetchINI($shipping_address,$master_dealer_id,$fpath,$newPo);
                        if(isset($shipping_obj)){
                            // found ini file details
                            $iniid=$shipping_obj->id;
                            $responsearr = callProcessor($shipping_obj,$fpath,$master_dealer_id,$newPOpdf);
                            $rarr = explode("::", $responsearr);
                            $response = $rarr[0];
                            $notification = $rarr[1];
                            print "<br> RESPONSE : $response <br>";
                            if($notification==1){
                               //movetoEANMISSING
                               $pdfname= moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext);
                               insertstatus($db,$pdfname,$master_dealer_id,$iniid, POStatus::STATUS_MISSING_EAN,$newPOpdf);
                               
                            }
                            
                            $pdfname=processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext);
                            insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf);
                            
                        }
                        else{ 
                            //move to unrecognized buunit
                            $pdfname=moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo,$Ext); 
                            print"<br>PDFNAME=$pdfname<br>";
                            insertstatus($db,$pdfname,$master_dealer_id,-1, POStatus::STATUS_UNRECOGNIZED_BU,$newPOpdf);
                        }*/
                        
                        /////////////////////////////// old logic ends here
                    }else{
                        //print "<br> IN ELSE : <br>";
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
//    print "<br>IN FN moveToABRLH<br>";
    // $folderPath = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/movedFiles/"; 
    echo $query = "select * from it_master_dealers where id=$iniMasterDealerId ";
  //    print "<br>$query";
    //exit;
      $db=new DBConn();
      $delete1 =  array();
      $pobj = $db->fetchObject($query);
    //  print "<br>";
      //print_r($pobj);
      if(isset($pobj)){
            $ch = str_replace(" ", "_",$pobj->name);
           // $dirpath = $folderPath.$ch."/";  
            //step 1 : create ABRL Hyper Chain folder , if not exist
           $new_dirpath = $source.$ch."/";          
        //    print "<br>DIR PATH: $new_dirpath ";
            // first chain dir
            if (!file_exists($new_dirpath)) {
                mkdir($new_dirpath,  0777 , true);
            }
            
            //Then new POs folder under ABRL Hyper
            $sub_folder = "newPOs/";
            //print "<br>FPATH : $chain_folder_path";
            $npodirpath = $new_dirpath.$sub_folder;          
          //  print "<br>DIR PATH SUB FOLDER : $npodirpath";
            if (!file_exists($npodirpath)) {
                mkdir($npodirpath,  0777 , true);
            }
            
            //file names
            $narr = explode(".",$newPo);
            $file_pdf = $narr[0].$Ext;
            $file_text = $narr[0].".txt";
            
            //fetch ABRL Super path
            $readFromDir = $chain_folder_path.$sub_folder;

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
    
    //print "<br>NEW PO DIR IN FN: $npodirpath <br/>";
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

// function fetchINI($shipping_address,$master_dealer_id,$fpath,$newPo){
//    $iniids=array();
//    print "<br> SHIPPING ADDRESS INITIAL : $shipping_address <br>";
//    $db = new DBConn();
//    $no_spaces = str_replace(" ", "", $shipping_address);
//    $no_spaces_db = $db->safe(trim($no_spaces));
//    print "<br> NO SPACE: $no_spaces <br>";
//    $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
//    print "<br> CHECK : $check <br>";
//    $query = " select * from it_shipping_address where $check";// and master_dealer_id = $master_dealer_id ";
//    print "<br> QUERY: $query <br>";
//    $sobj = $db->fetchObject($query); // isset?
//    $iniid=$sobj->ini_id;
//    print"<br>INIID=$iniid<br>";
//    $iniids=explode(",",$iniid);
//    print"<br>INIID ARRAY=";
//    print_r($iniids);
//    if(count($iniids)>1){
//         //$iniids=$explode(",",$iniid);
//            $sobj=fetchCorrectini($fpath,$newPo,$iniids,$shipping_address);   // call fetchCorrectINI
//            return $sobj;
//    }
//    else{
//        $query= "select * from it_inis where id=$iniid";
//        $sobj = $db->fetchObject($query);
//        return $sobj;
//    }
//    $db->closeConnection();    
//}

// function fetchCorrectini($fpath,$newPo,$iniids,$shipping_address){
//      $db = new DBConn();
//      foreach ($iniids as $id){ 
//        $query="select * from it_inis where id=$id"; 
//        print"QUERY-$query";
//        $gobj = $db->fetchObject($query);
//        $initxt=$gobj->ini_text;
//        print"<br>initext=$initxt<br>";
//        print"<br>FILE Directory path =$fpath<br>";
////        $filecont=$ndir ; //.$newPo;
////        print"<br>Filepath=$filecont <br>";
//       // $rows= file_get_contents($filecont);
//        //print"<br>filecontents=<br>$rows<br>";
//        $initest=new initester();
//        $shipping_address1=$initest->getIni($initxt,$fpath);
//        print "<br>BU PARSER SHP ADDR: $shipping_address<br>";
//        print "<br>INI TESTOR SHP ADDR: $shipping_address1<br>";
//        $no_spaces1 = str_replace(" ", "", $shipping_address1);
//        $no_spaces = str_replace(" ", "", $shipping_address);
//        if(strcasecmp($no_spaces1, $no_spaces)==0){
//            print "<br>MATCHED<br>";
//            require_once("util/itemsINI.php");
//            $chkitem= new itemsINI();
//            $items= $chkitem->iprocess($fpath,$initxt);
//             //if cond to chk items
//            //itm>0{
//            print"ITEMS RETURN BY ITEMSINI:<br>";
//            print_r($items); print"<br>";
//            $noofitems=0; 
//            $noofitems=count($items);
//            print"<br>no of items in PO=$noofitems<br>";
//            if($noofitems>0){
//                   print"<br>Item present<br>";
//            $query = " select * from it_inis where id=$id ";
//            $iobj = $db->fetchObject($query);
//            return $iobj;
//             //break;
//        }
//        }
//        print "<br>NOT MATCHED<br>";
//     }
//     
//      return null; // failure case
// }
//fn fetchcorrectINI{
//loop in for ini
//}

function callProcessor($shipping_obj,$fpath,$master_dealer_id,$newPOpdf,$shipping_address,$shipping_id){
   $db = new DBConn();
   // print "<br> SHIPPING OBJ: <br>";
  // print_r($shipping_obj);
   $initxt = $shipping_obj->ini_text;
   $iniid = $shipping_obj->id;
   //$pg = DEF_SITEURL."/Parsers/processPoToDb/initxt=".$initxt;
   
   $processor = new processPOToDB();
   $response = $processor->process($fpath, $initxt,$master_dealer_id,$iniid,$newPOpdf,$shipping_address,$shipping_id);
 //   print "\n got $response*****************************\n";
   return $response;
}

function processorResponseAction($response,$chain_folder_path,$ndir,$newPo,$Ext){
  //  print "\n in $response*****************************\n";
    if(trim($response)!=''){
     //   print "\n in movefile*****************************\n";
        $pdfname=movefile($chain_folder_path,$ndir,$newPo,$Ext,$response);  
        return $pdfname;
    } 
//    if(trim($response)== POStatus::STATUS_PROCESSED){
//       $pdfname=moveToProcessed($chain_folder_path,$ndir,$newPo,$Ext);
//    }else if(trim($response)==POStatus::STATUS_NOT_WEIKFIELD){
//          $pdfname=moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo,$Ext);
//    }else if(trim($response) == POStatus::STATUS_MISSING_EAN){
//          $pdfname=moveToEanMissingR($chain_folder_path,$ndir,$newPo,$Ext);
//    }else if(trim($response) == POStatus::STATUS_ARTICLE_NO_MISSING){
//          $pdfname=moveToArticleMissingR($chain_folder_path,$ndir,$newPo,$Ext);
//    }else if(trim($response) == POStatus::STATUS_DUPLICATE_PO){
//          $pdfname=moveToDuplicatePO($chain_folder_path,$ndir,$newPo,$Ext);
//    }else if(trim($response) == POStatus::STATUS_UNRECOGNIZED_BU){
//          $pdfname=moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo,$Ext);
//    }else{
//           $pdfname=moveToIssue($chain_folder_path,$ndir,$newPo,$Ext);
//    } 
    
}  
function movefile($chain_folder_path,$ndir,$newPo,$Ext,$response){
   // print "\n in movefile*****************************\n";
    $udir = $chain_folder_path.statusFolder::getStatusMsg($response)."/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
    $file_text = $narr[0].".txt";

    $pdfname=$udir.$file_pdf;
 //   print"<br>pdf name=$pdfname<br><br>";
                
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
  //  print"<br>PDFNAME-MTP=$pdfname<br>";
    return $pdfname;
}



function  moveToEanMissing($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."eanMissingPOs/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
    $file_text = $narr[0].".txt";

    $pdfname=$udir.$file_pdf;
    //print"<br>pdf name=$pdfname<br><br>";  
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
//    print"<br>PDFNAME-MTEANmiss=$pdfname<br>";
    return $pdfname;
}

function  moveToArticleMissing($chain_folder_path,$ndir,$newPo,$Ext){
    $udir = $chain_folder_path."ArticleNoMissingPOs/";
    if (!file_exists($udir)) {
        mkdir($udir,  0777 , true);
    }
    $narr = explode(".",$newPo);
    $file_pdf = $narr[0].$Ext;
    $file_text = $narr[0].".txt";

    $pdfname=$udir.$file_pdf;
    //print"<br>pdf name=$pdfname<br><br>";  
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
    //print"<br>PDFNAME-Articlemissing$pdfname=$pdfname<br>";
    return $pdfname;
}

//function  moveToEanMissingR($chain_folder_path,$ndir,$newPo,$Ext){
//    $udir = $chain_folder_path."eanMissingPOs/";
//    if (!file_exists($udir)) {
//        mkdir($udir,  0777 , true);
//    }
//    $narr = explode(".",$newPo);
//    $file_pdf = $narr[0].$Ext;
//    $file_text = $narr[0].".txt";
//
//    $pdfname=$udir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";  
//    $delete =  array();
//
//    //first move pdf file
//    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//        $delete[] = $ndir.$file_pdf;
//    }
//
//    //than move txt file
//    if (copy($ndir.$file_text, $udir.$file_text)) {
//        $delete[] = $ndir.$file_text;
//    }
//
////     unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    }
//    print"<br>PDFNAME-MTEANmiss=$pdfname<br>";
//    return $pdfname;
//}
//function moveToArticleMissingR($chain_folder_path,$ndir,$newPo,$Ext){
//    $udir = $chain_folder_path."articleMissingPOs/";
//    if (!file_exists($udir)) {
//        mkdir($udir,  0777 , true);
//    }
//    $narr = explode(".",$newPo);
//    $file_pdf = $narr[0].$Ext;
//    $file_text = $narr[0].".txt";
//
//    $pdfname=$udir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";  
//    $delete =  array();
//
//    //first move pdf file
//    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//        $delete[] = $ndir.$file_pdf;
//    }
//
//    //than move txt file
//    if (copy($ndir.$file_text, $udir.$file_text)) {
//        $delete[] = $ndir.$file_text;
//    }
//
////     unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    }
//    print"<br>PDFNAME-Articlemissing$pdfname<br>";
//    return $pdfname;
//}
//
//function moveToProcessed($chain_folder_path,$ndir,$newPo,$Ext){
//    $udir = $chain_folder_path."processed/";
//    if (!file_exists($udir)) {
//        mkdir($udir,  0777 , true);
//    }
//    $narr = explode(".",$newPo);
//    $file_pdf = $narr[0].$Ext;
//    $file_text = $narr[0].".txt";
//
//    $pdfname=$udir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";
//                
//    $delete =  array();
//
//    //first move pdf file
//    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//        $delete[] = $ndir.$file_pdf;
//    }
//
//    //than move txt file
//    if (copy($ndir.$file_text, $udir.$file_text)) {
//        $delete[] = $ndir.$file_text;
//    }
//
//    // unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    } 
//    //print"<br>PDFNAME-MTP=$pdfname<br>";
//    return $pdfname;
//}
//
//function moveToDuplicatePO($chain_folder_path,$ndir,$newPo,$Ext){
//    $udir = $chain_folder_path."duplicatePO/";
//    if (!file_exists($udir)) {
//        mkdir($udir,  0777 , true);
//    }
//    $narr = explode(".",$newPo);
//    $file_pdf = $narr[0].$Ext;
//    $file_text = $narr[0].".txt";
//
//    $pdfname=$udir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";
//                
//    $delete =  array();
//
//    //first move pdf file
//    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//        $delete[] = $ndir.$file_pdf;
//    }
//
//    //than move txt file
//    if (copy($ndir.$file_text, $udir.$file_text)) {
//        $delete[] = $ndir.$file_text;
//    }
//
//    // unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    } 
//    //print"<br>PDFNAME-MTP=$pdfname<br>";
//    return $pdfname;
//}
//
//function moveToIssue($chain_folder_path,$ndir,$newPo,$Ext){
//    $udir = $chain_folder_path."issueAtProcessing/";
//    if (!file_exists($udir)) {
//        mkdir($udir,  0777 , true);
//    }
//    $narr = explode(".",$newPo);
//    $file_pdf = $narr[0].$Ext;
//    $file_text = $narr[0].".txt";
//
//    $pdfname=$udir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>"; 
//    
//    $delete =  array();
//
//    //first move pdf file
//    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//        $delete[] = $ndir.$file_pdf;
//    }
//
//    //than move txt file
//    if (copy($ndir.$file_text, $udir.$file_text)) {
//        $delete[] = $ndir.$file_text;
//    }
//
//    // unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    }    
//    return $pdfname;
//}
//
//
//function moveToNotWeikfieldPO($chain_folder_path,$ndir,$newPo,$Ext){
//    $udir = $chain_folder_path."notWeikfieldPO/";
//    if (!file_exists($udir)) {
//        mkdir($udir,  0777 , true);
//    }
//    $narr = explode(".",$newPo);
//    $file_pdf = $narr[0].$Ext;
//    $file_text = $narr[0].".txt";
//    
//    $pdfname=$udir.$file_pdf;
//    print"<br>pdf name=$pdfname<br><br>";
//    
//    $delete =  array();
//
//    //first move pdf file
//    if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//        $delete[] = $ndir.$file_pdf;
//    }
//
//    //than move txt file
//    if (copy($ndir.$file_text, $udir.$file_text)) {
//        $delete[] = $ndir.$file_text;
//    }
//
//    // unlink files
//    if(! empty($delete)){
//        foreach ($delete as $file_pdf) {
//            if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//               unlink($file_pdf);
//            }
//        } 
//    } 
//    //print"<br>PDFNAME-NOTWKF=$pdfname<br>";
//    return $pdfname;
//   
//}

//function moveToUnrecognizedBUnit($chain_folder_path,$ndir,$newPo,$Ext){
//      $udir = $chain_folder_path."unrecognizedBussinessUnit/";
//          if (!file_exists($udir)) {
//                mkdir($udir,  0777 , true);
//                }
//                $narr = explode(".",$newPo);
//                $file_pdf = $narr[0].$Ext;
//                $file_text = $narr[0].".txt";
//                
//                $pdfname=$udir.$file_pdf;
//                print"<br>pdf name=$pdfname<br><br>";
//                
//                $delete =  array();
//
//                //first move pdf file
//                if (copy($ndir.$file_pdf, $udir.$file_pdf)) {
//                    $delete[] = $ndir.$file_pdf;
//                }
//                 
//                //than move txt file
//                if (copy($ndir.$file_text, $udir.$file_text)) {
//                    $delete[] = $ndir.$file_text;
//                }
//
//                // unlink files
//                if(! empty($delete)){
//                    foreach ($delete as $file_pdf) {
//                        if(trim($file_pdf)!="" && trim($file_pdf)!="." && trim($file_pdf) != ".."){  
//                            unlink($file_pdf);
//                        }
//                    }
//                }
//             
//                 return $pdfname;
//}
/*function insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response){ 
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $statusinsertQ="insert into it_process_status set  pdfname='$pdfname', master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
    print"<br>q=$statusinsertQ<br>";
    $db->execInsert($statusinsertQ); 
    
}*/

function insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$newPOpdf,$issuereason=FALSE){ 
    $issuecls="";
    if($issuereason != FALSE && $response == POStatus::STATUS_ISSUE_AT_PROCESSING){
      //  print "\n in issuecls************\n";
        $issuereason_db = $db->safe(IssueReason::getIssueMsg($issuereason));
        $issuecls= ",issue_reason= $issuereason_db";
    }
    $filelen=0;
    $pocntcls="";
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $flprts= explode(".",$pdfname);
    $txtflname= $flprts[0].".txt";
    if(file_exists($txtflname)){
        $filelen= count(file($txtflname));
    }
    //print_r(file($txtflname));
    //print" \n file length: $filelen: $txtflname \n\n";         
    if($filelen > 0){
        if($filelen > 100){
            $noofpos= ceil($filelen/100);
           // print"\n >100 noofpos:$noofpos \n";
        }else {
           $noofpos=1; 
         //  print"\n <100 noofpos:$noofpos \n";
        } 
        $pocntcls= ", noofpos= $noofpos";
    }
    $filename=$newPOpdf;
//    print"<br>Filename=$filename<br>";
    $filenameparts= explode("/",$filename);
    $filenamepartsrev= array_reverse($filenameparts);
    $id_fname=$filenamepartsrev[0];
//    print"<br>id_fname=$id_fname<br>";
    $filename_db=$db->safe($id_fname);
    $updtcurrstatusQ="update it_process_status set is_current_status = 0 where filename=$filename_db and is_current_status = 1 ";//and status not in(".POStatus::STATUS_DUPLICATE_PO .")";
//    print"<br>process_status_updated=$updtcurrstatusQ<br>";
    $db->execUpdate($updtcurrstatusQ);
    //$statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id, ini_id=$iniid, is_current_status = 1, status=$response, createtime= $createtime $pocntcls";
   echo $statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id, ini_id=$iniid, is_current_status = 1, status=$response, createtime= $createtime $pocntcls $issuecls";
//    print"<br>process_status=$statusinsertQ<br>";

    $db->execInsert($statusinsertQ);      

        echo $updtepodetails="update it_po_details set status = $response,fullpath='$pdfname' where new_filename=$filename_db and datetime like '".date('Y-m-d')."%' ";
//    print"<br>process_status=$statusinsertQ<br>";
    $db->execInsert($updtepodetails);
}
