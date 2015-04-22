<?php

class Account extends ActiveRecord\Model {

	static $validates_uniqueness_of = [
		['dropbox_uid'],
		['email']
	],
	$has_many = [
		['folders']
	];

}
