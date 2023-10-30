<?php
require_once "lib/db/DBConn.php";
//Walmart
class cls_7_1_buParser{
    public function __construct() {
        
    }
    
    public function process($file_text){
//        echo "Inside";
        $text = file_get_contents($file_text);        
        $db = new DBConn();
        $lines = explode("\n", $text);
        $numlines = count($lines);               
        $chain_name = "NotFound";
        $start_line_no = 0;
        $end_line_no = 0;
        for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $line = trim($lines[$lineno]);
            if(preg_match('/Ship\s+To/',$line)==1 || preg_match('/BILL\s+TO\s*:/',$line)==1){
                $start_line_no = $lineno+2;
            }else if(preg_match('/GLN\s+\d+/',$line)==1 || preg_match('/Place\s+of\s+Supply/',$line)==1){
                $end_line_no = $lineno;
                break;
            }
        }
        echo "start line: $start_line_no";
        echo "end line: $end_line_no";
       // $regex="/Purchase\s+Order\s+Number\s+\S+\s+(\w+.*)/";
        $regex="/(?'addr'.{40}$)/";
        $shipping_address1="";
       for ($lineno = $start_line_no; $lineno < $end_line_no; $lineno++) {
           $line = $lines[$lineno];
//           echo "Line=>".$line."<br>";
           if(preg_match($regex,$line,$result)){// || preg_match('/Purchase\s+Order\s+Date\s+\d+\/\d+\/\d+\s+(\w+.*)/',$line,$result) ){                
               //  print_r($result);
                 $shipping_address1 .= " ".$result['addr'];
             }
           
       }
       echo $shipping_address1."\n";
        $shipping_address= $shipping_address1;//str_replace("Address:","", $shipping_address1);
       // print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
        $no_spaces = str_replace(" ", "", $shipping_address);
        $no_spaces_db = $db->safe(trim($no_spaces));
       // print "<br> NO SPACE: $no_spaces <br>";
        $check = " replace(shipping_address ,' ','') = $no_spaces_db ";
     //   print "<br> CHECK : $check <br>";
        $query = " select * from it_shipping_address where $check "; 
       // print "<br> QUERY: $query <br>";
      
         $sobj = $db->fetchObject($query);
         $inimasterdealerid = "";
         
         if(isset($sobj)){
          $db->closeConnection();
          $inimasterdealerid = $sobj->master_dealer_id;
        //  print "<br>MASTER Dealer ID : $inimasterdealerid </br>";
            return $shipping_address."::".$inimasterdealerid;
        }else{
          $db->closeConnection();
            return "NotFound::-1";              
        }
    }
}

