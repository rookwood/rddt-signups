<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Dungeon_Edit extends Abstract_View_Page {

	public function dungeon_edit_url()
	{
		return Route::url('dungeon', array('action' => 'edit', 'id' => $this->dungeon_data->id));
	}
	
	public function name()
	{
		return $this->dungeon_data->name;
	}
}