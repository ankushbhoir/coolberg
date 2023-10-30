<?php
class UserType {

    const ITAdmin = 1;
    const VLCCAdmin = 2;

    public static function getAll() {
        return array(
            UserType::ITAdmin => "IT Admin",
            UserType::VLCCAdmin => "Mamaearth Admin"
        );
    }

    public static function getName($usertype) {
        $all = UserType::getAll();
        if (isset($all[$usertype])) {
            return $all[$usertype];
        } else {
            return "Not Found";
        }
    }
}
class IniType {

    const EAN = 1;
    const Article_No = 2;
   

    public static function getAll() {
        return array(
            IniType::EAN => "Based on EAN No",
            IniType::Article_No => "Based on Article No"
        );
    }

    public static function getName($initype) {
        $all = IniType::getAll();
        if (isset($all[$initype])) {
            return $all[$initype];
        } else {
            return "Not Found";
        }
    }

}

class POStatus {

    const STATUS_NOT_PROCESSED = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_MISSING_SITE = 2;
    const STATUS_ISSUE_AT_PROCESSING=3;
    const STATUS_UNRECOGNIZED_BU=4;
    const STATUS_MISSING_VENDOR=5;
    const STATUS_UNRECOGNIZED_CHAIN=6;
    const STATUS_MISSING_EAN=7;
    const STATUS_JUNK_FILES=8;
    const STATUS_NEW_PO=9;
    const STATUS_DUPLICATE_PO = 10 ;
    const STATUS_ARTICLE_NO_MISSING=11;
    const STATUS_MRP_MISMATCH=12;
    const STATUS_WRONG_COST=13;
    const STATUS_SENT_SAP=21;
   

    public static function getAll() {
        return array(
            POStatus::STATUS_NOT_PROCESSED => "Not Processed",
            POStatus::STATUS_PROCESSED => "Successfully Processed",
            POStatus::STATUS_MISSING_SITE => "Missing Site",
            POStatus::STATUS_ISSUE_AT_PROCESSING=>"Issue at Processing",
            POStatus::STATUS_UNRECOGNIZED_BU=>"Unrecognized Business Unit",
            POStatus::STATUS_MISSING_VENDOR=>"Missing Vendor",
            POStatus::STATUS_UNRECOGNIZED_CHAIN=>"Unrecognised Business Chain",
            POStatus::STATUS_MISSING_EAN=>"Missing EAN",
            POStatus::STATUS_JUNK_FILES=>"Junk Files",
            POStatus::STATUS_NEW_PO=>"New PO",
            POStatus::STATUS_DUPLICATE_PO=>"Duplicate PO",
            POStatus::STATUS_ARTICLE_NO_MISSING=>"Missing Article Number",
            pOStatus::STATUS_MRP_MISMATCH=>"Mrp in po is not Matched with Master",
            pOStatus::STATUS_WRONG_COST=>"Base cost is coming wrong with Master",
            pOStatus::STATUS_SENT_SAP=>"Successfully deliver To SAP"             
        );
    }

    public static function getStatusMsg($statusnumber) {
        $all = POStatus::getAll();
        if (isset($all[$statusnumber])) {
            return $all[$statusnumber];
        } else {
            return "Not Found";
        }
    }

    public static function getCombinedStatusMsg($combinedstatusnumber) {
        //$binnumber = decbin($combinedstatusnumber);
        $errorstring = POStatus::bindecValues($combinedstatusnumber);
        $errorarray = explode(",", $errorstring);
        $all = Status::getAll();
        $errormsg = "";
        foreach ($errorarray as $errornum) {
            if (isset($all[$errornum])) {
                $errormsg .= $all[$errornum] . ",";
            }
        }
        $errormsg = substr($errormsg, 0, -1);
        return $errormsg;
    }

    public static function getStatusNumberArray($combinedstatusnumber) {
        $errorstring = POStatus::bindecValues($combinedstatusnumber);
        return $errorstring;
    }

