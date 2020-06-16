<?php
ini_set('display_errors', "On");
require 'vendor/autoload.php';
$email = new \SendGrid\Mail\Mail();
$email->setFrom("mirko@mirko.jp", "送信者A");
$email->setSubject("TestMail漢字");
$email->addTo("catfish.m@nifty.com", "受信者B");
$email->addContent("text/plain", "日本語 English");
$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
	
	echo 'メール送信しましたyoyo。';

} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}
