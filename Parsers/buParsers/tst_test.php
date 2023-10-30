<?php

require_once "..//home/vlcc/public_html/vlcc_dt/it_config.php";
require_once "cls_7_1_buParser.php";
$parser = new cls_7_1_buParser(null);

$response = $parser->process(ROOTPATH."home/Parsers/movedFiles_2018-06-22/Walmart/unrecognizedBusinessUnit/Walmart_PO-13th_June.txt");
print_r($response);
