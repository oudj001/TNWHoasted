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

function redirect($location){
	header("Location: {$location}");
	exit;
}


$router = new AltoRouter();

$router->map('GET', '/', function(){
	echo '<a href="/connect">Connect</a>';
});

# User upload form
$router->map('GET', '/u/[i:dropbox_uid]/[:urlname]', function($params){

	$folder = Folder::find('first', [
		'conditions' => [
			'urlname = ? AND dropbox_uid = ?',
			$params['urlname'], $params['dropbox_uid']
		],
		'joins' => 'JOIN accounts ON folders.account_id = accounts.id'
	]);

	$authorized_folders = isset($_SESSION['authorized_folders']) ? $_SESSION['authorized_folders'] : [];

	if($folder->password && !in_array($folder->id, $authorized_folders)){
		echo <<<HTML
<form action="/u/{$params['dropbox_uid']}/{$params['urlname']}/login" method="POST">
	<label>
		Password<br>
		<input type="password" name="password">
	</label>
	<input type="submit" value="Login">
</form>
HTML;
	}else{
		echo <<<HTML
			<p>
				<a href="{$folder->getShareableLink()}">View folder contents</a>
			</p>
<form action="/u/{$params['dropbox_uid']}/{$params['urlname']}" method="POST" enctype="multipart/form-data">
	<label>
		<input type="file" name="file" value="">
	</label>
	<input type="submit" value="Upload file">
</form>
HTML;

	}

});

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

	if(!$folder->password || in_array($folder->id, $authorized_folders)){
		$file = $folder->uploadFile($_FILES['file']['tmp_name'], $_FILES['file']['name']);
		var_dump($file);
	}else{
		echo 'NOT AUTHENTICATED';
	}
});

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
		$_SESSION['authorized_folders'][] = $folder->id;
	}
	redirect("/u/{$params['dropbox_uid']}/{$params['urlname']}");
});

$router->map('GET', '/connect', function(){

	$authorizeUrl = getWebAuth()->start();

	redirect($authorizeUrl);
	
});

$router->map('GET', '/account', function(){
	if(!isset($_SESSION['account_id'])){
		redirect('/?error=not-authorized');
	}
	$account = Account::find((int)$_SESSION['account_id']);

	if(!!$account->folders){
		echo '<h3>Folders</h3>';
		echo '<ul>';
		foreach($account->folders as $_folder){
			echo '<li><a href="/account/folders/' . $_folder->urlname . '">' . $_folder->name . '</a></li>';
		}
		echo '</ul>';
	}
	
	echo <<<HTML
<form action="/account/folders" method="POST">
	<label>
		<input type="text" name="name" value="" placeholder="Folder name">
	</label>
	<input type="submit" value="Create folder">
</form>
HTML;

});

$router->map('POST', '/account/folders', function(){
	if(!isset($_SESSION['account_id'])){
		redirect('/?error=not-authorized');
	}
	$account = Account::find((int)$_SESSION['account_id']);
	
	$account->create_folders(['name' => $_POST['name']]);

	redirect('/account');
});

$router->map('GET', '/account/folders/[:urlname]', function($params){
	if(!isset($_SESSION['account_id'])){
		redirect('/?error=not-authorized');
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folder = Folder::find('first', ['account_id' => $account->id, 'urlname' => $params['urlname']]);

	echo <<<HTML
<form action="/account/folders/{$params['urlname']}/password" method="POST">
	<label>
		Only enter password if you want to change it<br>
		<input type="password" name="password">
	</label>
	<input type="submit" value="Set password">
</form>
<form action="/account/folders/{$params['urlname']}/invite" method="POST">
	<label>
		Invite by email<br>
		<input type="text" name="email">
	</label>
	<input type="submit" value="Send invite">
</form>
HTML;

});

$router->map('POST', '/account/folders/[:urlname]/invite', function($params){
	if(!isset($_SESSION['account_id'])){
		redirect('/?error=not-authorized');
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folder = Folder::find('first', ['account_id' => $account->id, 'urlname' => $params['urlname']]);

	$folder->inviteByEmail($_POST['email']);
	redirect("/account/folders/{$folder->urlname}");
});

$router->map('POST', '/account/folders/[:urlname]/password', function($params){
	if(!isset($_SESSION['account_id'])){
		redirect('/?error=not-authorized');
	}
	$account = Account::find((int)$_SESSION['account_id']);
	$folder = Folder::find('first', ['account_id' => $account->id, 'urlname' => $params['urlname']]);

	$folder->update_attribute('password', password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 10]));
	redirect("/account/folders/{$folder->urlname}");
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
	call_user_func($match['target'], $match['params']);
}