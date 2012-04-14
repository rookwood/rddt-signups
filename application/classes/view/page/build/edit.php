<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Build_Edit extends View_Page_Build_Add {

	public function edit_action_url()
	{
		return Route::url('build', array('action' => 'edit', 'id' => $this->build_data->id));
	}

	public function slots()
	{
		foreach (ORM::factory('slot')->order_by('name', 'ASC')->find_all() as $slot)
		{
			$function = ORM::factory('function', array('slot_id' => $slot->id, 'build_id' => $this->build_data->id));

			$out[] = array('name' => $slot->name, 'quantity' => ($function->loaded()) ? $function->number : FALSE);
		}
		
		return $out;
	}
	
	public function removal()
	{
		if ( ! $this->user->can('build_remove', array('build' => $this->build_data)))
		{
			return FALSE;
		}
		
		return Route::url('build', array('action' => 'remove', 'id' => $this->build_data->id));
	}
}