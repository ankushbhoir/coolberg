<?php
require_once("../../it_config.php");
require_once("../Parsers/headerReaderParser.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";

//$source = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/receivedPO/";
$source = DEF_READ_PATH;
$files = scandir($source);
//print_r($files);
//$destination = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/movedFiles/";
$destination = DEF_PROCESS_PATH;
//$unrecognizedChain  = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/unrecognizedChain/";
$unrecognizedChain = DEF_UC_PATH;
//$junkFiles = "/var/www/weikfield_DT_Auto_Niket/home/Parsers/junkFiles/";
$junkFiles = DEF_JF_PATH;
$hd = new headerReaderParser();
$db = new DBConn();
$delete = array();
//shell_exec ('sudo -u intouch -p intouch25 chmod -R 777'.DEF_READ_PATH);
//shell_exec ('sudo -u intouch -p intouch25 chmod -R 777'.DEF_PROCESS_PATH);
//shell_exec ('sudo -u intouch -p intouch25 chmod -R 777'.DEF_UC_PATH);
//shell_exec ('sudo -u intouch -p intouch25 chmod -R 777'.DEF_JF_PATH);
// Cycle through all source files
foreach ($files as $file) {
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $type = finfo_file($finfo,$source.$file);
   
   if(finfo_file($finfo, $source.$file)=='text/html') {
   
        if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){    
           $str = str_replace(" ", "_", $file); // remove spaces from file name as it halts the conversion
           $str1 = str_replace("(", "_", $str);
           $str2 = str_replace(")", "_", $str1);

           $arr = explode(".",$str2);  

           $file_htm = $arr[0].".htm";  

           if(file_exists($source."".$file)){
               rename($source."".$file, $source."".$file_htm);  
               $file_txt = $arr[0].".txt";
               //print "<br> FILE txt: $file_txt <br>";

                $command = 'html2text -o '.$source.''.$file_txt.' '.'-width 85'.' '.'-ascii'.' '.'-nometa'.' '.$source.''.$file_htm;
                $htmtxt = shell_exec($command); 
                moveFiles($file_htm,$file_txt,$type);
            }
        }
   }
   else if(finfo_file($finfo, $source.$file)=='application/pdf'){ // checking the type of file 
            if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){  
                $str = str_replace(" ", "_", $file); // remove spaces from file name as it halts the conversion
                $arr = explode(".",$str);  
                $file_pdf1 = $arr[0].".pdf";  

                //print "<br> PDF FILE: $file_pdf1 <br>";
                $file_pdf11= str_replace("(","_", $file_pdf1);// replace "(" with "_"
                $file_pdf= str_replace(")","_", $file_pdf11); // replace ")" with "_"  
              //  print "<br> PDF FILE name changed: $file_pdf<br>";
            //    $pdfname=$unrecognizedChain.$file_pdf;
            //      print"<br><br>filepath=$pdfname<br><br>";

                if(file_exists($source."".$file)){
                  rename($source."".$file, $source."".$file_pdf);  
                  $file_text = $arr[0].".txt";
                  //print "<br> FILE TEXT: $file_text ";
                 // $pdftext = shell_exec('/usr/bin/pdftotext -layout '.$source.''.$file_pdf.' '.$destination.''.$file_text);
                   $pdftext = shell_exec('/usr/bin/pdftotext -layout '.$source.''.$file_pdf.' '.$source.''.$file_text);
                   moveFiles($file_pdf,$file_text,$type);
                }

            }
    }
    else {
//        print "<br> SR: ".$source.$file;
//        print "<br> JF: ".$junkFiles.$file;
        if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){   
            if(copy($source.$file,$junkFiles.$file)){
               // print"<br> Junkfile name=$junkFiles.$file<br>";
                insertReceivedPO(-1, $file,$type);
                insertstatus($db,$junkFiles.$file,-1,-1,POStatus::STATUS_JUNK_FILES,$file);
                $delete1[$source.$file]=$source.$file;
            }
        }
    }
//To count total no of PO as 1po = 100lines
//count rows then divide it by 100 to get pocount save it to some table (it_process_status)in database with po no
//    $rows=file($source.$file);
//    $filelen= count($rows);
//            print" file length: $filelen \n\n";
//            if($filelen > 100){
//                $noofpos= ceil($filelen/100);
//                print"\n1:$noofpos \n";
//            }else{
//               $noofpos=1; 
//               print"\n2:$noofpos \n";
//            }   
}
    //Write to empty delete1 array 
    if(! empty($delete1)){
    foreach ($delete1 as $file) {
//        print "<br>UNLInk: $file";
         if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){  
            unlink($file);
         }
      }
    }
 // convert unrecognized chain folder contents if any file not converted then convert it 
    
