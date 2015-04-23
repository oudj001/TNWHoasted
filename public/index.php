<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();


define('APP_ROOT', realpath(__DIR__ . '/..'));
require_once APP_ROOT . "/vendor/autoload.php";

if(!getenv('APPLICATION_ENV') || getenv('APPLICATION_ENV') == 'development'){
	Dotenv::load(APP_ROOT);
}

use \Dropbox as dbx;
use Symfony\Component\Yaml\Yaml;

ActiveRecord\Config::initialize(function($cfg){
	$cfg->set_model_directory(APP_ROOT . '/app/models');
	$connections = Yaml::parse(file_get_contents(APP_ROOT . '/db/config.yml'));
	if($database_url = getenv('DATABASE_URL')){
		$connections['production'] = $database_url;
	}
	$cfg->set_connections($connections);
	$cfg->set_default_connection(getenv('APPLICATION_ENV') ?: 'development');
});

define('CLIENT_IDENTIFIER', 'TNWDropboxUploader/1.0');
define('INVITE_ORIGINATOR', 'invite@' . $_SERVER['HTTP_HOST']);

define('MANDRILL_API_KEY', getenv('MANDRILL_API_KEY'));
define('DROPBOX_KEY', getenv('DROPBOX_KEY'));
define('DROPBOX_SECRET', getenv('DROPBOX_SECRET'));

function getWebAuth(){

	$appInfo = new dbx\AppInfo(DROPBOX_KEY, DROPBOX_SECRET);
	$csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
	$redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/auth-finish';

	return new dbx\WebAuth($appInfo, CLIENT_IDENTIFIER, $redirectUrl, $csrfTokenStore);
}

function redirect($location, $query = null){
	if($query !== null){
		$location .= '?' . http_build_query($query);
	}
	header("Location: {$location}");
	exit;
}

$router = new AltoRouter();
function router(){
	global $router;
	return $router;
}

$router->map('GET', '/', function($params){
	include APP_ROOT . '/app/views/index.php';
}, 'root');

# User upload form
$router->map('GET', '/u/[i:dropbox_uid]/[:urlname]', function($params){

	$folder = Folder::find('first', [
		'conditions' => [
			'urlname = ? AND dropbox_uid = ?',
			$params['urlname'], $params['dropbox_uid']
		],
		'joins' => 'JOIN accounts ON folders.account_id = accounts.id'
	]);

	if(!$folder){
		redirect(router()->generate('root'), ['error' => 'not_found']);
	}

	$authorized_folders = isset($_SESSION['authorized_folders']) ? $_SESSION['authorized_folders'] : [];

	if($folder->password && (!isset($authorized_folders[$folder->id]) || $folder->password != $authorized_folders[$folder->id])){

		$login_url = router()->generate('login', $params);
		include APP_ROOT . '/app/views/user/login.php';

	}else{
		$upload_url = router()->generate('upload', $params);
		include APP_ROOT . '/app/views/user/index.php';

	}

}, 'user');

# User upload
$router->map('POST', '/u/[i:dropbox_uid]/[:urlname]', function($params){

	$folder = Folder::find('first', [
		'conditions' => [
			'urlname = ? AND dropbox_uid = ?',
			$params['urlname'], $params['dropbox_uid']
		],
		'joins' => 'JOIN accounts ON folders.account_id = accounts.id'
	]);

	$authorized_folders = isset($_SESSION['authorized_folders']) ? $_SESSION['authorized_folders'] : [];

	if(!$folder->password || $folder->password == $authorized_folders[$folder->id]){
		$file = $folder->uploadFile($_FILES['file']['tmp_name'], $_FILES['file']['name']);
		redirect(router()->generate('user', $params), ['success' => 'true']);
	}
}, 'upload');

# User login
$router->map('POST', '/u/[i:dropbox_uid]/[:urlname]/login', function($params){

	$folder = Folder::find('first', [
		'conditions' => [
			'urlname = ? AND dropbox_uid = ?',
			$params['urlname'], $params['dropbox_uid']
		],
		'joins' => 'JOIN accounts ON folders.account_id = accounts.id'
	]);

	if(password_verify($_POST['password'], $folder->password)){
		$_SESSION['authorized_folders'] = isset($_SESSION['authorized_folders']) ? $_SESSION['authorized_folders'] : [];
		$_SESSION['authorized_folders'][$folder->id] = $folder->password;
	}
	redirect(router()->generate('user', $params));

}, 'login');

$router->map('GET', '/connect', function(){

	$authorizeUrl = getWebAuth()->start();

	redirect($authorizeUrl);
	
});

$router->map('GET', '/account', function(){
	if(!isset($_SESSION['account_id'])){
		redirect(router()->generate('root'), ['error' => 'not-authorized']);
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folders_url = router()->generate('folders');

	include APP_ROOT . '/app/views/account/index.php';

}, 'account');

$router->map('POST', '/account/folders', function(){
	if(!isset($_SESSION['account_id'])){
		redirect(router()->generate('root'), ['error' => 'not-authorized']);
	}
	$account = Account::find((int)$_SESSION['account_id']);
	
	$account->create_folders(['name' => $_POST['name']]);

	redirect(router()->generate('account'));
}, 'folders');

$router->map('GET', '/account/folders/[:urlname]', function($params){
	if(!isset($_SESSION['account_id'])){
		redirect(router()->generate('root'), ['error' => 'not-authorized']);
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folder = Folder::find('first', ['account_id' => $account->id, 'urlname' => $params['urlname']]);

	$invite_url = router()->generate('invite', $params);
	$password_url = router()->generate('password', $params);

	include APP_ROOT . '/app/views/account/folder.php';

}, 'folder');

$router->map('POST', '/account/folders/[:urlname]/invite', function($params){
	if(!isset($_SESSION['account_id'])){
		redirect(router()->generate('root'), ['error' => 'not-authorized']);
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folder = Folder::find('first', ['account_id' => $account->id, 'urlname' => $params['urlname']]);

	$folder->inviteByEmail($_POST['email']);
	redirect(router()->generate('folder', $params));
}, 'invite');

$router->map('POST', '/account/folders/[:urlname]/password', function($params){
	if(!isset($_SESSION['account_id'])){
		redirect(router()->generate('root'), ['error' => 'not-authorized']);
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folder = Folder::find('first', ['account_id' => $account->id, 'urlname' => $params['urlname']]);

	$folder->update_attribute('password', password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 10]));
	redirect(router()->generate('folder', $params));
}, 'password');

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
		redirect(router()->generate('account'));
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
	call_user_func($match['target'], $match['params']);
}
