<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Dungeon_Add extends Abstract_View_Page {

	public function dungeon_add_url()
	{
		return Route::url('dungeon', array('action' => 'add'));
	}
}