    static function bindecValues($decimal, $reverse = false, $inverse = false) {
        /*
         * ex: bindecValues("1023");
         * returns : 1,2,4,8,16,32,64,128,256,512
          1. This function takes a decimal, converts it to binary and returns the
          decimal values of each individual binary value (a 1) in the binary string.
          You can use larger decimal values if you pass them to the function as a string!
          2. The second optional parameter reverses the output.
          3. The third optional parameter inverses the binary string, eg 101 becomes 010.
          -- darkshad3 at yahoo dot com
         */

        $bin = decbin($decimal);
        if ($inverse) {
            $bin = str_replace("0", "x", $bin);
            $bin = str_replace("1", "0", $bin);
            $bin = str_replace("x", "1", $bin);
        }
        $total = strlen($bin);

        $stock = array();

        for ($i = 0; $i < $total; $i++) {
            if ($bin{$i} != 0) {
                $bin_2 = str_pad($bin{$i}, $total - $i, 0);
                array_push($stock, bindec($bin_2));
            }
        }

        $reverse ? rsort($stock) : sort($stock);
        return implode(",", $stock);
    }

}
class statusFolder {

    const STATUS_NOT_PROCESSED = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_NOT_WEIKFIELD = 2;
    const STATUS_ISSUE_AT_PROCESSING=3;
    const STATUS_UNRECOGNIZED_BU=4;
    const STATUS_GR=5;
    const STATUS_UNRECOGNIZED_CHAIN=6;
    const STATUS_MISSING_EAN=7;
    const STATUS_JUNK_FILES=8;
    const STATUS_NEW_PO=9;
    const STATUS_DUPLICATE_PO = 10 ;
    const STATUS_ARTICLE_NO_MISSING=11;
   

    public static function getAll() {
        return array(
            statusFolder::STATUS_NOT_PROCESSED => "newPOs",//   NAME
            statusFolder::STATUS_PROCESSED => "processed",
            statusFolder::STATUS_NOT_WEIKFIELD => "notvlccPO",
            statusFolder::STATUS_ISSUE_AT_PROCESSING=>"issueAtProcessing",
            statusFolder::STATUS_UNRECOGNIZED_BU=>"unrecognizedBusinessUnit",
            statusFolder::STATUS_GR=>"GR",
            statusFolder::STATUS_UNRECOGNIZED_CHAIN=>"unrecognizedChain",
            statusFolder::STATUS_MISSING_EAN=>"missingEAN",
            statusFolder::STATUS_JUNK_FILES=>"junkFiles",
            statusFolder::STATUS_NEW_PO=>"newPOs",
            statusFolder::STATUS_DUPLICATE_PO=>"duplicatePO",
            statusFolder::STATUS_ARTICLE_NO_MISSING=>"missingArticleNo"
        );
    }

    public static function getStatusMsg($statusnumber) {
        $all = statusFolder::getAll();
        if (isset($all[$statusnumber])) {
            return $all[$statusnumber];
        } else {
            return "Not Found";
        }
    }

    public static function getCombinedStatusMsg($combinedstatusnumber) {
        //$binnumber = decbin($combinedstatusnumber);
        $errorstring = statusFolder::bindecValues($combinedstatusnumber);
        $errorarray = explode(",", $errorstring);
        $all = Status::getAll();
        $errormsg = "";
        foreach ($errorarray as $errornum) {
            if (isset($all[$errornum])) {
                $errormsg .= $all[$errornum] . ",";
            }
        }
        $errormsg = substr($errormsg, 0, -1);
        return $errormsg;
    }

    public static function getStatusNumberArray($combinedstatusnumber) {
        $errorstring = statusFolder::bindecValues($combinedstatusnumber);
        return $errorstring;
    }

    static function bindecValues($decimal, $reverse = false, $inverse = false) {
        /*
         * ex: bindecValues("1023");
         * returns : 1,2,4,8,16,32,64,128,256,512
          1. This function takes a decimal, converts it to binary and returns the
          decimal values of each individual binary value (a 1) in the binary string.
          You can use larger decimal values if you pass them to the function as a string!
          2. The second optional parameter reverses the output.
          3. The third optional parameter inverses the binary string, eg 101 becomes 010.
          -- darkshad3 at yahoo dot com
         */

        $bin = decbin($decimal);
        if ($inverse) {
            $bin = str_replace("0", "x", $bin);
            $bin = str_replace("1", "0", $bin);
            $bin = str_replace("x", "1", $bin);
        }
        $total = strlen($bin);

        $stock = array();

        for ($i = 0; $i < $total; $i++) {
            if ($bin{$i} != 0) {
                $bin_2 = str_pad($bin{$i}, $total - $i, 0);
                array_push($stock, bindec($bin_2));
            }
        }

        $reverse ? rsort($stock) : sort($stock);
        return implode(",", $stock);
    }

}
class IssueReason {

