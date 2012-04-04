<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_User_Manage extends Abstract_View_Page {

	public $title = 'Profile Editor';

	public function action()
	{
		return Route::url('default', array('controller' => 'user', 'action' => 'manage'));
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