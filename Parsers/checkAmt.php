<?php
require_once("../../it_config.php");
require_once "SaveToDB.php";
require_once "lib/db/DBConn.php";

class checkAmt{
    
    public function _construct(){
        
    }    
    public function chkValue($item_parts){
        print"<br>check item values<br>";
        $Qty=doubleval(trim(getFieldValue($item_parts,"Qty")));
        $Rate = str_replace(",","",trim(getFieldValue($item_parts,"Rate")));
        $Amount = str_replace(",","",trim(getFieldValue($item_parts,"Amount")));
        if(($Qty != "" && $Qty !=0) && ($Rate != "" && $Rate !=0) && ($Amount != "" && $Amount !=0) ){
            $Amount= str_replace(",", "", $Amount);
            $Qty= str_replace(",", "", $Qty);
            $Rate= str_replace(",", "", $Rate);

            $cal_amt= $Qty*$Rate;

            print"<br>calculations in chk amt class<br>Qty:".$Qty."<br>Rate:".$Rate."<br>calamt:".$cal_amt."<br>poAmount:".$Amount."<br>";

            if($Amount >= $cal_amt){
               print"<br>valid amount<br>";
               return 1;
            }
            else{
                $diff_amt = $cal_amt - $Amount;
                $diff_per = ($diff_amt / $cal_amt) * 100 ;
                print"<br>diff_per:".$diff_per."<br>diff_amt:".$diff_amt."<br>";
                if($diff_per <= DEF_TAX){
                    return 1;
                  
                }
                else{
                print"<br>invalid Amount continue with next regex<br>";
                return 0;               
                }
            }
        }
        return 0;        
    }
} 