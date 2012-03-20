<?php
require_once 'Zend/Mobile/Push/C2dm.php';
require_once 'Zend/Mobile/Push/Message/C2dm.php';
require_once 'Zend/Gdata/ClientLogin.php';

try {
    $client = Zend_Gdata_ClientLogin::getHttpClient(
        'my@gmail.com', // REPLACE WITH YOUR GOOGLE ACCOUNT
        'myPassword', // REPLACE WITH YOUR PASSWORD
        Zend_Mobile_Push_C2dm::AUTH_SERVICE_NAME,
        null,
        'myAppName' // REPLACE WITH YOUR APP NAME
    );
} catch (Zend_Gdata_App_CaptchaRequiredException $cre) {
    // manual login is required
    echo 'URL of CAPTCHA image: ' . $cre->getCaptchaUrl() . PHP_EOL;
    echo 'Token ID: ' . $cre->getCaptchaToken() . PHP_EOL;
    exit(1);
} catch (Zend_Gdata_App_AuthException $ae) {
    echo 'Problem authenticating: ' . $ae->exception() . PHP_EOL;
    exit(1);
}
 
$message = new Zend_Mobile_Push_Message_C2dm();
$message->setId(time());
$message->setToken('ABCDEF0123456789');
$message->setData(array(
    'foo' => 'bar',
    'bar' => 'foo',
));
 
$c2dm = new Zend_Mobile_Push_C2dm();
$c2dm->setLoginToken($client->getClientLoginToken());
 
try {
    $c2dm->send($message);
} catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
    // you would likely want to remove the token from being sent to again
    echo $e->getMessage();
} catch (Zend_Mobile_Push_Exception $e) {
    // all other exceptions only require action to be sent or implementation of exponential backoff.
    echo $e->getMessage();
}
