<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Build extends Abstract_Controller_Website {

	public function action_index()
	{		
		$this->view->build_data = ORM::factory('build')->where('visibility', '=', 1)->find_all();
	}
	
	public function action_add(){}
	
	public function action_edit(){}
	
	public function action_remove(){}

}