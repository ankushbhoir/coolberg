<?php
require_once("/home/vlcc/public_html/vlcc_dt/it_config.php");
require_once("../lib/aws2.8.16/aws-autoloader.php");
//require_once 'lib/db/DBConn.php';
require_once '../lib/core/Constants.php';
require_once "../lib/mailparse/MimeMailParser.class.php";

//use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;
	// AWS Info
	$bucketName = 'intouchcdn';
	$IAM_KEY = 'AKIAIR7HYJ7WZ4YYKTIQ';
	$IAM_SECRET = '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';
	// Connect to AWS
	try {
		// You may need to change the region. It will say in the URL when the bucket is open
		// and on creation.
		$s3 = S3Client::factory(
			array(
				'credentials' => array(
					'key' => $IAM_KEY,
					'secret' => $IAM_SECRET
				),
				'version' => 'latest',
				'region'  => 'ap-south-1',
				'signature' => 'v4'
			)
		);
//		$s3->setSignatureVersion('v4');
	} catch (Exception $e) {
		// We use a die, so if this fails. It stops here. Typically this is a REST call so this would
		// return a json object.
		die("Error: " . $e->getMessage());
	}

// Use the plain API (returns ONLY up to 1000 of your objects).
try {
echo 'test1';
    $result = $s3->listObjects([
        'Bucket' => $bucketName,
        'Prefix' => 'data.ipn/'
    ]);
echo 'test';
    echo "Keys retrieved!" . PHP_EOL;
    foreach ($result['Contents'] as $object) {

	$result1 = $s3->getObject(array(
    	'Bucket' => $bucketName,
    		'Key'    => $object['Key']
	));
	processMailFile($result1['Body'], $s3, $bucketName);
//	echo $result1['Body'] . "\n";
       // echo $object['Key'] . PHP_EOL;
    }
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
	echo 'Done';
	// Now that you have it working, I recommend adding some checks on the files.
	// Example: Max size, allowed file types, etc.

function processMailFile($file_path, $s3, $bucketName) {
try {
    $Parser = new MimeMailParser();
    $Parser->setText($file_path);

    $headers = $Parser->getHeaders();

    $from_email = $Parser->getHeader('from');
    $to_email = $Parser->getHeader('to');
    $receipt_date = $Parser->getHeader('date');

    $attachments = $Parser->getAttachments();
    if (count($attachments) == 0) { // move file to ignored folder and return
       // rename($file_path, DEF_MAILDIR_IGNORED_FOLDER . basename($file_path));
       // return;
	echo 'No attachment frond';
	return;
    }
    

// save to db and get uniqid
    global $db;
//    $from_email = $db->safe($from_email);
   
//    $to_email = $db->safe($to_email);
//    $receipt_date = $db->safe($receipt_date);
//    $db_file_path = $db->safe($file_path);
//    $attachment_count = count($attachments);
//    $insert_id = $db->execInsert("insert into it_incoming_emails set from_email=$from_email, to_email=$to_email, receipt_date=$receipt_date, mailfile=$db_file_path, num_attachments = $attachment_count");
$insert_id = 123;
    $count=0;
    $filenames = array();
    foreach ($attachments as $attachment) {
        $count++;
        $filename = $attachment->filename;
        $filenames[] = $filename;
        $filename = $insert_id."_".$count."_".str_replace(" ", "", $filename);
//        $save_file_path = DEF_MAILDIR_ATTACHMENTS_FOLDER . $filename;
	if (!file_exists('/tmp/tmpfile')) {
			mkdir('/tmp/tmpfile');
		}
	$save_file_path = '/tmp/tmpfile/'.$filename;
        if ($fp = fopen($save_file_path, 'w')) {
            while($bytes = $attachment->read()) {
                fwrite($fp, $bytes);
            }
            fclose($fp);
        }
	$put=$s3->putObject(
		array(
			'Bucket'=>$bucketName,
			'Key' => 'weikfield/receivedfiles/'.$filename,
			'SourceFile' => $save_file_path,
			'StorageClass' => 'REDUCED_REDUNDANCY'
		)
	);
	//print $put;
       // $new_file_path = DEF_MAILDIR_PROCESSED_FOLDER . $insert_id . "_". basename($file_path);
       // rename($file_path, $new_file_path);
    }
   // $attachment_filenames = $db->safe(join("<>", $filenames));
   // $db->execUpdate("update it_incoming_emails set attachment_filenames=$attachment_filenames where id=$insert_id");
} catch (S3Exception $e) {
		echo $e->getMessage();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

?>
