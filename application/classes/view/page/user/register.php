<?php defined('SYSPATH') or die('No direct access allowed.');class View_Page_User_Register extends Abstract_View_Page {	public $title = 'Registration Form';
	public $errors;		public $values;		public function action()
	{
		return '/'.Route::get('default')
			->uri(array(
				'controller' => 'user',
				'action'     => 'register',
			));
	}}