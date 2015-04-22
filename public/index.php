<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();


define('APP_ROOT', realpath(__DIR__ . '/..'));
require_once APP_ROOT . "/vendor/autoload.php";

use \Dropbox as dbx;

ActiveRecord\Config::initialize(function($cfg){
	$cfg->set_model_directory(APP_ROOT . '/app/models');
	$cfg->set_connections([
		'development' => 'mysql://root@localhost/droptobox'
	]);
});

define('CLIENT_IDENTIFIER', 'TNWDropboxUploader/1.0');

$router = new AltoRouter();

$router->map('GET', '/', function(){
	echo '<a href="/connect">Connect</a>';
});

function getWebAuth(){

	$appInfo = dbx\AppInfo::loadFromJsonFile(APP_ROOT . "/app-info.json");
	$csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
	$redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/auth-finish';

	return new dbx\WebAuth($appInfo, CLIENT_IDENTIFIER, $redirectUrl, $csrfTokenStore);
}

$router->map('GET', '/connect', function(){

	$authorizeUrl = getWebAuth()->start();

	header("Location: {$authorizeUrl}");
	
});

$router->map('GET', '/auth-finish', function(){

	try {
		list($accessToken, $userId, $urlState) = getWebAuth()->finish($_GET);
		var_dump($urlState === null);  // Since we didn't pass anything in start()
	}
	catch (dbx\WebAuthException_BadRequest $ex) {
		error_log("/dropbox-auth-finish: bad request: " . $ex->getMessage());
		// Respond with an HTTP 400 and display error page...
	}
	catch (dbx\WebAuthException_BadState $ex) {
		// Auth session expired.  Restart the auth process.
		var_dump($ex);
		// header('Location: /connect');
	}
	catch (dbx\WebAuthException_Csrf $ex) {
		error_log("/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage());
		// Respond with HTTP 403 and display error page...
	}
	catch (dbx\WebAuthException_NotApproved $ex) {
		error_log("/dropbox-auth-finish: not approved: " . $ex->getMessage());
	}
	catch (dbx\WebAuthException_Provider $ex) {
		error_log("/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage());
	}
	catch (dbx\Exception $ex) {
		error_log("/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage());
	}

	// We can now use $accessToken to make API requests.
	$client = new dbx\Client($accessToken, CLIENT_IDENTIFIER);
	var_dump($client);

});

$router->map('GET', '/test', function(){
	$client = new dbx\Client("3xqpArsI4wAAAAAAAAAOb6GlYnIxos0cQWRFw6m6TV4qzjPJlsT2HruCLOo0X4sn", CLIENT_IDENTIFIER);
	var_dump($client);
});

$match = $router->match();
if($match){
	call_user_func($match['target']);
}