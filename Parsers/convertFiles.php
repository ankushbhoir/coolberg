<?php
require_once("../../it_config.php");
require_once("headerReaderParser.php");
require_once("lib/db/DBConn.php");
require_once "lib/core/Constants.php";

$source1 = DEF_READ_PATH;
$destination = DEF_PROCESS_PATH;
$unrecognizedChain = DEF_UC_PATH;
$junkFiles = DEF_JF_PATH;
convert($source1,$destination,$unrecognizedChain,$junkFiles);
if(file_exists($unrecognizedChain)){
convert($unrecognizedChain,$destination,$unrecognizedChain,$junkFiles);
}

function array_flatten($array) { 
  if (!is_array($array)) { 
    return false; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
}


function convert($source,$destination,$unrecognizedChain,$junkFiles){  
//    print"\n\n**********$source**********\n\n";
    $db = new DBConn();
    $delete = array();
    $arr1= array();
    $arr= array();
    $a1=array();  
        $files = scandir($source);
      //print_r($files);
echo  $query = "select po_filenames,id from it_po_details where ready_process=1 and datetime like '".date('Y-m-d')."%' and status=0"; 

    $result = $db->getConnection()->query($query);        
    while ($pobj = $result->fetch_object()) {
       array_push($a1,$pobj->po_filenames);
       }
     //print_r($a1);
     $result=array_intersect($files,$a1);
//print_r($result);
//exit;
if(sizeof($result)>=1){
  $files=$result;
}
else {
  echo "No file is selected for process";
  //exit;
}
//  exit;
  //$json_arr=json_decode($pobj,true);

   print_r($files);
   //exit; 
        foreach ($files as $file) {
            if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){
                if(strcasecmp($source, $unrecognizedChain)== 0){
    //               print"$unrecognizedChain$file\n\n";
                   $arr1 = explode(".",$unrecognizedChain.$file); 
//                   print_r($arr1);
                   $txtfl= $arr1[0].".txt";
                  // print"\n\ntxtfl:$txtfl";
                   if(file_exists($txtfl)){
                    //   print"\n\n file already converted\n\n\n";
                       continue;
                   }else{
                      // print"\n file not found\n";
                   }
                } 
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo,$source.$file);

            if(finfo_file($finfo, $source.$file)=='text/html') {
                $invarr= array();
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
                        $invarr=getsubinvc($source.$file_txt);
//                        print"invarr--\n";
//                        print_r($invarr);
                        if(!empty($invarr)){
                        //    print"multiplePO<br>";
                            unlink($source.$file_txt);
                            unlink($source.$file_htm);
                            foreach($invarr as $inv){
                               $fnparts = pathinfo($inv);
                               $fname=$fnparts['filename'];
                                $file_txt=$fname.".txt";
                                $file_htm=$fname.".htm";
                                moveFiles($source,$file_htm,$file_txt,$type,$file);
                            }
                        }else{
                        //print"singlePO<br>";
                        moveFiles($source,$file_htm,$file_txt,$type,$file);
                        }
                    }
                }
            }else if(finfo_file($finfo, $source.$file)=='application/pdf'){ // checking the type of file 
              //  echo "Inside pdf";
                if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){  
                   $str = str_replace(" ", "_", $file); // remove spaces from file name as it halts the conversion
                    $str1 = str_replace("&", "and", $str); 
                     $str1 = str_replace("'", "_", $str1); 
                    $arr = explode(".",$str1);
                    $file_pdf1 = $arr[0].".pdf";  

                    //print "<br> PDF FILE: $file_pdf1 <br>";
                    echo $file_pdf11= str_replace("(","_", $file_pdf1);// replace "(" with "_"
                    echo $file_pdf= str_replace(")","_", $file_pdf11); // replace ")" with "_"  
      
       echo  $updenewfilename ="update it_po_details set new_filename='".$file_pdf."' where po_filenames='".$file."'";
                  
                  $db->execInsert($updenewfilename);

                    if(file_exists($source."".$file)){
                        
                        rename($source."".$file, $source."".$file_pdf);  
                        $file_text = $arr[0].".txt";
                        print "<br> FILE TEXT: $file_text ";
                        // $pdftext = shell_exec('/usr/bin/pdftotext -layout '.$source.''.$file_pdf.' '.$destination.''.$file_text);
                        $pdftext = shell_exec('/usr/bin/pdftotext -layout '.$source.''.$file_pdf.' '.$source.''.$file_text);
                        print_r($pdftext);
                        moveFiles($source,$file_pdf,$file_text,$type,$file);
                    }
                }
            }else if(finfo_file($finfo, $source.$file)=='application/vnd.ms-excel' || finfo_file($finfo, $source.$file)=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){ // checking the type of file 
               // print"<br>Excel file found++++++++++++++++++++++<br>";
                if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){  
                    $str = str_replace(" ", "_", $file); // remove spaces from file name as it halts the conversion
                    $arr = explode(".",$str);  
                    $file_pdf1 = $arr[0].".".$arr[1];  

                    //print "<br> PDF FILE: $file_pdf1 <br>";
                    $file_pdf11= str_replace("(","_", $file_pdf1);// replace "(" with "_"
                    $file_pdf= str_replace(")","_", $file_pdf11); // replace ")" with "_"  

                    if(file_exists($source."".$file)){
                        rename($source."".$file, $source."".$file_pdf);  
                        $file_text = $arr[0].".txt";
                      //  print "<br> FILE TEXT: $file_text <br>";
                        $xlstext = shell_exec('ssconvert '.$source.''.$file_pdf.' '.$source.''.$file_text);
                         // print "<br> FILE xlstext: $xlstext <br>";
                        //$pdftext = shell_exec('/usr/bin/pdftotext -layout '.$source.''.$file_pdf.' '.$source.''.$file_text);
                        moveFiles($source,$file_pdf,$file_text,$type,$file);
                    }
                }
            }
            else {
    //            print "<br> SR: ".$source.$file;
    //            print "<br> JF: ".$junkFiles.$file;
                if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){   
                     if (!file_exists($junkFiles)){
                            mkdir($junkFiles,  0777 , true);
                    }
                    if(copy($source.$file,$junkFiles.$file)){
                       // print"<br> Junkfile name=$junkFiles.$file<br>";
                        insertReceivedPO(-1, $file,$type);
                        insertpostatus($db,$junkFiles.$file,-1,-1,POStatus::STATUS_JUNK_FILES,$file);
                        $delete1[$source.$file]=$source.$file;
                    }
                }
            }
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
       $db->closeConnection();
}
 // convert unrecognized chain folder contents if any file not converted then convert it 
    
