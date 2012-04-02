<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_Role_Index extends View_Page_Admin_Dashboard_Index {

	public $title = 'Role administration';

	/**
	 * Gets data on all roles for display in table format
	 */	
	/*public function roles()
	{
		$role_list = array();
		
		foreach (ORM::factory('role')->find_all() as $role)
		{
			$role_list[] = array(
				'id'          => $role->id,
				'name'        => $role->name,
				'description' => $role->description,
				'role_stripe' => Text::alternate('even', 'odd'),
				'edit_route'  => '/'.Route::get('admin')->uri(array('controller' => 'role', 'action' => 'edit', 'name' => $role->name)),
				'remove_route' => '/'.Route::get('admin')->uri(array('controller' => 'role', 'action' =>'remove', 'name' => $role->name)),
			);
		}
		
		return $role_list;
	}*/
}