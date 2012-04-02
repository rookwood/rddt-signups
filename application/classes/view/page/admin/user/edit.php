<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_User_Edit extends Abstract_View_Admin_Layout {

	public $title = 'Edit user account';
	
	public function action()
	{
		return '/'.Route::get('admin')
			->uri(array(
				'controller' => 'user',
				'action'     => 'edit',
				'name'       => Request::$current->param('name'),
			));
	}
	
	public function roles()
	{		
		$role_list = array();
		
		foreach (ORM::factory('role')->find_all() as $role)
		{
			$role_list[] = array(
				'id'          => $role->id,
				'name'        => $role->name,
				'description' => $role->description,
				'owned'       => $this->user->is_a($role) ? TRUE : FALSE,
				'role_stripe' => Text::alternate('even', 'odd'),
			);
		}
		
		return $role_list;
	}	
}