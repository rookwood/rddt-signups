<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Edit extends Abstract_View_Page {

	public $errors;
	
	public $values;
	
	public $event_data;
	
	public function event_edit_action()
	{
		return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
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
			if ($dungeon->name === $this->event_data->dungeon->name)
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
	
	public function scheduled_time()
	{
		return date('g:i a', $this->event_data->time);
	}
	
	public function scheduled_date()
	{
		return date('Y-m-d', $this->event_data->time);
	}
}