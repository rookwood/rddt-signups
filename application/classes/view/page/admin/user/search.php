<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Admin_User_Search extends Abstract_View_Admin_Layout {

	public $title = 'Search results';
	
	protected $_template = 'page/admin/user/index';
	
	public $show_roles = FALSE;
	
	public function users()
	{
		$user_list = array();
		
		// Get the users
		foreach (Model_User::search($this->search_data) as $user)
		{			
			// Populate the array with any data we want to display
			$user_list[] = array(
				'user_stripe'      => Text::alternate('even', 'odd'),
				'id'               => $user['id'],
				'username'         => $user['username'],
				'name'             => $user['first_name'].' '.$user['last_name'],
				'email'            => $user['email'],
				'occupation'       => $user['occupation'],
				// turn our $roles array into a comma-separated list
				'roles'            => '',
				'edit_route'       => '/'.Route::get('admin')->uri(array('controller' => 'user', 'action' => 'edit', 'name' => $user['username'])),
				'deactivate_route' => '/'.Route::get('admin')->uri(array('controller' => 'user', 'action' =>'disable', 'name' => $user['username'])),
			);
		}
		return $user_list;
	}
}