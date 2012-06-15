<?php
require_once 'Zend/Mobile/Push/Apns.php';
require_once 'Zend/Mobile/Push/Message/Apns.php';

$message = new Zend_Mobile_Push_Message_Apns();
$message->setAlert('Zend Mobile Push Example');
$message->setBadge(1);
$message->setSound('default');
$message->setId(time());
$message->setToken('ABCDEF0123456789');
 
$apns = new Zend_Mobile_Push_Apns();
$apns->setCertificate('/path/to/provisioning-certificate.pem');
 
try {
    $apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
} catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
    // you can either attempt to reconnect here or try again later
    exit(1);
} catch (Zend_Mobile_Push_Exception $e) {
    echo 'APNS Connection Error:' . $e->getMessage();
    exit(1);
}
 
try {
    $apns->send($message);
} catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
    // you would likely want to remove the token from being sent to again
    echo $e->getMessage();
} catch (Zend_Mobile_Push_Exception $e) {
    // all other exceptions only require action to be sent
    echo $e->getMessage();
}
$apns->close();
