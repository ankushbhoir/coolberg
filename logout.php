<?php

require_once("../it_config.php");
require_once("session_check.php");
//print_r($_SESSION['saleshead']);

if (isset($_SESSION['hoCodeInfo'])) {
  $user = $_SESSION['currStore'] = $_SESSION['hoCodeInfo'];
  $_SESSION['form_storecode'] = $user->username;
  unset($_SESSION['hoCodeInfo']);
  header("Location: " . DEF_SITEURL);
  exit;
}
if (isset($_SESSION['saleshead'])) {
    unset($_SESSION['currStore']);
    $_SESSION['currStore'] = $_SESSION['saleshead'];
    header("Location:" . DEF_SITEURL . "place/order");    
    unset ($_SESSION['saleshead']);   
    exit();
} else {
    session_destroy();
    header("Location: " . DEF_SITEURL);
    exit();
}
//header("Location: " . DEF_SITEURL);
exit();
?>