function moveFiles($readpath,$file_org,$file_text,$type,$file){
//    print"in moved files\n";
    //$source = DEF_READ_PATH;
    $source= $readpath;
    $destination = DEF_PROCESS_PATH;
    $unrecognizedChain = DEF_UC_PATH;
    $hd = new headerReaderParser();
    $db = new DBConn();
       //send the txt file to header Parser to fetch chain name
      //$file_path = $destination."".$file_text;
      $file_path = $source."".$file_text;
//      print "<br>FILE PATH: $file_path <br>";   
      $parser_identification = $hd->headerParser($file_path);
//      print "<br><br> Chain Name: $parser_identification";
  
      //fetch chains identification from db
      //$parser_identification_db = $db->safe(trim($parser_identification));
      //$query = "select * from it_master_dealers where parser_identification = $parser_identification_db";
      if(trim($parser_identification)!=""){
     echo  $query = "select * from it_master_dealers where parser_identification like '%$parser_identification%' ";
     
  //    print "<br>$query";
      $pobj = $db->fetchObject($query);
//      print "<br>";
      //print_r($pobj);
      if($pobj->id==5){
        echo "check for reliance digital";
        
        $text = file_get_contents($file_path);
         $lines = explode("\n", $text);
      echo $numlines = count($lines);   
      echo $lines[27];
     
      preg_match("/(TRENDS|DIGITAL|RDRL|CDIT)/i", $text, $matches);
      // print_r($matches);
      // exit;
      if($matches){
          echo  $query = "select * from it_master_dealers where name like 'Reliance Digital' ";
          $pobj = $db->fetchObject($query);
        }

        preg_match("/SMART\s+BAZAAR/i", $text, $matches11);
        if($matches11){
          echo "Bazaar Smart";
           echo  $query11 = "select * from it_master_dealers where name like 'Reliance Smart Bazaar' ";
          $pobj = $db->fetchObject($query11);
        }
      }
      
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
      $updenewfilename ="update it_po_details set new_filename='".$file_org."',fullpath='".$dirpaths.$file_org."' where po_filenames='".$file."'";
 $db->execInsert($updenewfilename);
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
            if (!file_exists($unrecognizedChain)){
                    mkdir($unrecognizedChain,  0777 , true);
            }
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
          insertpostatus($db,$unrecognizedChain.$file_org,-1,-1, POStatus::STATUS_UNRECOGNIZED_CHAIN,$file_org);
          
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
    $db->closeConnection();    
}


function insertpostatus($db,$pdfname,$master_dealer_id,$iniid,$response,$file){ 
    $filelen=0;
    $pocnt="";
    $createtime1=date('Y-m-d H:i:s');
    $createtime=$db->safe($createtime1);
    $flprts= explode(".",$pdfname);
    $txtflname= $flprts[0].".txt";
    if(file_exists($txtflname)){
        $filelen= count(file($txtflname));
    }
    //print_r(file($txtflname));
   // print" \n file length: $filelen: $txtflname \n\n";         
    if($filelen > 0){
        if($filelen > 100){
            $noofpos= ceil($filelen/100);
     //       print"\n >100 noofpos:$noofpos \n";
        }else {
           $noofpos=1; 
       //    print"\n <100 noofpos:$noofpos \n";
        } 
        $pocnt= ", noofpos= $noofpos";
    } 
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
    $statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id, ini_id=$iniid, is_current_status = 1, status=$response,  createtime= $createtime $pocnt";
//  $statusinsertQ="insert into it_process_status set  pdfname='$pdfname',filename=$filename_db, master_dealer_id=$master_dealer_id,  ini_id=$iniid, status=$response, createtime= $createtime";
   // print"<br>q=$statusinsertQ<br>";
    $db->execInsert($statusinsertQ);    
}

function insertReceivedPO($masterdealerid, $file,$type){
    // print"<br> in insert received POS";
     $db = new DBConn();
     if(trim($file)!="" && trim($file)!="." && trim($file) != ".."){ 
      //checking that file is already available in table   .. niket
//      print"<br>select_insert<br>";
      $receivedPOSelectqry = "select * from it_receivedpos where filename = '$file'and master_dealer_id=$masterdealerid" ;
      $receivedPOSelectobj = $db ->fetchObject($receivedPOSelectqry);
     //print "<br>$receivedPOSelectqry<br>";
      if(!(isset($receivedPOSelectobj))){
        // Inserting every file into it_receivedPOs with its file type. ...niket 
        
        $insertReceivedPOs = "insert into it_receivedpos set filename= '$file' , type = '$type' , master_dealer_id=$masterdealerid, createtime = now()"; 
      //  print "ReceivedPOqry : $insertReceivedPOs ";
        $ins_id = $db ->execInsert($insertReceivedPOs);
        if($ins_id > 0 ){
        //        print "<br> Record inserted successfully <br>";
        }
            
    }
    else{
         // print "<br>File already available<br>";
        } 
    }
}
function getsubinvc($file_text){
   // print"in getsubinvc<br>";
    $cnt=0;
    $text = file_get_contents($file_text);        
    $lines = explode("\n", $text);
    $numlines = count($lines); 
    $posrt=0;
    $matches=array();
    $filenmarr= array();
    $invno="";
    for($i=0;$i<$numlines;$i++){
       // print $lines[$i];      
       if(preg_match("/\d+?-\d+?-\d+\s*?\d+\s*?(\d+\.\d+)/", $lines[$i],$matches)){
//            print"$lines[$i]--matches o/p-$matches[1]<br>";
            if($invno ==""){
                $invno=trim($matches[1]);
//                print"***invno=$invno<br>";
                $cnt++;             
            }else{
                if(trim($invno)!=trim($matches[1])){
//                print"in else invno=$invno & $matches[1]<br> ";               
//                print"newPO<br>";
                $invno=trim($matches[1]);
               // $cnt++;
                for($k=$i;$k>$posrt;$k--){
                    if(preg_match("/(METRO\s*Cash\s*&\s*Carry).*/", $lines[$k])){
//                        print"in metro start<br>";
                        $poend=$k-1;    
//                        print" $posrt::::$poend----$cnt<br>"; 
                        $name= explode(".",$file_text);
                        if($poend){//$posrt && 
//                            print"create file<br>";
                            $filename= $name[0]."_".$cnt.".txt";
                            $filenamehtm= $name[0]."_".$cnt.".htm";
                            // print"$posrt::::$poend<br>";
//                            print $lines[$posrt]."------------linetext<br>";
                            for($j=$posrt;$j<=$poend;$j++){                                  
                                file_put_contents($filename, $lines[$j]."\n", FILE_APPEND);
                                file_put_contents($filenamehtm, $lines[$j]."\n", FILE_APPEND);                             
                                }
                                array_push($filenmarr, $filename);
                        }
                        $cnt++;
                        $posrt=$k;
//                        print"posrt:$posrt";
                        break;
                    }   
                }                
            }
           }
        }
    }$poend =$numlines;
//     print"$posrt::::$poend<br>"; 
        $name= explode(".",$file_text);
            if($cnt > 0 && $posrt && $poend){
                $filename= $name[0]."_".$cnt.".txt";
                $filenamehtm= $name[0]."_".$cnt.".htm";
                for($j=$posrt;$j<$poend;$j++){                 
                    file_put_contents($filename, $lines[$j]."\n", FILE_APPEND);
                    file_put_contents($filenamehtm, $lines[$j]."\n", FILE_APPEND);                                                            
                }
                array_push($filenmarr, $filename); 
            }
            if(isset($filename)){
                array_push($filenmarr, $filename);   
            }
   // print"<br>*********cnt======$cnt<br>";
    return $filenmarr;
}