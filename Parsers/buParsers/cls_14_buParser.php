<?php
require_once "lib/db/DBConn.php";
//Metro Cash & carry
class cls_14_buParser{
    public function __construct() {
        
    }
    //****public function process($file_text,master_dealer_id){
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
            $regex1 = "(Purchase\s*?Order).*";
            $regex2 = "(FAX\s*?:*?).*";
          //  $regex="/ORDER\s*?STORE\s*?(\d+)/";
           // print "<br> Regex1: $regex1 <br>";
           // print "<br> Regex2: $regex2 <br>";
            
            if(preg_match('/(Purchase\s*?Order).*/',$line,$matches)){
                print "<br> IN D IF: <br>";
                $start_line_no = $lineno+2;
                //print "<br> START LINE NO: $start_line_no <br>";
            }
            
            if(preg_match('/(FAX\s*?:*?).*/',$line,$matches)){
                $end_line_no = $lineno-2;
            }
            if($end_line_no > 0){
              break;   
            }
//            if(preg_match($regex,$line,$matches)){
//                print "<br> IN D IF: <br>";
//                print_r($matches);
//                $shipping_address = $matches[1];
//            }
        }
      
        $cnt=0;
     //   print "<br> aniket START LINE NO: $start_line_no <br>";
      //  print "<br> END LINE NO: $end_line_no <br>";
       
        $regex="/^(?<addr>.{0,44})/";
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
               
              // exit();
               continue;
            }
           print "<br><br> LINE $i: $lines[$i] ";
           $line = trim($lines[$i]); 
           if(preg_match('/(Delivery\s*?Location\s*?:).*/',$line,$matches)){
                 $line = trim(str_replace("Delivery Location:","",$line)); 
           }
              
             //print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
//             print "<br>LINE: $i:: $line<br>";
//             print "<br>REGEX: $regex <br>";
             if(preg_match($regex,$line,$result)){
                 print "<br>IN MATCHES: ";
                 print_r($result);
                 $shipping_address .= " ".$result['addr'];
             }
            
             print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
             
             
        }
            
     //   print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        //return $shipping_address;
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
     //   print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK abc123 : $check <br>";
        //echo $query = " select * from it_shipping_address where $check and master_dealer_id = $master_dealer_id ";
         
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY final check: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         print_r($sobj);
        // exit;
         if(isset($sobj)){
            $db->closeConnection();
            $inimasterdealerid = $sobj->master_dealer_id;
            print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            $db->closeConnection();
            print "<br>CALL NEXT 14_1 BU PARSER <br>";
            $clsname = "cls_14_1_buParser";
            if(file_exists("buParsers/".$clsname.".php")){
                    require_once "buParsers/$clsname.php";
                    $parser = new $clsname();
                    $response = $parser->process($file_text);
                    return $response;
                   // print_r($response);
            }else{
                return "NotFound::-1";
            }
            
        }
    }
}





