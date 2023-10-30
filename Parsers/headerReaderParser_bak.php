<?php
require_once("../../it_config.php");
require_once("lib/db/DBConn.php");

class headerReaderParser{
    public function __construct() {
        
    }
    
    public function headerParser($file_text){
        if(file_exists($file_text)){
            $text = file_get_contents($file_text);        
            $db = new DBConn();
            $lines = explode("\n", $text);
            $numlines = count($lines);        
            print "<br>CNT: $numlines";
            $chain_name = "NotFound";
            //for ($lineno = 0; $lineno < $numlines; $lineno++) {
            $endloop = 0;
            if($numlines < 20){
               $endloop = $numlines; 
            }else{
               $endloop = 20; 
            }
            for ($lineno = 0; $lineno < $endloop; $lineno++) {
                $line = trim($lines[$lineno]);

                if(trim($line)==""){ continue; }
                print "<br> Line: $line";
                $matches = array();
                preg_match("/(\w+.*)/",$line, $matches);
                print_r($matches);
                if( ! empty($matches)){
                 $chain_name = $matches[0];
                }
                if(trim($chain_name) != ""){
                    // chk if valid chain identification
                    $carr = explode(" ",$chain_name);
                    $query = "select * from it_master_dealers where parser_identification like '%$carr[0]%' ";
                    print "<br>$query";
                    $pobj = $db->fetchObject($query);
                    if(isset($pobj)){
                        //found valid chain
                        break;
                    }else{
                        continue;
                    }

                }
    //            if (preg_match('/(.*)\s+(\d+\s*-...\s*-\d\d\d\d)/', $line, $matches)) {
    //                $invoice_no = $matches[1];
    //                list($dd, $mon, $yy) = explode("-", $matches[2]);
    //                $invoice_date = sprintf("%04d-%s-%02d", $yy, $this->month($mon), trim($dd));
    //                break;
    //            }
            }
            $carr = explode(" ",$chain_name);
            //return $chain_name;
            return $carr[0];
        }else{
            return "NotFound";
        }
    }
}

