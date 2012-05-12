<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Dungeon_Index extends Abstract_View_Page {

	public $dungeon_data;
	
	public function dungeon_list()
	{
		foreach ($this->dungeon_data as $dungeon)
		{
			$out[] = array(
				'name'       => $dungeon->name,
				'edit_url'   => Route::url('dungeon', array('action' => 'edit',   'id' => $dungeon->id)),
				'remove_url' => Route::url('dungeon', array('action' => 'remove', 'id' => $dungeon->id)),
			);
		}
		
		return $out;
	}

	public function dungeon_add_url()
	{
		return Route::url('dungeon', array('action' => 'add'));
	}
}