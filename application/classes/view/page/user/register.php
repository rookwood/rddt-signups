<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_User_Register extends Abstract_View_Page {

	public $title = 'Registration Form';

	/**
	 * @var   array   List of validation errors (if any)
	 */
	public $errors;
	
	/**
	 * @var   array   List of form values to be prefilled (if any)
	 */
	public $values;
	
	/**
	 * @return  string  Form action URL
	 */
	public function action()
	{
		return Route::url('default', array('controller' => 'user', 'action' => 'register'));
	}
	
	/**
	 * @return  array   Formatted list of timezones for use in <select>
	 */
	public function timezone_list()
	{
		$current_timezone = $this->user->timezone;
		
		foreach (Date::$timezone_list as $value => $name)
		{
			
			if ($value == $current_timezone)
			{
				$out[] = array('value' => $value, 'name' => $name, 'selected' => TRUE);
			}
			else
			{
				$out[] = array('value' => $value, 'name' => $name);
			}
		}
		
		return $out;		
	}
}