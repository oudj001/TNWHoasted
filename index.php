<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once __DIR__ . "/vendor/autoload.php";
use \Dropbox as dbx;

$appInfo = dbx\AppInfo::loadFromJsonFile("app-info.json");
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");

$authorizeUrl = $webAuth->start();

echo "1. Go to: " . $authorizeUrl . "\n";
echo "2. Click \"Allow\" (you might have to log in first).\n";
echo "3. Copy the authorization code.\n";
$authCode = \trim('7TnrbzttMUEAAAAAAAAKriIu6WVnxu86V2mEeVhdNHQ');

list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
print "Access Token: " . $accessToken . "\n";

$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
$accountInfo = $dbxClient->getAccountInfo();
print_r($accountInfo);

$f = fopen("test.rtf", "rb");
$result = $dbxClient->uploadFile("/test.rtf", dbx\WriteMode::add(), $f);
fclose($f);
print_r($result);

?>