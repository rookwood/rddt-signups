<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_User_Lostname extends Abstract_View_Page {

	public $title = 'Recover lost username';
	
	public function action()
	{
		return '/'.Route::get('user')
			->uri(array(
				'action'     => 'lostname',
			));
	}
}