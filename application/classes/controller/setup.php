<?php

class Controller_Setup extends Controller {

	public function action_index()
	{
		// Check for presence of admin user
		$user = ORM::factory('user', array('username' => 'admin'));
		
		if ( ! $user->loaded())
		{
			$values = array(
				'username' => 'admin',
				'password' => 'admin',
				'email'    => 'admin@example.com',
			);
			
			$user = ORM::factory('user')->create_user($values, array('username', 'email', 'password'));
			
			$user->update_roles(array('login', 'admin'));
			
			Notices::add('notice', 'msg_info', array('message' => 'User \'admin\' created with password \'admin\'', 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		else
		{
			Notices::add('error', 'msg_info', array('message' => 'Admin user already exists.  Delete the database row if you need to recreate.', 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		
		$this->request->redirect('/');
	}


}