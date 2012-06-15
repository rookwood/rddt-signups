<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_User_Lostpw extends Abstract_View_Page {

	public $title = 'Recover lost password';
	
	public function action()
	{
		return Route::url('user', array('action' => 'lostpw'));
	}
}