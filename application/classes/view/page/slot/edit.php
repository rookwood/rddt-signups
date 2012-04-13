<?php defined('SYSPATH') or die('No direct script access');

class View_Page_Slot_Edit extends View_Page_Slot_Add {

	public function slot_list()
	{
		foreach ($this->slot_data as $slot)
		{
			$out[] = array('name' => $slot->name);
		}
		
		return $out;
	}
	
	public function slot_add_url()
	{
		return Route::url('slot', array('action' => 'add'));
	}
	
	public function professions()
	{
		foreach (Model_Profession::profession_list() as $profession)
		{
			ProfilerToolbar::addData($profession, 'professions');
			$out[] = array(
				'name' => $profession,
				'checked' => ($this->slot_data->has('professions', ORM::factory('profession', array('name' => $profession)))) ? TRUE : FALSE);
		}
		
		return $out;
	}
}