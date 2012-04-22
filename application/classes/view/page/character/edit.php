<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Character_Edit extends Abstract_View_Page {

	public $character_data;
	
	public function edit_action_url()
	{
		return Route::url('character', array('action' => 'edit', 'id' => $this->character_data->id));
	}
	
	public function professions()
	{		
		foreach (Model_Profession::profession_list() as $profession)
		{
			$out[] = array('name' => $profession, 'selected' => ($this->character_data->profession->name === $profession) ? TRUE : FALSE);
		}
		
		return $out;
	}

	public function name()
	{
		return $this->character_data->name;
	}
}