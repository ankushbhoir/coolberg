<?php
//include '/home/vlcc/public_html/vlcc_dt/it_config.php';
include '/home/vlcc/public_html/vlcc_dt/it_config.php';
require_once 'lib/db/DBConn.php';
require_once 'lib/core/Constants.php';
require_once "lib/mailparse/MimeMailParser.class.php";
require_once 'lib/s3/S3Helper.php';

define("DEF_MAILDIR_PROCESSED_FOLDER", '/vlcc/processed');
define("DEF_MAILDIR_ATTACHMENTS_FOLDER", '/vlcc/receivedfiles/');
define("DEF_MAILDIR_IGNORED_FOLDER", '/vlcc/ignored');

$g_valid_extensions = array('pdf', 'html', 'htm', 'xls', 'xlsx');

$db = new DBConn();
$s3 = new S3Helper();
//processMaildir('/weikfield/emails/');
processMaildir('vlcc/emails/');
//processMaildir('/home/vlcc/Maildir/.spam/new/');
//processMaildir('/home/vlcc/Maildir/.spam/cur/');

function processMailDir($maildir_path) {

global $s3;
	$emails = $s3->getFilesList($maildir_path);
//	$emails = $s3->getFilesListIterator($maildir_path);
//	$emails = $s3->getFilesPaginator($maildir_path);
//	print_r ($emails);
	foreach ($emails as $object) {
	//	print_r ($object);
		$filename = $s3->getFileName($object);
		echo 'FileName' .$filename. PHP_EOL;
		if(trim($filename) == ''){
			continue;
		}
		$content = $s3->getFile($filename, $maildir_path);
       		 processMailFile($content, $filename, $maildir_path);
	}
}

function processMailFile($file_path, $s3filename, $maildir_path) {
	echo 'reached1';
    $Parser = new MimeMailParser();
    $Parser->setText($file_path);
    global $s3;
    $headers = $Parser->getHeaders();

    $from_email = $Parser->getHeader('from');
    $to_email = $Parser->getHeader('to');
    $receipt_date = $Parser->getHeader('date');

    $attachments = $Parser->getAttachments();
	echo 'reached2';
    if (count($attachments) == 0) { // move file to ignored folder and return
	$s3->moveFile2($maildir_path,DEF_MAILDIR_IGNORED_FOLDER, $s3filename );
        return;
    }

    // save to db and get uniqid
    global $db;
    $from_email = $db->safe($from_email);
    $to_email = $db->safe($to_email);
    $receipt_date = $db->safe($receipt_date);
//    $db_file_path = $db->safe($file_path);
	echo 'reached3';
    $attachment_count = count($attachments);
	echo "insert into it_incoming_vlccdt_emails set from_email=$from_email, to_email=$to_email, receipt_date=$receipt_date, mailfile='', num_attachments = $attachment_count".PHP_EOL;
    $insert_id = $db->execInsert("insert into it_incoming_vlccdt_emails set from_email=$from_email, to_email=$to_email, receipt_date=$receipt_date, mailfile='', num_attachments = $attachment_count");

    $count=0;
    $filenames = array();
    foreach ($attachments as $attachment) {
	$count++;
        $filename = $attachment->filename;
        $filenames[] = $filename;
        $filename = $insert_id."_".$count."_".str_replace(" ", "", $filename);	
       // $save_file_path = DEF_MAILDIR_ATTACHMENTS_FOLDER . $filename;
	 if (!file_exists('receivedPO')) {
                        mkdir('receivedPO');
                }
        $save_file_path = 'receivedPO/'.$filename;
	echo PHP_EOL .$save_file_path. PHP_EOL;
        if ($fp = fopen($save_file_path, 'w')) {
            while($bytes = $attachment->read()) {
                fwrite($fp, $bytes);
            }
            fclose($fp);
        }
//	$s3->moveFile($maildir_path,DEF_MAILDIR_ATTACHMENTS_FOLDER, $filename );
	
	$s3->saveFile(DEF_MAILDIR_ATTACHMENTS_FOLDER, $filename, $save_file_path);
	//$s3->moveFile2($maildir_path,DEF_MAILDIR_PROCESSED_FOLDER, $s3filename );
       // if ($fp = fopen($save_file_path, 'w')) {
       //     while($bytes = $attachment->read()) {
       //         fwrite($fp, $bytes);
       //     }
       //     fclose($fp);
       // }

       // $new_file_path = DEF_MAILDIR_PROCESSED_FOLDER . $insert_id . "_". basename($file_path);
       // rename($file_path, $new_file_path);
    }
    $attachment_filenames = $db->safe(join("<>", $filenames));
	echo "update it_incoming_vlccdt_emails set attachment_filenames=$attachment_filenames where id=$insert_id".PHP_EOL;
    $db->execUpdate("update it_incoming_vlccdt_emails set attachment_filenames=$attachment_filenames where id=$insert_id");
    $s3->moveFile2($maildir_path,DEF_MAILDIR_PROCESSED_FOLDER, $s3filename );
}

