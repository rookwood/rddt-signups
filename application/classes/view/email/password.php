<?php defined('SYSPATH') or die('No direct script access.');

class View_Email_Password extends Abstract_View_Email {

	public function link()
	{
		$route = Route::get('user')->uri(array('action' => 'check')).
			URL::query(array(
				'action'   => 'reset_password',
				'username' => $this->user->username,
				'key'      => $this->user->get_key('reset_password'),
			));
		
		return HTML::anchor($route, URL::base(TRUE, TRUE).$route);
	}

}