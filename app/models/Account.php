<?php

class Account extends ActiveRecord\Model {

	static $validates_uniqueness_of = [
		['dropbox_uid'],
		['email']
	],
	$has_many = [
		['folders']
	];

  public function getFolder($urlname){
    return Folder::find('first', ['account_id' => $this->id, 'urlname' => $urlname]);
  }

}
