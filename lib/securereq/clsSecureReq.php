<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/sms/clsSMSHelper.php";
require_once "lib/codes/clsVouchers.php";
require_once "lib/messages/clsFanMessage.php";

class clsSecureReq extends dbobject
{

    public function isMethodAllowed($method, $allowedMethods)
    {
        if (!isset($allowedMethods)) {
            return 401;
        } else if (!in_array($method, $allowedMethods)) {
            return 400;
        } else {
            return 200;
        }

    }

}