    const MISSING_PONO = 1;
    const MISSING_PODATE = 2;
    const MISSING_DELDATE = 3;
    const MISSING_EXPDATE = 4;
    const MISSING_UNIQUEID = 5;
    const MISSING_PONODADTE = 6;
    const MISSING_AMT = 7;
    const ITMNOTFND = 8;
    const ISWKFQRYFAIL = 9;
    const DBENTRYMISSING = 10;
    const MISSING_EAN = 11;
    const ITEMARREMPTY = 12;
    const INVALID_TAX = 13;
    const INVALID_MRPQTY = 14;
    const INVALID_AMT = 15;
    const INVALID_ITEM = 16;
    
    
    public static function getAll() {
        return array(
            IssueReason::MISSING_PONO => "PO Number Not Found",
            IssueReason::MISSING_PODATE => "PO Date Not Found",
            IssueReason::MISSING_DELDATE => "PO Delivery Date Not Found",
            IssueReason::MISSING_EXPDATE => "PO Expiry Date Not Found",
            IssueReason::MISSING_UNIQUEID => "Distributor Unique Id(VATTIN) Not Found",
            IssueReason::MISSING_PONODADTE => "PO NO and PO Date Not Found (DB Insertion Fail)",
            IssueReason::MISSING_AMT => "Line Amount Not Found",
            IssueReason::ITMNOTFND => "Item Not Found in it_dealer_items table",
            IssueReason::ISWKFQRYFAIL => "Is WKF Count Query Faild",
            IssueReason::DBENTRYMISSING => "Distid, Masterdealerid, distdealerid Missing",
            IssueReason::MISSING_EAN => "EAN not found in dealer_items",
            IssueReason::ITEMARREMPTY => "Item array Empty",
            IssueReason::INVALID_TAX => "Invalid Tax",
            IssueReason::INVALID_MRPQTY => "MRP QTY Out of Range",
            IssueReason::INVALID_AMT => "Invalid Amount",
            IssueReason::INVALID_ITEM => "Invalid Item Info"                      
        );
    }

    public static function getIssueMsg($IssueReason) {
        $all = IssueReason::getAll();
        if (isset($all[$IssueReason])) {
            return $all[$IssueReason];
        } else {
            return "Not Found";
        }
    }
}


class StatisticsReason {

    const TOP3CUSTOMERS = 1;
    const TOP3CATEGORY = 2;
    const TOP3PRODUCTS = 3;
    const TOP3REGIONS = 4;
    const BOTTOM3CUSTOMERS = 5;
    const BOTTOM3CATEGORY = 6;
    const BOTTOM3PRODUCTS = 7;
    const BOTTOM3REGIONS = 8;
    
    
    public static function getAll() {
        return array(
            StatisticsReason::TOP3CUSTOMERS => "TOP 3 CUSTOMERS",
            StatisticsReason::TOP3CATEGORY => "TOP 3 CATEGORY",
            StatisticsReason::TOP3PRODUCTS => "TOP 3 PRODUCTS",
            StatisticsReason::TOP3REGIONS => "TOP 3 REGIONS",
            StatisticsReason::BOTTOM3CUSTOMERS => "BOTTOM 3 CUSTOMERS",
            StatisticsReason::BOTTOM3CATEGORY => "BOTTOM 3 CATEGORY",
            StatisticsReason::BOTTOM3PRODUCTS => "BOTTOM 3 PRODUCTS",
            StatisticsReason::BOTTOM3REGIONS => "BOTTOM 3 REGIONS"
        );
    }

    public static function getIssueMsg($StatisticsReason) {
        $all = StatisticsReason::getAll();
        if (isset($all[$StatisticsReason])) {
            return $all[$StatisticsReason];
        } else {
            return "Not Found";
        }
    }
}
   
 

