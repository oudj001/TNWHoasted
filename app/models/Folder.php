<?php

class Folder extends ActiveRecord\Model {

	static $belongs_to = [
		['account']
	],
	$validates_uniqueness_of = [
		['name'],
		['urlname'],
	];

	protected function get_dbx_client(){
		return new Dropbox\Client($this->account->access_token, CLIENT_IDENTIFIER);
	}

	protected function get_dbx_folder_path(){
		return "/{$this->name}";
	}

	private function _createDbxFolder(){
		return $this->dbx_client->createFolder($this->dbx_folder_path);
	}

	private function _ensureDbxFolder(){
		if($this->dbx_client->getMetadata($this->dbx_folder_path) === null){
			$this->_createDbxFolder();
		}
	}

	public function before_validation(){
		$this->urlname = PhpInflector\Inflector::parameterize($this->name);
	}

	public function before_create(){
		return $this->_createDbxFolder() !== null;
	}

	public function get_public_url(){
		return 'https://' . $_SERVER['HTTP_HOST'] . "/u/{$this->account->dropbox_uid}/{$this->urlname}";
	}

	public function uploadFile($filePath, $fileName = null){
		$this->_ensureDbxFolder();
		$fileName = $fileName ?: basename($filePath);
		$file = fopen($filePath, 'rb');
		$meta = $this->dbx_client->uploadFile("{$this->dbx_folder_path}/{$fileName}", Dropbox\WriteMode::add(), $file);
		fclose($file);
		return $meta;
	}

	public function getShareableLink(){
		return $this->dbx_client->createShareableLink($this->dbx_folder_path);
	}

	public function inviteByEmail($to){
		$mandrill = new Mandrill(MANDRILL_API_KEY);

		$message = array(
			'html' => sprintf('<p><a href="%s">Start uploading!</a></p>', $this->public_url),
			'text' => $this->public_url,
			'subject' => "You're invited for {$this->name}",
			'from_email' => INVITE_ORIGINATOR,
			'from_name' => 'DropToBox',
			'to' => array(
				array(
					'email' => $to,
					// 'name' => 'Recipient Name',
					// 'type' => 'to'
				)
			)
		);
		return $mandrill->messages->send($message);
	}

}
