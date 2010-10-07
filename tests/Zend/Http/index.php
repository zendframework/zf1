<?php

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
$autoloader = Zend_Loader_Autoloader::getInstance();

error_reporting(E_ALL);
set_time_limit(0);

$config['config']['wurflapi']['wurfl_lib_dir'] = dirname(__FILE__) . '/_files/Wurfl/1.1/';
$config['config']['wurflapi']['wurfl_config_file'] = dirname(__FILE__) . '/_files/Wurfl/resources/wurfl-config.php';
$config['server'] = $_SERVER;

if (!empty($_GET['userAgent'])) {
    $config['server']['http_user_agent'] = $_GET['userAgent'];
} else {
    $_GET['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
}

if (!empty($_GET['sequence'])) {
    $config['config']['identification_sequence'] = $_GET['sequence'];
}
$oUserAgent = new Zend_Http_UserAgent($config);

//$oUserAgent = Zend_Http_UserAgent::getInstance ();


function printBrowserDetails($browser)
{
    $device = $browser->getDevice();
    //Zend_Debug::dump($device->getAllFeatures());
    if (isset($device)) {
        print "<b>General informations</b>";
        print "<ul>";
        print "<li>Browser Type: " . $browser->getBrowserType() . "</li>";
        print "<li>Browser Name: " . $device->getFeature('browser_name') . "</li>";
        print "<li>Browser Version: " . $device->getFeature('browser_version') . "</li>";
        print "<li>Browser Compatibility: " . $device->getFeature('browser_compatibility') . "</li>";
        print "<li>Browser Engine: " . $device->getFeature('browser_engine') . "</li>";
        print "<li>Device OS Name: " . $device->getFeature('device_os_name') . "</li>";
        print "<li>Device OS token: " . $device->getFeature('device_os_token') . "</li>";
        print "<li>Server Os: " . $device->getFeature('server_os') . "</li>";
        print "<li>Server OS Version: " . $device->getFeature('server_os_version') . "</li>";
        print "</ul>";
        
        $wurfl = $device->getFeature("mobile_browser");
        if (!$wurfl) {
            print "<b>no WURFL identification</b>";
        } else {
            print "<b>WURFL capabilities :</b>";
            print "<ul>";
            print "<li>WURFL ID: " . (isset($device->id) ? $device->id : "") . "</li>";
            print "<li>Mobile browser: " . $device->getFeature("mobile_browser") . "</li>";
            print "<li>Mobile browser version: " . $device->getFeature("mobile_browser_version") . "</li>";
            print "<li>Device Brand Name: " . $device->getFeature("brand_name") . "</li>";
            print "<li>Device Model Name: " . $device->getFeature('model_name') . "</li>";
            print "<li>Device OS: " . $device->getFeature('device_os') . "</li>";
            print "<li>Xhtml Preferred Markup:" . $device->getFeature('preferred_markup') . "</li>";
            print "<li>Resolution Width:" . $device->getFeature('resolution_width') . "</li>";
            print "<li>Resolution Height:" . $device->getFeature('resolution_height') . "</li>";
            print "<li>MP3:" . $device->getFeature('mp3') . "</li>";
            print "</ul>";
        }
        
        print "<br /><br />";
        print "<b>Full</b>";
        Zend_Debug::dump($device->getAllFeatures());
    }

}

?>

<div id="content">

<p><b>Query by providing the user agent:</b></p>
<p>look at <a target="_blank"
	href="http://www.useragentstring.com/pages/useragentstring.php">http://www.useragentstring.com/pages/useragentstring.php</a></p>
<p>For mobile, look at <a target="_blank"
	href="http://www.mobilemultimedia.be/">http://www.mobilemultimedia.be/</a></p>
<fieldset>
<form method="get">
<div>Sequence : <select name="sequence">
	<option value="">(standard)</option>
	<option value="mobile, text, desktop">mobile, text, desktop</option>
	<option value="bot, validator, checker, console, offline, email, text">bot,
	validator, checker, console, offline, email, text</option>
</select> (DON'T FORGET TO CLEAN SESSION COOKIE)<br />
User Agent : <input type="text" name="userAgent" size="40"
	value="<?=htmlentities($_GET['userAgent'])?>" /> <br />
<input type="submit" /></div>
</form>
</fieldset>

<?php
if ($oUserAgent) {
    printBrowserDetails($oUserAgent);
}
?>

</div>
