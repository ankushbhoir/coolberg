<?php 

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";

if(OPENSSL_VERSION_NUMBER < 0x009080bf) {
    echo "OpenSSL Version Out-of-Date";
} else {
    echo "OpenSSL Version OK";
}

print_r(PDO::getAvailableDrivers());

if (extension_loaded('mbstring')) { 
/* loaded */ 
echo "Loaded mbstring";
}

?>