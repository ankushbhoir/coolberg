<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
//require_once("session_check.php");
require_once "../lib/db/DBConn.php";
require_once "util/itemsINI.php";
require_once "util/initester.php";

try{
    $db = new DBConn();
    $issue_po = array();
    $faulty_items = array();
    $query = "select po_id as id from it_po_items where dealer_item_id not in (select id from it_dealer_items) "; // and po_id not in (910,1260,868,877,859,595,594) group by po_id "; //limit 1";
    //$query = "select po_id as id from it_po_items where dealer_item_id not in (select id from it_dealer_items) and po_id in (910,868,859,595,594) group by po_id "; //1260, ,877limit 1";
    //$query = "select po_id as id from it_po_items where dealer_item_id not in (select id from it_dealer_items) and po_id in (1260,877) group by po_id "; //1260, ,877limit 1";
    //$result = $db->execQuery($query);
    $result = $db->fetchAllObjects($query);
    //while($obj = $result->fetch_obj()){
    foreach($result as $obj){
        print_r($obj);
        $qry = "select * from it_po where id  = $obj->id ";
        $pobj = $db->fetchObject($qry);
        if(isset($pobj)){
            //call buParser
            $clsname = "cls_".$pobj->master_dealer_id."_buParser";
                print "<br> CLSNAME: $clsname <br>";
                if(file_exists("../Parsers/buParsers/".$clsname.".php")){
                    require_once "../Parsers/buParsers/$clsname.php";
                    $parser = new $clsname();
                    $fpath =  "/var/www/weikfield_DT/home/util/".trim($pobj->invoice_no).".txt";
                    if(! file_exists($fpath)){
                     $fpath = createTxtFile($pobj);
                    }
                    $buParserResponse = $parser->process($fpath);
                    $responseArray = explode("::",$buParserResponse);
                    $shipping_address = $responseArray[0];
                    $iniMasterDealerId= $responseArray[1];
                    //fetch the ini
                    $no_spaces = str_replace(" ", "", $shipping_address);
                    $no_spaces_db = $db->safe(trim($no_spaces));
                    print "<br> NO SPACE: $no_spaces <br>";
                    $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
                    print "<br> CHECK : $check <br>";
                    $query = " select * from it_shipping_address where $check and master_dealer_id = $iniMasterDealerId "; 
                    print "<br> QUERY: $query <br>";
                    $sobj = $db->fetchObject($query);
                    if(isset($sobj)){                                                
                        //fetch ini
                        $iniid=$sobj->ini_id;
                        print"<br>INIID=$iniid<br>";
                        $iniids=explode(",",$iniid);
                        print"<br>INIID ARRAY=";
                        print_r($iniids);
                        if(count($iniids)>1){
                             //$iniids=$explode(",",$iniid);
                                $iobj=fetchCorrectini($fpath,$iniids,$shipping_address);   // call fetchCorrectINI
                               // return $sobj;
                                if(isset($iobj)){
                                $inicall = new itemsINI();
                                $items = $inicall->iprocess($fpath, $iobj->ini_text);
                                print "<br>ITEMS: <br/>";
                                print_r($items);
                                fetchfaultyItem($items,$pobj);
                            }else{
                                $issue_po['missing_ini'] = $obj->id;
                            }
                        }else{                        
                            $q = "select * from it_inis where id = $sobj->ini_id ";
                            $iniobj = $db->fetchObject($q);
                            if(isset($iniobj)){
                                $inicall = new itemsINI();
                                $items = $inicall->iprocess($fpath, $iniobj->ini_text);
                                print "<br>ITEMS: <br/>";
                                print_r($items);
                                fetchfaultyItem($items,$pobj);
                            }else{
                                $issue_po['missing_ini'] = $obj->id;
                            }
                        }
                    }else{
                        $issue_po['missing_shipping_address'] = $obj->id;
                    }
                    
                }
        }else{
            $issue_po['missing_po'] = $obj->id;
        }
    }
    if(!empty($faulty_items)){
        createFFile($faulty_items);
    }
}catch(Exception $xcp){
 print $xcp->getMessage();   
}

function createTxtFile($pobj){
    print "<br>In create file fn <br>";
    $db = new DBConn();
    //$fname = $pobj->invoice_no.".txt";
    $fname = $pobj->id.".txt";
    $myfile = fopen("$fname", "w") or die("Unable to open file!");
   // $txt = "John Doe\n";
    //fwrite($myfile, $txt);
    //$txt = "Jane Doe\n";
    $txt = $pobj->invoice_text;
    fwrite($myfile, $txt);
    fclose($myfile);
    $filepath = "/var/www/weikfield_DT/home/util/".$fname;
    $db->closeConnection();
    return $filepath;
}

