<?php defined('SYSPATH') or die('No direct script access.');

class View_Email_Registration extends Abstract_View_Email {
	
	public function link()
	{
		$route = Route::get('user')->uri(array('action' => 'check')).
			URL::query(array(
				'action'   => 'registration',
				'username' => $this->user->username,
				'key'      => $this->user->get_key('registration'),
			));
		
		return HTML::anchor($route, URL::base(TRUE, TRUE).$route);
	}
}