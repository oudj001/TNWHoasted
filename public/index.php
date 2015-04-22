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

function redirect($location){
	header("Location: {$location}");
	exit;
}

$router->map('GET', '/connect', function(){

	$authorizeUrl = getWebAuth()->start();

	redirect($authorizeUrl);
	
});

$router->map('GET', '/account', function(){
	if(!isset($_SESSION['account_id'])){
		redirect('/?error=not-authorized');
	}
	$account = Account::find((int)$_SESSION['account_id']);
	var_dump($account);
});

$router->map('GET', '/auth-finish', function(){

	try {
		list($accessToken, $userId, $urlState) = getWebAuth()->finish($_GET);

		$client = new dbx\Client($accessToken, CLIENT_IDENTIFIER);
		$accountInfo = $client->getAccountInfo();

		$account = Account::find('first', ['dropbox_uid' => $userId]);

		if(!$account){
			$account = new Account([
				'dropbox_uid' => $userId,
			]);
		}
		$account->set_attributes([
			'name' => $accountInfo['display_name'],
			'email' => $accountInfo['email'],
			'access_token' => $accessToken
		]);

		$account->save();

		$_SESSION['account_id'] = $account->id;
		redirect('/account');
	}
	catch (dbx\WebAuthException_BadRequest $ex) {
		error_log("/dropbox-auth-finish: bad request: " . $ex->getMessage());
		// Respond with an HTTP 400 and display error page...
	}
	catch (dbx\WebAuthException_BadState $ex) {
		// Auth session expired.  Restart the auth process.
		var_dump($ex->getMessage());
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
});

$match = $router->match();
if($match){
	call_user_func($match['target']);
}