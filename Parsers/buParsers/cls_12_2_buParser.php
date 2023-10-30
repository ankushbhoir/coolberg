<?php


class cls_12_2_buParser{
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
            print "<br> Line $lineno: $line";
            $regex1 = "(Delivered\s+?To\s+?:).*";
            $regex2 = "(No).*";
            print "<br> Regex1: $regex1 <br>";
            print "<br> Regex2: $regex2 <br>";
            
            if(preg_match('/(Delivered\s+?To\s+?:).*/',$line,$matches)){
                print "<br> IN D IF: <br>";
                $start_line_no = $lineno;
                print "<br> START LINE NO: $start_line_no <br>";
            }
            
            if(preg_match('/(No).*/',$line,$matches)){
                $end_line_no = $lineno;
            }
            if($end_line_no > 0){
              break;   
            }
        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
        $regex="/^(?<addr>.{0,48})/";
        $shipping_address = "";
        for($i=$start_line_no;$i < $end_line_no;$i++){
            $cnt++;
//            if($cnt==2){
//                continue;
//            }
             $line = trim($lines[$i]);     
             
             if(preg_match('/(Delivered\s+?To\s+?:).*/',$line,$matches)){
                  $line = trim(str_replace("Delivered To :","",$line)); 
            }
             print "<br><br> LINE $i: $line ";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                print "<br>";
                print_r($result);
                if(preg_match("/VAT/",$result[0])!=1){
                   $shipping_address .= " ".$result['addr'];
                }
             }
        }            
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
       // return $shipping_address;
         print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        //return $shipping_address;
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
        print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
        print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
        print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         $db->closeConnection();
         if(isset($sobj)){
          $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;          
          print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
          return $shipping_address."::".$inimasterdealerid;
        }else{
          $db->closeConnection();
          return "NotFound::-1";
        }
    }
}