function moveFiles($file_org,$file_text,$type){
    $source = DEF_READ_PATH;
    $destination = DEF_PROCESS_PATH;
    $unrecognizedChain = DEF_UC_PATH;
    $hd = new headerReaderParser();
    $db = new DBConn();
       //send the txt file to header Parser to fetch chain name
      //$file_path = $destination."".$file_text;
      $file_path = $source."".$file_text;
//      print "<br>FILE PATH: $file_path <br>";
      $parser_identification = $hd->headerParser($file_path);
      print "<br><br> Chain Name: $parser_identification";
  
      //fetch chains identification from db
      //$parser_identification_db = $db->safe(trim($parser_identification));
      //$query = "select * from it_master_dealers where parser_identification = $parser_identification_db";
      if(trim($parser_identification)!=""){
      $query = "select * from it_master_dealers where parser_identification like '%$parser_identification%'";
//      print "<br>$query";
      $pobj = $db->fetchObject($query);
//      print "<br>";
      //print_r($pobj);
      }
      if(isset($pobj)){
            $masterdealerid = $pobj->id;
            insertReceivedPO($masterdealerid, $file_org,$type);
            $ch = str_replace(" ", "_",$pobj->name);
            $dirpath = $destination.$ch."/";          
//            print "<br>DIR PATH: $dirpath ";
            // first chain dir
            if (!file_exists($dirpath)){
                    mkdir($dirpath,  0777 , true);
            }

            //Then new POs folder
            $sub_folder = "newPOs";
            $dirpaths = $destination.$ch."/".$sub_folder."/";          
//            print "<br>DIR PATH SUB FOLDER : $dirpaths ";
            if (!file_exists($dirpaths)) {
                    mkdir($dirpaths,  0777 , true);
            }

            //move the pdf
            if (copy($source.$file_org, $dirpaths.$file_org)) {
                $delete[$source.$file_org] = $source.$file_org;
            }

            //move the txt file
            if (copy($source.$file_text, $dirpaths.$file_text)) {
             $delete[$source.$file_text] = $source.$file_text;
            }
        } else {
            //Means un recognized chain
            print "<br>IN ELSE CASE: <br>";
            // move file to un recognized folder 
            // where chain name not found in our db

            //move the pdf
            insertReceivedPO(-1, $file_org,$type);
            if(! file_exists($source.$file_org)){
                $file_org = $arr[0];
            }
            if (copy($source.$file_org, $unrecognizedChain.$file_org)) {
                  $delete[$source.$file_org] = $source.$file_org;
            }

            if(! file_exists($source.$file_text)){
                $file_text = $arr[0];
            } 
//            print "<br>FILE_TXT: $file_text <br>";
              //move the txt file

            if (copy($source.$file_text, $unrecognizedChain.$file_text)) {
                $delete[$source.$file_text] = $source.$file_text;
              }
          insertstatus($db,$unrecognizedChain.$file_org,-1,-1, POStatus::STATUS_UNRECOGNIZED_CHAIN,$file_org);
          
        }
    // Delete all successfully-copied files
      if(! empty($delete)){
            foreach ($delete as $file_org) {
//                print "<br>UNLInk: $file_org";
                if(trim($file_org)!="" && trim($file_org)!="." && trim($file_org) != ".."){  
                  unlink($file_org);
                }
            }
        }
}


function insertstatus($db,$pdfname,$master_dealer_id,$iniid,$response,$file){ 
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $filename=$file;
//    print"<br>Filename=$filename<br>";
//    $qry="select * from it_master_dealers where id=$master_dealer_id";
//    $mdobj= $db->fetchObject($qry);
    $filenameparts= explode("/",$filename);
    $filenamepartsrev= array_reverse($filenameparts);
    $id_fname=$filenamepartsrev[0];
//    print"<br>id_fname=$id_fname<br>";
    $filename_db=$db->safe($id_fname);
    $updtcurrstatusQ="update it_process_status set is_current_status = 0 where filename=$filename_db and is_current_status = 1";// and status not in(".POStatus::STATUS_DUPLICATE_PO .")";
//    print"<br>process_status_updated=$updtcurrstatusQ<br>";
    $db->execUpdate($updtcurrstatusQ);
    $statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id, ini_id=$iniid, is_current_status = 1, status=$response, createtime= $createtime";
//  $statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
//    print"<br>q=$statusinsertQ<br>";
    $db->execInsert($statusinsertQ);    
}

function insertReceivedPO($masterdealerid, $file,$type){
     print"<br> in insert received POS";
     $db = new DBConn();
     if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){ 
      //checking that file is already available in table   .. niket
//      print"<br>select_insert<br>";
      $receivedPOSelectqry = "select * from it_receivedpos where filename = '$file'and master_dealer_id=$masterdealerid" ;
      $receivedPOSelectobj = $db ->fetchObject($receivedPOSelectqry);
//      print "<br>$receivedPOSelectqry<br>";
      if(!(isset($receivedPOSelectobj))){
        // Inserting every file into it_receivedPOs with its file type. ...niket 
        
        $insertReceivedPOs = "insert into it_receivedPOs set filename= '$file' , type = '$type' , master_dealer_id=$masterdealerid, createtime = now()"; 
//        print "ReceivedPOqry : $insertReceivedPOs ";
        $ins_id = $db ->execInsert($insertReceivedPOs);
        if($ins_id > 0 ){
                print "<br> Record inserted successfully <br>";
        }            
    }
    else{
          print "<br>File already available<br>";
        } 
    }
}
//print "<br> converted";
