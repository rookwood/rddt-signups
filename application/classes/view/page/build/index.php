<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Build_Index extends Abstract_View_Page {

	public $build_data;
	
	public function build_list()
	{
		foreach ($this->build_data as $build)
		{
			$out[] = array('name' => $build->name, 'url' => $build->url, 'edit_link' => Route::url('build', array('action' => 'edit', 'id' => $build->id)));
		}
		
		return $out;
	}
	
	public function add_link()
	{
		return Route::url('build', array('action' => 'add'));
	}

}