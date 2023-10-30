<?php 
// $host='arddms.intouchrewards.com';
// $ftp = ftp_connect($host,$port,$timeout);
// $user='arddms';
// $pass='arddms';
// ftp_login($ftp,$user,$pass);
// $dest_file="/sappodata/tobeprocessed/";
// $source_file="/home/ttk/public_html/Data/DailyParsedPOXLS/LFS_PO_20200831_034813.xml";
// $ret = ftp_nb_put($ftp, $dest_file, $source_file, FTP_BINARY, FTP_AUTORESUME);

// while (FTP_MOREDATA == $ret)
//     {
//         // display progress bar, or someting
//         $ret = ftp_nb_continue($ftp);
//     }


    $server="arddms.intouchrewards.com";
 $serverUser="arddms";
 $serverPassword="arddms";
 $file = "LFS_PO_20200831_034813.xml";//tobe uploaded
 $remote_file = "/sappodata/tobeprocessed/";

 // set up basic connection
//  echo $conn_id = ftp_connect($ftp_server);
// exit;
//  // login with username and password
//  $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

//  // upload a file
//  if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
//     echo "successfully uploaded $file\n";
//     exit;
//  } else {
//     echo "There was a problem while uploading $file\n";
//     exit;
   // }
 // close the connection
 //ftp_close($conn_id);

$localFile='/home/ttk/public_html/Data/DailyParsedPOXLS/LFS_PO_20200831_034813.xml';
$remoteFile='/sappodata/tobeprocessed/LFS_PO_20200831_034813.xml';
$host = "arddms.intouchrewards.com";
$port = 22;
$user = "arddms";
$pass = "arddms";

$connection = ssh2_connect($host, $port);
ssh2_auth_password($connection, $user, $pass);
$sftp = ssh2_sftp($connection);

$stream = fopen("ssh2.sftp://$sftp$remoteFile", 'w');
$file = file_get_contents($localFile);
fwrite($stream, $file);
fclose($stream);

?>