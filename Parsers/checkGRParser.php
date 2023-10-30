<?php
require_once("../../it_config.php");
require_once("lib/db/DBConn.php");

class checkGRParser{
    public function __construct() {
        
    }
    
    public function grParser($file_text){
        if(file_exists($file_text)){
            $text = file_get_contents($file_text);        
//            $db = new DBConn();
            $lines = explode("\n", $text);
            //$numlines = count($lines);   
            $numlines = 30;      
//            print "<br>CNT: $numlines";
            $found = false;
            for ($lineno = 0; $lineno < $numlines; $lineno++) {
                $line = trim($lines[$lineno]);

                if(trim($line)==""){ continue; }
            //  print "<br> Line: $line";
                $matches = array();
                
                if(preg_match("/(.*GOODS\s+?RECEIPT\s+?NOTE\s+?)|(.*RETURN\s+?DELIVERY\s+?NOTE\s+?).*/",$line, $matches)){
                //print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    } 
                }
                else if(preg_match("/(GR\s+?goods\s+?receipt)/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(Debit\s+Note)/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(Credit\s+Note)/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(Goods\s+Received\s+Report\s+By\s+SKU\s+Number)/",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(PAYMENT\s+ADVICE)/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(GR\s+returns)/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(TAX\s+INVOICE)/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(Goods\s+?Receipt|Return\s+?Slip\s+?No).*/",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(Goods\s+?Receipt\s+?Note).*/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                 else if(preg_match("/(FILL\s+?RATE\s+?REPORT\s+).*/",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                  else if(preg_match("/(SERVICE\s+INVOICE).*/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/(DEBIT\s+NOTE).*/i",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/.*(Balance\s*?Confirmation).*/",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/.*(COMPOSITE\s*?SCHEME\s*?OF\s*?ARRANGEMENT).*/",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/.*(Dear\s*Supplying\s*Partner\s*,).*/",$line,$matches)){
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                } 
                else if(preg_match("/.*(Sub:\s*Confirmation\s*of\s*Balance).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/.*(Online\s*Application\s*For).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                } 
		else if(preg_match("/.*(Material\s*Rejection).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                else if(preg_match("/.*(Certificate\s*of\s*Incorporation).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }else if(preg_match("/.*(Discrepancies\s*at\s*the\s*DC\s*Gate).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }else if(preg_match("/.*(DELIV\s*NOTE).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }else if(preg_match("/.*(General\s*Ledger).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }else if(preg_match("/.*(Purchase\s*Discrepancy).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }else if(preg_match("/.*(DISCREPANCY\s*NOTE).*/",$line,$matches)){
                   // print_r($matches);
                    if( ! empty($matches)){
                        $found = true;
                        break;
                    }
                }
                
            }  
            if(count($matches)>0){
                print_r($matches);
            }           
            return $found;
        }else{
            return false;
        }
    }
}

