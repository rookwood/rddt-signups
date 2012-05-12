<?php defined('SYSPATH') or die('No direct script access.');

class View_Page_Build_Index extends Abstract_View_Page {

	public $build_data;
	
	public function build_list()
	{
		foreach ($this->build_data as $build)
		{
			$out[] = array(
				'name'       => $build->name,
				'url'        => $build->url,
				'edit_url'   => Route::url('build', array('action' => 'edit', 'id' => $build->id)),
				'create_url' => Route::url('event', array('action' => 'add')) . URL::query(array('build' => $build->id))
			);
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function build_add_link()
	{
		return Route::url('build', array('action' => 'add'));
	}

}