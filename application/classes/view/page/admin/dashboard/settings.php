<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Admin_Dashboard_Settings extends Abstract_View_Admin_Layout {

	public $title = 'Admin Section Settings';		public function open_registration()	{		$config = Kohana::$config->load('registration');				return $config->get('open_registration');	}
	
	public function require_email_verification()
	{
		$config = Kohana::$config->load('registration');
		
		return $config->get('require_email_verification');
	}
	
	public function email_lost_password()
	{
		$config = Kohana::$config->load('lost_data');
		
		return $config->get('email_lost_password');
	}
	
	public function email_lost_username()
	{
		$config = Kohana::$config->load('lost_data');
		
		return $config->get('email_lost_username');
	}
}