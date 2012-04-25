<?php defined('SYSPATH') or die('No direct script access');

class View_Page_Slot_Index extends Abstract_View_Page {

	public function slot_list()
	{
		foreach ($this->slot_data as $slot)
		{
			$professions = $slot->professions->find_all();
			
			foreach ($professions as $profession)
			{
				$profs_associated[$slot->name][] = $profession->name;
			}
			
			$out[] = array(
				'name' => $slot->name,
				'edit_link' => Route::url('slot', array('action' => 'edit', 'id' => $slot->id)),
				'professions' => (isset($profs_associated[$slot->name])) ? implode(', ', $profs_associated[$slot->name]) : FALSE,
			);
			
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function slot_add_url()
	{
		return Route::url('slot', array('action' => 'add'));
	}

}