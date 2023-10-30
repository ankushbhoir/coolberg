<?php
require_once "lib/db/DBConn.php";

class cls_2_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
//        print "<br>CNT: $numlines";
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        $regex4 = "/(Delivery\s*\&\s*:).*/";
        $regex1 = "/(Delivered\s*To\s*:).*/";
        $regex2 = "/(Item).*/";
        $regex3 = "/(Article\s*EAN).*/";
        ///////////
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);            
            if(trim($line)==""){ continue; }
//            print "<br> Line $lineno: $line";
            if(preg_match('/(Delivered\s*To\s*:).*/',$line) || preg_match('/(Delivery\s*\&\s*:).*/',$line)  ){
                 $fndonlineno = $lineno;
                    print"<br>regex del_to found on line= $fndonlineno <br>";
                 break;
            }
        }
         for ($lineno = $fndonlineno; $lineno < $numlines; $lineno++) {
//             print"<br>in loop $lineno<br>";
             $line = trim($lines[$lineno]);     
             if(preg_match('/(Delivered\s*By\s*:).*/',$line)){
                print"<br>regex del_by found after del_to <br>";
                $regex1 = "/(Delivered\s*By\s*:).*/";
                break;
            }
         }
        //////////////
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            
            if(trim($line)==""){ continue; }
            print "<br> Line $lineno: $line";
//            $regex1 = "(Delivered\s+?To\s+?:).*";
//            $regex2 = "(Item).*";
            print "<br> Regex1: $regex1 <br>";
            print "<br> Regex2: $regex2 <br>";
            
            if(preg_match($regex1,$line,$matches) || preg_match($regex4,$line,$matches)){
                print "<br> IN D IF: <br>";
                $start_line_no = $lineno+1;
                print "<br> START LINE NO: $start_line_no <br>";
            } 
            
            if(preg_match($regex2,$line,$matches)){
                $end_line_no = $lineno-2;
            }else if(preg_match($regex3,$line,$matches)){
                $end_line_no = $lineno-2;
                
            }
            if($end_line_no > 0){
              break;   
            }
        }
//        for ($lineno = 0; $lineno < $numlines; $lineno++) {
//            $line = trim($lines[$lineno]);
//            
//            if(trim($line)==""){ continue; }
//            print "<br> Line $lineno: $line";
//            $regex1 = "(Delivered\s+?To\s+?:).*";
//            $regex2 = "(Item).*";
//            print "<br> Regex1: $regex1 <br>";
//            print "<br> Regex2: $regex2 <br>";
//            
//            if(preg_match('/(Delivered\s+?To\s+?:).*/',$line,$matches)){
//                print "<br> IN D IF: <br>";
//                $start_line_no = $lineno+1;
//                print "<br> START LINE NO: $start_line_no <br>";
//            }
//            
//            if(preg_match('/(Item).*/',$line,$matches)){
//                $end_line_no = $lineno-1;
//            }
//            if($end_line_no > 0){
//              break;   
//            }
//        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/^(?<addr>.{0,74})/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
//            if($cnt==2){
//                continue;
//            }
             $line = trim($lines[$i]);            
             print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 print "<br>";
                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
             
             
        }
            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        //return $shipping_address;
        $shipping_address = str_replace("Billing","",$shipping_address); 
        $shipping_address = str_replace("Address","",$shipping_address); 
        $no_spaces = str_replace(" ","", $shipping_address);
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
             $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
          print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            print "<br>CALL NEXT 2_1 BU PARSER <br>";
            $clsname = "cls_2_1_buParser";
            if(file_exists("buParsers/".$clsname.".php")){
                    require_once "buParsers/$clsname.php";
                   $db->closeConnection();
                    $parser = new $clsname();
                    $response = $parser->process($file_text);
                    return $response;
            }else{
              $db->closeConnection();
                return "NotFound::-1";
            }
            
        }
    }
}

