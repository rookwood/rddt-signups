<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_Role_Create extends Abstract_View_Admin_Layout {

	public $title = 'Create new role';
	
	public $form_name = 'create_role';

	public function action()
	{
		return '/'.Route::get('admin')->uri(array('controller' => 'role', 'action' => 'create'));
	}
}