<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Add extends Abstract_View_Page {

	public $errors;
	
	public $values;
	
	public function event_add_action()
	{
		return Route::url('event', array('action' => 'add'));
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
	
	public function dungeon_list()
	{
		$dungeons = ORM::factory('dungeon')->find_all();
		
		foreach ($dungeons as $dungeon)
		{
			if ($dungeon->name === $this->values['dungeon'])
			{
				$out[] = array('value' => $dungeon->name, 'name' => $dungeon->name, 'selected' => TRUE);
			}
			else
			{
				$out[] = array('value' => $dungeon->name, 'name' => $dungeon->name);
			}
			
		}
		
		return $out;
	}
}