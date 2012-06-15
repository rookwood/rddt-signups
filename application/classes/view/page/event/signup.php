<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Event_Signup extends View_Page_Event_Display {

	public function signup()
	{
		return $this->user->can('event_signup', array('event' => $this->event_data));
	}
}