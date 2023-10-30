<?php
require_once "lib/db/DBConn.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once "lib/core/Constants.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "chkDuplicate.php";

date_default_timezone_set('Asia/Kolkata');