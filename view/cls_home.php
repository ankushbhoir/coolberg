<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once 'lib/locations/clsLocation.php';

class cls_home extends cls_renderer {
    function __construct($params = null) {
        $currStore = getCurrStore();
        if (isset($currStore)) {
            if($currStore->usertype == UserType::ITAdmin){
                header("Location: " . DEF_SITEURL . "users");
            }else if ($currStore->usertype == UserType::VLCCAdmin){
               header("Location: " . DEF_SITEURL . "unprocessed/po");
            }else{
                header("Location: " . DEF_SITEURL . "unprocessed/po");
            }
        }
    }
    public function pageContent() {
        $currStore = getCurrStore();
        if (!isset($currStore)) {
            include_once 'inc.storehome.php';
        }
    }
}
?>
