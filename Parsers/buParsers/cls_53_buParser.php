<?php
require_once "lib/db/DBConn.php";
class cls_53_buParser{
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
            $regex1 = "(Del.To\s*).*";
            $regex2 = "(Cr.Days).*";
            print "<br> Regex1: $regex1 <br>";
            print "<br> Regex2: $regex2 <br>";
           
            if($start_line_no==0){
            if(preg_match('/(Del.To\s*).*/',$line,$matches)){              
                $start_line_no = $lineno+2;
                print "<br> START LINE NO: $start_line_no <br>";
            }
            }
           
           if(preg_match('/(Cr.Days).*/',$line)==1){
                $end_line_no = $lineno-2;
            }
            if($end_line_no > 0){
              break;  
            }
        }
        $cnt=0;
        print "<br> START LINE NO: $start_line_no <br>";
        print "<br> END LINE NO: $end_line_no <br>";
         $regex="/(?'addr'.{40}$)/";
         for($i=$start_line_no;$i <= $end_line_no;$i++){
            $cnt++ ;
//            if($cnt==2){
//                continue;
//            }
           $line = trim($lines[$i]);
       
             print "<br>LINE $i: $line<br>";
             if(trim($line)==""){ continue; }                        
             $result = array();
             if(preg_match($regex,$line,$result)){
                 print "<br>result=";
                 print_r($result);
                 $shipping_address1 .= " ".trim($result['addr']);
             }
             
             
        }
        if(preg_match('/Ph:.*/',$shipping_address1,$result)){
                 print "<br>result=";
                 print_r($result);
                 $shipping_address1=str_replace($result, "", $shipping_address1);
             
             }
        $shipping_address= $shipping_address1;//str_replace("Address:","", $shipping_address1);
        print "<br><br> SHIPPING ADDRESS: $shipping_address <br>";
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
            $db->closeConnection();
            print "<br>CALL NEXT 53_1 BU PARSER (Write new) <br>";
            // $clsname = "cls_52_1_buParser";
            // if(file_exists(ROOTPATH."home/Parsers/buParsers/".$clsname.".php")){
            //     echo "inside 1";
            //         require_once ROOTPATH."home/Parsers/buParsers/$clsname.php";
            //         $parser = new $clsname();
            //         $response = $parser->process($file_text);
            //         return $response;
            // }else{
                return "NotFound::-1";
            // }      
        }
         
         
         
    }
}