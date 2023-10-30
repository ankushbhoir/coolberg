<?php
require_once("../../it_config.php");
//require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");

$folder = DEF_PROCESS_PATH;

$dirs = scandir($folder);

//print_r($dirs);

foreach($dirs as $dir){
    $ubu_folder = $folder.$dir."/unrecognizedBusinessUnit/";
    if(is_dir($ubu_folder)){    
       $cnt = count(scandir($ubu_folder));
       
       if($cnt > 2){
           moveFileBack($folder,$dir);
       }
    }
}


function moveFileBack($folder,$dir){
    $ubu_folder = $folder.$dir."/unrecognizedBusinessUnit/";
    $newpo = $folder.$dir."/newPOs/";
 $newpo = str_replace("&","\&",$newpo);
    $ubu_folder = str_replace("&","\&",$ubu_folder);    
    $move = "mv ". $ubu_folder."*  ".$newpo;
   // foreach($files as $file){
   //shell_exec("chmod -R 777 ".$folder.$dir);
        shell_exec($move);
       // echo $move."<br>";
    //} 
}
