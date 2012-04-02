<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Character extends Abstract_Controller_Website {

	public function action_index()
	{
		$characters = ORM::factory('character')->where('user_id', '=', $this->user->id)->find_all()->as_array();
		
		$this->view->characters = (count($characters) !== 0) ? $characters : FALSE;
	}
	
	public function action_add()
	{
		if ( ! $this->user->can('character_add'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			if ($status === Policy_Add_Character::NOT_LOGGED_IN)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'character.add.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->session->set('follow_login', $this->request->url());
				$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
			}
			else if ($status === Policy_Add_Character::NOT_ALLOWED)
			{
				Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'character.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
		}

		// Alias for user and profile
		$user = $this->user;
		$profile = $user->profile;		
		
		// Is the form submitted correctly w/ CSRF token?
		if ($this->valid_post())
		{
			// Submitted data
			$character_post = Arr::get($this->request->post(), 'charactrer', array());
						
			// Create the character
			$character = ORM::factory('character')->create_character($user, $character_post, array('name', 'profession'));
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('character.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect(Route::url('character');
		}
	}
	
	public function action_remove()
	{
		// Load character model
		$character = ORM::factory('character', array('name' => $this->request->param('character')));
		
		if ( ! $this->user->can('character_remove', array('character' => $character)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			if ($status === Policy_Remove_Character::NOT_ALLOWED)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'character.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('character');
			}
		}
				
		// Remove
		$character->delete();
		
		Notices::add('success', 'msg_info', array('message' => Kohana::message('character.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		$this->request->redirect(Route::url('character'));
	}
	
	public function action_edit()
	{
		$character = ORM::factory('character', array('name' => $this->request->param('character')));
		
		if ( ! $this->user->can('character_edit', array('character' => $character)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			if ($status === Policy_Edit_Character::NOT_ALLOWED)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'character.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('character');
			}
		}
		
		if ($this->valid_post())
		{
			$character_post = Arr::get($this->request->post(), 'character', array());
			
			$character->values($character_post);
			$character->save();
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'character.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		
		$this->view->character = $character;
	}
}