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

}
