<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Build_Edit extends View_Page_Build_Add {

	public function edit_action_url()
	{
		return Route::url('build', array('action' => 'edit', 'id' => $this->build_data->id));
	}


}