<?php 
require_once "lib/db/DBConn.php";

class cls_5_3_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
        print "<br>CNT: $numlines";
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }
           // print "<br> Line $lineno: $line";
            $regex1 = "(Delivery\s*Address\s*:).*";
            // $regex2 = "(GSTIN).*";
         //   print "<br> Regex1: $regex1 <br>";
            // print "<br> Regex2: $regex2 <br>";
            //Delivery Address
            if(preg_match('/(Delivery\s*Address\s*:).*/',$line,$matches)){
              //  print "<br> IN D IF: <br>";
                $start_line_no = $lineno+1;
              //  print "<br> START LINE NO: $start_line_no <br>";
                break;
            }
            // //Tel:
            // if(preg_match('/(GSTIN).*/',$line,$matches)){
            //     $end_line_no = $lineno-1;
            // }

        }
         for ($lineno = $start_line_no ; $lineno < $numlines; $lineno++) {
            $line =  $lines[$lineno];
            $regex2 = "(GSTIN).*";
          //  print "<br> Regex2: $regex2 <br>";
          //  print "<br> line for regex2:$line<br>";
            if(preg_match('/Tel\s+:/',$line,$matches)){
              echo $line;
               echo $end_line_no = $lineno-1;
                break;
            }
        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> ENDsss LINE NO: $end_line_no <br>";
      // exit;
        $regex="/^(?<addr>.{0,65})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
//            if($cnt==2){
//                continue;
//            }
             $line = trim($lines[$i]);            
         //    print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 print "<br>";
                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
             
             
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
       // return $shipping_address;
        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
        if(isset($sobj)){
            $db->closeConnection();
           // return $shipping_address;
            $inimasterdealerid = $sobj->master_dealer_id;
            print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            print "<br>CALL NEXT 5_4 BU PARSER <br>";
            $clsname = "cls_5_4_buParser";
            if(file_exists("buParsers/".$clsname.".php")){
                   
                    require_once "buParsers/$clsname.php";
                    $parser = new $clsname();
                    $db->closeConnection();
                    $shipping_address = $parser->process($file_text);
                    
                    return $shipping_address;
            }else{
                $db->closeConnection();
                return "NotFound::-1";
            }
            
        }
    }
}
?>