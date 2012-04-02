<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Admin role management gui
 */
class Controller_Admin_Role extends Abstract_Controller_Admin {
	
	public function action_index(){}
	
	public function action_edit()
	{
		if ( ! $this->user->can('admin_edit_role'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.role.edit.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'role', )));
		}
		
		$role = ORM::factory('role')->where('name', '=', $this->request->param('name'))->find();
		
		if ($this->valid_post())
		{
			// Get relevant data from $_POST
			$role_post = Arr::get($this->request->post(), 'role', array());
			
			// Set new values to the Model_Role
			$role->values($role_post, array('name', 'description'));
			
			$role->save();
		}
		
		// User notification
		Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.role.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		// Pass the role object to the view
		$this->view->roles = $role;
	}
	
	public function action_remove()
	{
		if ( ! $this->user->can('admin_edit_role'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.role.remove.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'role', )));
		}
		
		// Get the role we're going to remove
		$role = ORM::factory('role', array('name' => $this->request->param('name')));
		
		// This line is self-commenting
		$role->delete();

		// User notification
		Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.role.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

		// Back to the dashboard with you
		$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'dashboard')));
	}
	
	public function action_create()
	{
		if ( ! $this->user->can('admin_edit_role'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.role.create.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'role', )));
		}
		
		if ($this->valid_post())
		{
			// Submitted data
			$role_post = Arr::get($this->request->post(), 'role', array());

			// Create the new role
			$role = ORM::factory('role')->values($role_post, array('name', 'description'))->create();
		
			// User notification
			Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.role.create.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

			// Back to the dashboard
			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'dashboard')));
		}
	}
}