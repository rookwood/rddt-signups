<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_User_Jail extends Abstract_View_Page {

	public function send_route()
	{
		return '/'.Route::get('email registration')->uri(array('action' => 'send'));
	}
}