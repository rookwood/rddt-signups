<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Admin user management gui
 */
class Controller_Admin_User extends Abstract_Controller_Admin {
		
	public function action_index(){}
	
	public function action_edit()
	{		
		if ( ! $this->user->can('admin_edit_profile'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.edit.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'user', )));
		}
		
		// Grab the profile
		$user = ORM::factory('user', array('username' => $this->request->param('name')));
		$profile = $user->profile;
		
		if ($this->valid_post())
		{
			// Extract user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
			$role_post    = Arr::get($this->request->post(), 'role',    array());
			
			try
			{
				// Update all user data
				$user->update_user($user_post, array('username', 'email', 'password'));
				
				// Update roles
				$user->update_roles($role_post);
				
				// Update all profile data
				$profile->values($profile_post);
				$profile->save();
				
				// User notification
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			catch (Exception $e)
			{
				// User notification
				Notices::add('error', 'msg_error', array('message' => (string) $e, 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
		}
		
		// Pass our user object to the view for display		
		$this->view->user    = $user;
		$this->view->profile = $profile;
	}
	
	public function action_create()
	{
		if ( ! $this->user->can('admin_create_user'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.create.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'user', )));
		}
		
		
		// If the form is submitted via POST and the CSRF token is valid
		if ($this->valid_post())
		{
			// Extract the user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
			$role_post    = Arr::get($this->request->post(), 'role',    array());
			
			if ($user_post['password'] === $user_post['password_confirm'])
			{
				try 
				{
					// Create our user
					$user = ORM::factory('user')->create_user($user_post, array('username', 'email', 'password'));
					
					// Add roles
					$user->update_roles($role_post);
					
					// Create the user's profile
					$profile = ORM::factory('profile')->create_profile($user, $profile_post, array('first_name', 'last_name', 'occupation', 'favorite_game', 'birthdate'));
					
					// User notification
					Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.create.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
					
				}
				catch (Exception $e)
				{
					// User notification
					Notices::add('error', 'msg_error', array('message' => (string) $e, 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				}
			}
			else 
			{
				// User notification
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.create.password'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
						
			// Redirect back to the dashboard
			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'user')));
		}
		else 
		{
			// Empty user object needed for correct display
			$this->view->user = ORM::factory('user');
		}
	}
	
	public function action_disable()
	{
		if ( ! $this->user->can('admin_edit_profile'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.disable.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'user', )));
		}
	
		// Authorization complete, grab the profile
		$user = ORM::factory('user', array('username' => $this->request->param('name')));
		
		$user->remove('roles', ORM::factory('role', array('name' => 'login')));
			
		// User notification
		Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.disable.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'user')));
	}
	
	public function action_search()
	{
		if ( ! $this->user->can('admin_user_search'))
		{
			Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'admin.user.search.denied'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect('/'.Route::get('admin')->uri(array('controller' => 'user', )));
		}
		
		// Pass search paramaters to the view
		$this->view->search_data = $this->request->query('q');
	}
}