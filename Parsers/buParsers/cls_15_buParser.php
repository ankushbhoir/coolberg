<?php
require_once "lib/db/DBConn.php";
//Trent
class cls_15_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
        echo "Inside Trent ";
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);        
  //      print "<br>CNT: $numlines";
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = $lines[$lineno];
            
            if(trim($line)==""){ continue; }
            if(preg_match('/(Shipping\s+Address).*/',$line,$matches)||preg_match('/(Consignee\s*Detail).*/',$line,$matches)){
                $start_line_no = $lineno + 1;
            }
            
            if(preg_match('/(Article).*/',$line,$matches)){
                $end_line_no = $lineno - 2;
            }
            if($end_line_no > 0){
              break;   
            }
        }
        $cnt=0;

         $regex="/(.*)\s+State\s+Name\s+\&\s+/";
         $regex1 = "/(.*)\s+PAN\s+No/";
         $regex2 = "/(.*)\s+GSTIN\s+\/\s+UIN/";
        $shipping_address = "";
        for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++;
           $line = $lines[$i]; 
           if(preg_match('/(Shipping\s+Address).*/',$line,$matches)){
                 $line = trim(str_replace("Shipping Address ","",$line)); 
           }
                        
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 $shipping_address .= " ".$result[1];
             }else if(preg_match($regex1,$line,$result)){
                 $shipping_address .= " ".$result[1];
             }else if(preg_match($regex2,$line,$result)){
                 $shipping_address .= " ".$result[1];
             }else{
                 $shipping_address .= " ".trim($line);
             }                          
        }
            
        print "<br><br> SHIPPING ADDRESS****: $shipping_address <br>";        
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));       
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";       
        $query = " select * from it_shipping_address where $check "; 
        //print "<br> QUERY: $query <br>";
      
        $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
            $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
          //print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
            $db->closeConnection();
            print "<br>CALL NEXT 15_1 BU PARSER <br>";
            $clsname = "cls_15_1_buParser";
            if(file_exists("buParsers/".$clsname.".php")){
                    require_once "buParsers/$clsname.php";
                    $parser = new $clsname();
                    $response = $parser->process($file_text);
                    return $response;
            }else{
                return "NotFound::-1";
            }
            
        }
    }
}