function createFFile($fitems){
    print "<br>";
    print_r($fitems);
    print "<br>In create file fn <br>";
    $db = new DBConn();
    $fname = "Faulty_items.txt";
    $myfile = fopen("$fname", "w") or die("Unable to open file!");
   // $txt = "John Doe\n";
    //fwrite($myfile, $txt);
    //$txt = "Jane Doe\n";
    //$txt = implode("\n", $fitems);
    $txt = "";
    foreach($fitems as $ft){
        $str = serialize($ft);
     $txt .= "".$str;
    
    }
    fwrite($myfile, $txt);
    fclose($myfile);
    $filepath = "/var/www/weikfield_DT/home/util/".$fname;
    $db->closeConnection();
    return $filepath;
}

 function fetchCorrectini($fpath,$iniids,$shipping_address){
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

function fetchfaultyItem($items,$pobj){
    global $faulty_items;
    $db = new DBConn();
    $query = "select * from it_po_items where po_id = $pobj->id and dealer_item_id not in ( select id from it_dealer_items where master_dealer_id = $pobj->master_dealer_id )";
    $objs = $db->fetchAllObjects($query);
    foreach($objs as $obj){
        print "<br>Faulty item: <br>";
        $faulty_items[] = $obj;
        print_r($obj);
        $item = fetchItemDetails($obj,$items,$pobj);
    }
    $db->closeConnection();
}

function fetchItemDetails($fautlytem_obj,$items,$pobj){
    $db = new DBConn();
    //fetch master item details
    $query = "select * from it_master_items where id = $fautlytem_obj->master_item_id ";
    $mobj = $db->fetchObject($query);
    $item_to_correct = array();
    if(isset($mobj)){
        foreach($items as $item){
            print "<br>ITM: ";
            print_r($item);
            print "<br>ITM EAN: ".$item['EAN'];
            print "<br>";
            if(strcmp($item['EAN'], $mobj->itemcode)==0){
                print "<br>FAULTY ITM: ";
                print_r($item);
                $item_to_correct = $item;
                print "<br>Found Correct Details: <br/>";
                print_r($item_to_correct);
                updateToDB($fautlytem_obj, $item_to_correct,$pobj);
            }
        }
    }
    $db->closeConnection();
}


function updateToDB($fautlytem_obj, $item_to_correct,$poobj){
  $db = new DBConn();
  $query = "select * from it_dealer_items where master_dealer_id = $poobj->master_dealer_id and master_item_id = $fautlytem_obj->master_item_id ";
  print "<br>SEL: $query<br>";
  $dobj = $db->fetchObject($query);
  if(isset($dobj)){
      //update
      print "<br>FITEM: <br/>";
      print_r($item_to_correct);
      print "<br> ARCODE: ".$item_to_correct['ArticleNo'];
      $mqry = "select * from it_master_items where id = $fautlytem_obj->master_item_id ";
      $mobj = $db->fetchObject($mqry);
      if(isset($mobj)){ // master item exists
        $article_no_db = $db->safe(trim($item_to_correct['ArticleNo']));
        $itemname_db = $db->safe(trim($mobj->itemname));
        $qry = "update it_dealer_items set master_dealer_id = $poobj->master_dealer_id , itemcode = $article_no_db  , itemname = $itemname_db , master_item_id = $fautlytem_obj->master_item_id , is_weikfield = 1 , updatetime = now() where id = $dobj->id ";
        print "<br>INS QRY: $qry</br>";
        $db->execUpdate($qry);
        //chk again
        $cqry = "select * from it_dealer_items where master_dealer_id = $poobj->master_dealer_id and id = $dobj->id ";        
        $chk = $db->fetchObject($cqry);
        if(isset($chk)){
            $uqry = "update it_po_items set dealer_item_id = $dobj->id where id = $fautlytem_obj->id ";
            print "<br>UPDATE QRY: $uqry<br/>";
            $db->execUpdate($uqry);
        }
      }
  }else{
      //insert
      print "<br>FITEM: <br/>";
      print_r($item_to_correct);
      print "<br> ARCODE: ".$item_to_correct['ArticleNo'];
      $mqry = "select * from it_master_items where id = $fautlytem_obj->master_item_id ";
      $mobj = $db->fetchObject($mqry);
      if(isset($mobj)){ // master item exists
        $article_no_db = $db->safe(trim($item_to_correct['ArticleNo']));
        $itemname_db = $db->safe(trim($mobj->itemname));
        $qry = "insert into it_dealer_items set master_dealer_id = $poobj->master_dealer_id , itemcode = $article_no_db  , itemname = $itemname_db , master_item_id = $fautlytem_obj->master_item_id , is_weikfield = 1 , createtime = now() ";
        print "<br>INS QRY: $qry</br>";
        $dealer_item_id = $db->execInsert($qry);
        //chk again
        $cqry = "select * from it_dealer_items where master_dealer_id = $poobj->master_dealer_id and id = $dealer_item_id ";        
        $chk = $db->fetchObject($cqry);
        if(isset($chk)){
            $uqry = "update it_po_items set dealer_item_id = $dealer_item_id where id = $fautlytem_obj->id ";
            print "<br>UPDATE QRY: $uqry<br/>";
            $db->execUpdate($uqry);
        }
      }
  }
  $db->closeConnection();
}

print "<br>ISSUES <br/>";
print_r($issue_po);