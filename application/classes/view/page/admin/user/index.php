<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_User_Index extends Abstract_View_Admin_Layout {

	public $title = 'User List';

	public function users()
	{
		$user_list = array();
		
		// Get the users
		foreach (ORM::factory('user')->order_by('id', 'desc')->find_all() as $user)
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
				// turn our $roles array into a comma-separated list
				'roles'            => implode(', ', $roles),
				'edit_route'       => '/'.Route::get('admin')->uri(array('controller' => 'user', 'action' => 'edit', 'name' => $user->username)),
				'deactivate_route' => '/'.Route::get('admin')->uri(array('controller' => 'user', 'action' =>'disable', 'name' => $user->username)),
			);
		}
		return $user_list;
	}
}
