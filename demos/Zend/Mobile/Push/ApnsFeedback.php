<?php
require_once 'Zend/Mobile/Push/Apns.php';

$apns = new Zend_Mobile_Push_Apns();
$apns->setCertificate('/path/to/provisioning-certificate.pem');
 
try {
    $apns->connect(Zend_Mobile_Push_Apns::SERVER_FEEDBACK_SANDBOX_URI);
} catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
    // you can either attempt to reconnect here or try again later
    exit(1);
} catch (Zend_Mobile_Push_Exception $e) {
    echo 'APNS Connection Error:' . $e->getMessage();
    exit(1);
}
 
$tokens = $apns->feedback();
while(list($token, $time) = each($tokens)) {
    echo $time . "\t" . $token . PHP_EOL;
}
$apns->close();
