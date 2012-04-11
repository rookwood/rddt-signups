<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Build_Add extends Abstract_View_Page {

	public $build_data;
	
	public function add_action_url()
	{
		return Route::url('build', array('action' => 'add'));
	}
	
	public function slots()
	{
		return ORM::factory('slot')->find_all();
	}
	
	public function slot_add()
	{
		return Route::url('slot', array('action' => 'add'));
	}

}