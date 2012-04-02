<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Character_Add extends Abstract_View_Page {

	public $errors;
	
	public function action()
	{
		return Route::url('character', array('action' => 'add'));
	}

}