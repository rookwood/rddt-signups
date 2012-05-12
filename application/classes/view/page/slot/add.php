<?php defined('SYSPATH') or die('No direct script access');

class View_Page_Slot_Add extends Abstract_View_Page {

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
		return Model_Profession::profession_list();
	}
}