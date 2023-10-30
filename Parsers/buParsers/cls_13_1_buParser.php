<?php
require_once "lib/db/DBConn.php";
//Nature Basket
class cls_13_1_buParser{
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
            //print "<br> Line $lineno: $line";
            $regex1 = "(Delivery\s*?Location\s*?:).*";
            $regex2 = "(Natures\s*?Basket\s*?Limited).*";
//            print "<br> Regex1: $regex1 <br>";
//            print "<br> Regex2: $regex2 <br>";
            
            if(preg_match('/(Delivery\s*?Location\s*?:).*/',$line,$matches)){
                print "<br> IN D IF: <br>";
                $start_line_no = $lineno;
                print "<br> START LINE NO: $start_line_no <br>";
            }
            
            if(preg_match('/(Natures\s*?Basket\s*?Limited).*/',$line,$matches)){
                $end_line_no = $lineno-1;
            }
            if($end_line_no > 0){
              break;   
            }
        }
      
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/^(?<addr>.{0,69})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
//            if($cnt==2){
//                continue;
//            }
           $line = $lines[$i]; 
           //print "<br><br> LINE $i: $line ";
           $str=  substr($line, 0, 40);
           print "<br>LINE PART: $str <br>";
           //print"<br>str= $str =is blank<br>";
           if (ctype_space($str)) {
               print"<br>IN str blank If<br>";
               continue;
            }
           print "<br><br> LINE $i: $lines[$i] ";
           $line = $lines[$i]; 
           if(preg_match('/(Delivery\s*?Location\s*?:).*/',$line,$matches)){
               print"<br>in replace $line";
                 $line = str_replace("Delivery Location :","",$line); 
                 print"<br>after replace $line";
           }
               if(preg_match('/(Airport\s*?Tax).*/',$line,$matches)){
                 $line = str_replace("Airport Tax","",$line); 
           }
             print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             print "<br>LINE: $i:: $line<br>";
             print "<br>REGEX: $regex <br>";
             if(preg_match($regex,$line,$result)){
                 print "<br>IN MATCHES: ";
                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
            
             print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
             
             
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        //return $shipping_address;
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> shp QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
//        print"<br>shiObj:";
//        print_r($sobj);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
          $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
          print "<br>in obj set MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
          $db->closeConnection();
//            print "<br>CALL NEXT 13_1 BU PARSER <br>";
//            $clsname = "cls_13_1_buParser";
//            if(file_exists("buParsers/".$clsname.".php")){
//                   
//                    require_once "buParsers/$clsname.php";
//                    $parser = new $clsname();
//                    $response = $parser->process($file_text);
//                    return $response;
//            }else{
                return "NotFound::-1";
//            }
            
        }
    }
}



