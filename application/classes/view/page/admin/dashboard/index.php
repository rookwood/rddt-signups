<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_Dashboard_Index extends Abstract_View_Admin_Layout {

	public $title = 'Admin dashboard';
	
	public $user_table_caption = 'Last 15 registered users';
	
	public $role_table_caption = 'Available roles';
	
	/**
	 * Gets data on all users for display in table format
	 */
	public function users()
	{
		$user_list = array();
		
		// Get the users
		foreach (ORM::factory('user')->order_by('id', 'desc')->limit(15)->find_all() as $user)
		{
			$roles = array();
			
			// Get all of a user's roles (necessary step for any $_has_many relationship)
			foreach ($user->roles->find_all() as $role)
			{
				$roles[] = $role->name;
			}
			
			// Populate the array with any data we want to display
			$user_list[] = array(
				'user_stripe'      => Text::alternate('even', 'odd'),
				'id'               => $user->id,
				'username'         => $user->username,
				'name'             => $user->profile->first_name.' '.$user->profile->last_name,
				'email'            => $user->email,
				'occupation'       => $user->profile->occupation,
				'roles'            => implode(', ', $roles),
				'edit_route'       => '/'.Route::get('admin')->uri(array('controller' => 'user', 'action' => 'edit',    'name' => $user->username)),
				'deactivate_route' => '/'.Route::get('admin')->uri(array('controller' => 'user', 'action' => 'disable', 'name' => $user->username)),
			);
		}
		return $user_list;
	}
	
	/**
	 * Gets data on all roles for display in table format
	 */	
	public function roles()
	{
		$role_list = array();
		
		foreach (ORM::factory('role')->find_all() as $role)
		{
			$role_list[] = array(
				'id'           => $role->id,
				'name'         => $role->name,
				'description'  => $role->description,
				'role_stripe'  => Text::alternate('even', 'odd'),
				'edit_route'   => '/'.Route::get('admin')->uri(array('controller' => 'role', 'action' => 'edit', 'name' => $role->name)),
				'remove_route' => '/'.Route::get('admin')->uri(array('controller' => 'role', 'action' =>'remove', 'name' => $role->name)),
			);
		}
		
		return $role_list;
	}
}
