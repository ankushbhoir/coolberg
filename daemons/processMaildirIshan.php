#!/usr/bin/php -q
<?php
include '/home/vlcc/it_config.php';

require_once 'lib/db/DBConn.php';
require_once 'lib/core/Constants.php';
require_once "lib/mailparse/MimeMailParser.class.php";

define("DEF_MAILDIR_PROCESSED_FOLDER", '/home/vlcc/Maildir/processed/');
define("DEF_MAILDIR_ATTACHMENTS_FOLDER", '/home/vlcc/Maildir/attachments/');
define("DEF_MAILDIR_ATTACHMENTS_FOLDERNEW", '/home/vlcc/public_html/vlcc_dt/home/Parsers/receivedPO/');
define("DEF_MAILDIR_IGNORED_FOLDER", '/home/vlcc/Maildir/ignored/');

$g_valid_extensions = array('pdf', 'html', 'htm', 'xls', 'xlsx');

$db = new DBConn();
processMaildir('/home/vlcc/Maildir/cur/');
processMaildir('/home/vlcc/Maildir/new/');
processMaildir('/home/vlcc/Maildir/.spam/new/');
processMaildir('/home/vlcc/Maildir/.spam/cur/');

function processMailDir($maildir_path) {
    $handle = opendir($maildir_path);
    if (!$handle) return;

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        $file_path = "$maildir_path$entry";
        if (!is_file($file_path)) continue;
        processMailFile($file_path);
    }

    closedir($handle);
}

function processMailFile($file_path) {
    $Parser = new MimeMailParser();
    $Parser->setPath($file_path);

    $headers = $Parser->getHeaders();

    $from_email = $Parser->getHeader('from');
    $to_email = $Parser->getHeader('to');
    $receipt_date = $Parser->getHeader('date');

    $attachments = $Parser->getAttachments();
    if (count($attachments) == 0) { // move file to ignored folder and return
        rename($file_path, DEF_MAILDIR_IGNORED_FOLDER . basename($file_path));
        return;
    }

    // save to db and get uniqid
    global $db;
    $from_email = $db->safe($from_email);
    $to_email = $db->safe($to_email);
    $receipt_date = $db->safe($receipt_date);
    $db_file_path = $db->safe($file_path);
    $attachment_count = count($attachments);
    $insert_id = $db->execInsert("insert into it_incoming_vlcc_emails set from_email=$from_email, to_email=$to_email, receipt_date=$receipt_date, mailfile=$db_file_path, num_attachments = $attachment_count");

    $count=0;
    $filenames = array();
    foreach ($attachments as $attachment) {
	$count++;
        $filename = $attachment->filename;
        $filenames[] = $filename;
        $filename = $insert_id."_".$count."_".str_replace(" ", "", $filename);
//        $save_file_path = DEF_MAILDIR_ATTACHMENTS_FOLDER . $filename;
 //       if ($fp = fopen($save_file_path, 'w')) {
 //           while($bytes = $attachment->read()) {
 //               fwrite($fp, $bytes);
 //           }
 //           fclose($fp);
  //      }

 echo "insert into it_po_details set from_email=$from_email, po_filenames='".$filename."'";
 $newpo_entry = $db->execInsert("insert into it_po_details set from_email=$from_email, po_filenames='".$filename."'");


	
	$save_file_path = DEF_MAILDIR_ATTACHMENTS_FOLDERNEW . $filename;
        if ($fp = fopen($save_file_path, 'w')) {
            while($bytes = $attachment->read()) {
                fwrite($fp, $bytes);
            }
            fclose($fp);
        }

        $new_file_path = DEF_MAILDIR_PROCESSED_FOLDER . $insert_id . "_". basename($file_path);
        rename($file_path, $new_file_path);
    }
    $attachment_filenames = $db->safe(join("<>", $filenames));
    $db->execUpdate("update it_incoming_vlcc_emails set attachment_filenames=$attachment_filenames where id=$insert_id");
}

