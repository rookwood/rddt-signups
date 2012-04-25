<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Edit extends View_Page_Event_Add {

	public $event_data;
	
	public function event_edit_action()
	{
		return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
	}
	
	
	public function scheduled_time()
	{
		return date('g:i a', $this->event_data->time + Date::offset($this->user->timezone, 'Europe/London'));
	}
	
	public function scheduled_date()
	{
		return date('Y-m-d', $this->event_data->time + Date::offset($this->user->timezone, 'Europe/London'));
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

}