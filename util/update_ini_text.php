<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once "../lib/db/DBConn.php";

$db= new DBConn();
$masterid=5;
$cnt=0;
if($masterid==5){
    print"In Reliance-----------\n";
$query= "select id, ini_text from it_inis where  id=668";
echo $query."\n";
// $query= "select id, ini_text from it_inis where id= 39 ";
$iniobj =$db->fetchObject($query);


  # code...
    $iniid= $iniobj->id;
//    print $iniid."<br>";//$iniobj->ini_text;
    $ini= json_decode($iniobj->ini_text);
    
//    $fld = $ini->Items;
//    print_r($fld);
//    print_r($fld->Regex);
    
//    echo $fld->Regex[0]."\n";
//    $fld->Regex[0] = "/(?'Srno'\\d+)\\s+(?'ArticleNo'\\d+)\\s+(?'Itemname'\\w+.*)\\s+(?'EAN'\\d+)\\s+(?'Qty'\\S+)\\s+(?'ign1'\\S+)\\s+(?'VAT'\\S+)\\s+(?'ign2'\\S+)\\s+(?'ign3'\\S+)\\s+(?'Ignore'\\S+)\\s+(?'ign'\\S+)\\s+(?'CAR'\\S+)/";

//echo $fld->Regex[0];
//    $rowno = $fld->RowsPerItem;
//    $cnt=-1;
//    $fld->RowsPerItem=$cnt;
//    print "Row no: ".$rowno;
//    print "Row no: ".$fld->StartRow;
    
    
    // print_r($ini);
    if (!isset($ini->Header->Fields) || !is_array($ini->Header->Fields) || count($ini->Header->Fields) == 0) 
    {print"JSON not read";return;}
   
    $fields = $ini->Header->Fields;
    
//    $fields->Name = "Expiry_Date";
    
    
//    $fields = $ini->Items;
//    print $fields->Regex[2];
  //  $fields->Regex[2] = "/(?'ign'\\S+)\\s+(?'MRP'\\d\\S*)\\s+(?'VAT'\\d\\S*)\\s*(?'Ign'\\%)\\s+(?'ign1'\\S+\\%)\\s+(?'ign2'\\d+)?\\s+(?'ign3'\\d\\S*%)/";
//print $fields->Regex[2];
//    print_r($fields);
//    $fields->RowsPerItem=-1;
//    print_r($fields);
//    $fields->Regex = array();
////    print $fields->Regex[0];
//    $fields->Regex[0] = "/(?'Itemname'.*\\w+)\\s+(?'Qty'\\d+)\\s+(?'MRP'\\d\\S*)\\s+(?'Rate'\\d\\S*)\\s+(?'Amount'\\d\\S*)/";
//    $fields->Regex[1] = "/(?'ArticleNo'\\S+)\\s+(?'EAN'\\d+)\\s+(?'Ignore'\\S+)\\s*(?'ign'\\-)\\s*(?'VAT'\\d\\S*)\\s*(?'ign1'\\%)\\s*(?'ign2'\\-)\\s*(?'ign3'\\d\\S*)/";
////    $fields->Regex[1] = "/(?'ArticleNo'\\d+)\\s+(?'ign'\\d\\S*)\\s*(?'ign1'\\d\\S*)?\\s*(?'ign2'\\d\\S*)?/";
//     $fields->Regex = array();
////    print $fields->Regex[0];
//    $fields->Regex[0] = "/(?'Srno'\\d+)\\s+(?'ArticleNo'\\d+)\\s+(?'Itemname'\\w+.*)\\s+(?'EAN'\\d+)\\s+(?'Ignore'\\S+\\s+\\S+)\\s+(?'VAT'\\S+)\\s+(?'ign1'\\S+)\\s+(?'ign2'\\S+)\\s+(?'ign3'\\S+)?\\s+(?'Qty'\\S+)\\s+(?'CAR'\\S+)/";
//    print_r($fields);
//    $fields->Regex[0] = "/(?'Srno'\\d+)\\s+(?'ArticleNo'\\d+)\\s+(?'EAN'\\d+)\\s+(?'Itemname'.*\\w+)\\s+(?'Qty'\\d\\S*)\\s+(?'CAR'\\w+)\\s+(?'MRP2'\\d\\S+)?\\s+(?'Rate'\\d\\S+)\\s+(?'VAT'\\d\\S*)\\s+(?'ing'\\d\\S*)\\s+(?'Amount'\\d\\S+)/";
//    $fields->Regex[0] = "/(?'ArticleNo'\\S+)\\s+(?'EAN'\\d+)\\s+(?'Ignore'\\S+)\\s*(?'ign'\\-)\\s*(?'VAT'\\d\\S*)\\s*(?'ign1'\\%)\\s*(?'ign2'\\-)\\s*(?'ign3'\\d\\S*)/";
//    $fields->Regex[1] = "/(?'Itemname'\\w.*)?\\s*(?'Ignore'\\*)?\\s+(?'Qty'\\d*\\S+)\\s+(?'MRP'\\d\\S+)\\s+(?'Rate'\\d\\S+)/";
//    $fields->Regex[2] = "/(?'Itemname'\\w+.*)?\\s+(?'ArticleNo'\\d+)\\s+(?'Ignore'\\S+)\\s*(?'ign'\\-)\\s*(?'VAT'\\S+)\\s*(?'ign1'\\%)\\s*(?'ign2'\\-)\\s*(?'ign3'\\d\\S*)/";
//    $fields->Regex[3] = "/(?'Itemname'.*\\w+)/"  ;
//    $fields->Regex[5] = "/(?'Itemname'\\w.*)?\\s+(?'Qty'\\d*\\S+)\\s+(?'MRP'\\d\\S+)\\s+(?'Rate'\\d\\S+)\\s+(?'Amount'\\d\\S*)/"  ;
//    print_r($fields);
//    $fields->RowsPerItem=-3;
//    
//print_r($fields);
    foreach ($fields as $field) {
      if($field->Name == "DealerName"){
//          $field->format = "m/d/Y";
          $field->value = "RATNADEEP";
//          echo "hiii";
//          print_r($field);
  //        $field->row=array(0=> 13);
//          $field->Regex = array();
//          $field->Regex[0] = "/Purchase\\s+Order\\s+Number\\s+(\\S+)/";
         // print_r($field->Regex[0]);
          
        //  $field->Regex[0] = "Vendor\\s+\\:?\\s*(.{50})\\s+";
         //  print_r($field->Regex[0]);
          
//          print_r($field);
//          $field->row=array(0=> 7);
////          $field->Regex[0] = "/VENDOR\\s+NAME\\s*\\:?\\s*(.*)\\s+PO\\s*Date/";
//          $field->jumpUp = 3;
//          $field->startIdentifierRegex="";
////          $field->sameline="";
//          print_r($field);
        //  $field->row = array(0=> 13); 
        //  print_r($field->row);
//           print "fnd-$field->Name<br>";
//                    print_r($field->row);
//                    print"<br>";
                   // $field->row = array(0=> 13);   
                  //  print_r($field->row);
//           print_r($field->row[7]);
          // print"<br>";
//          $field->row =  $field->row + 1;  // for start-length match
//          $field->row =  $field->row + 1;
          //$field->row =  $field->row + 1;  // for start-length match
          // $field->row = array(0 => $field->row[0] -1); // for regex match
          // print_r($field->row);
          // print"<br>";
      }
//       if($field->Name == "VendorName"){
//           $field->value='';
//       }
//      if($field->Name == "Expiry_Date"){
//          print_r($field);
//          $field->row=array(0=> 17);
//          print_r($field);      
//      }
    }
//    print_r($fields);
     $updatediniobj= addslashes(json_encode($ini));
//      print_r($updatediniobj);
// // //     print"<br>----------";
     $updateQ="update it_inis set ini_text= '$updatediniobj' where id= $iniid";
//      print"<br>$updateQ<br>";    
     $no=$db->execUpdate($updateQ);   
//     $cnt= $cnt+$no;

print"<br>no of ini updated= $cnt<br>";
}
else if($masterid == 13){
    print"Nature Basket-------------------\n";
    $query= "select id,ini_text from it_inis where master_dealer_id= $masterid ";//247
    $iniobjsarr =$db->fetchAllObjects($query);
    //print count($iniobjsarr);
    foreach ($iniobjsarr as $iniobj){
        $iniid= $iniobj->id;
       // print $iniid.$iniobj->ini_text;
        $ini= json_decode($iniobj->ini_text);

//        print_r($ini);

        if (!isset($ini->Header->Fields) || !is_array($ini->Header->Fields) || count($ini->Header->Fields) == 0) 
            {print"JSON not read";return;}
        $fields = $ini->Header->Fields;
        foreach ($fields as $field) {
            //print_r($field);
            if(strcasecmp($field->Name,"VendorName")==0){
                print "fnd-$field->Name<br>";
                print "regex:==== "; print_r($field->Regex); print"<br>";
                $field->Regex[0]="/\\S+\\s+(.*)\\s+Order Date.*/"; 
                print_r($field->Regex);
                
                if(is_array($field->row)){
                $namerowno=$field->row[0];
                $idrowno= $namerowno;//"[".$namerowno."]";
                print"namerow=$idrowno<br>"; 
                }
            }else{
                // print "not fnd-$field->Name<br>";
            }
            if(strcasecmp($field->Name,"Vat_Tin")==0){
                print "fnd-$field->Name<br>";
                    print_r($field->row);
                    print"<br>";
                    $field->row = array(0=> 7);   
                    print_r($field->row);
                    $field->start = 0;
                    $field->length = 0; 
                    $field->Regex[0]="/(\\S+)\\s+.*\\s+Order Date.*/"; 
            }else{
                // print "not fnd-$field->Name<br>";
            }
        }   
         if(isset($ini->Footer->Vat_Tin->Regex)){
             $ini->Footer->Vat_Tin->Regex="-";
         }
          print"<br> updated ini-------------<br>";
         //print_r($fields);
         $updatediniobj= addslashes(json_encode($ini));
        // print_r($updatediniobj);
    //     print"<br>----------";
         $updateQ="update it_inis set ini_text= '$updatediniobj' where id= $iniid";
         print"<br>$updateQ<br>";    
         $no=$db->execUpdate($updateQ);   
         $cnt= $cnt+$no;
}
print"<br>no of ini updated= $cnt<br>";
}else if($masterid=2){
    print"Future Retail-------------------<br>";
    $query= "select id,ini_text from it_inis where master_dealer_id = $masterid";
    //$query= "select * from it_inis where id=93";
    // print"$query<br>";
    $iniobjsarr =$db->fetchAllObjects($query);
    print "total ini=".count($iniobjsarr);
    foreach ($iniobjsarr as $iniobj){
      //print_r($iniobj);
       // print"<br>$cnt-----:";
        $iniid= $iniobj->id;
        // print $iniid.$iniobj->ini_text;
        $ini= json_decode($iniobj->ini_text);
        // print_r($ini);
        if (!isset($ini->Header->Fields) || !is_array($ini->Header->Fields) || count($ini->Header->Fields) == 0) 
            {print"JSON not read";continue;}
          $fields = $ini->Header->Fields;
        foreach ($fields as $field) {
          if(strcasecmp($field->Name,"PO_NO")==0){
           //$field->Regex[0] = '["/.*P\\.O\\.\\s*Number\\s*:\\s*(.*)\\-?/"]'; 
            $field->Regex = array(0=> "/.*P\\.O\\.\\s*Number\\s*:\\s*(.*)\\-?/");
          }
        }
        
        $ini->Items->Regex = "";
        // print "<br>inichanges-".$ini->Items->Regex."<br>";
        $ini->Items->Regex = "/(?'EAN'\\d+)\\s+(?'ign'\\S+)\\s+(?'MRP'\\S+)\\s+(?'Qty'\\S+)\\s+(?'CAR'\\w+)\\s+(?'Rate'\\S+)\\s+(?'Ignore'\\S+.*)\\s+(?'VAT'\\S+)\\s+(?'Ignore1'\\S+.*)\\s+(?'Amount'\\S+)/";
              // print "<br>inichanges-".$ini->Items->Regex."<br>";
             // print_r($ini);
        $updatediniobj= addslashes(json_encode($ini));
            // print_r($updatediniobj);
        //     print"<br>----------";
        $updateQ="update it_inis set ini_text= '$updatediniobj' where id= $iniid";
        // print"<br>$updateQ<br>";    
        $no=$db->execUpdate($updateQ);   
        $cnt= $cnt+$no;
       // $cnt++;
    }
     print"<br>no of ini updated= $cnt<br>";
}
