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
			$out[] = array('value' => $dungeon->name, 'name' => $dungeon->name);
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function characters()
	{
		$characters =  $this->user->characters->find_all();
		
		foreach ($characters as $character)
		{
			$out[] = array(
				'profession' => $character->profession->name,
				'name'       => $character->name,
			);
		}
		
		return isset($out) ? $out : FALSE;
	}

	public function build_list()
	{
		if ( ! isset($this->build_id))
			$this->build_id = -1;
			
		foreach (ORM::factory('build')->find_all() as $build)
		{
			$out[] = array('name' => $build->name, 'url' => $build->url, 'selected' => ($this->build_id === $build->id) ? TRUE : FALSE);
		}
		
		return isset($out) ? $out : FALSE;
	}
}