<?php defined('SYSPATH') or die('No direct script access.');

class View_Email_Username extends Abstract_View_Email {

	public function username()
	{
		return $this->user->username;
	}

}