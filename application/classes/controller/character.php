<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Character extends Abstract_Controller_Website {

	public function action_index()
	{
		// Load array of this user's character list from the database
		$characters = ORM::factory('character')->where('user_id', '=', $this->user->id)->find_all()->as_array();
		
		// Pass character array to the view class
		if (count($characters) !== 0)
		{
			$this->view->characters = $characters;
			$this->view->count = TRUE;
		}
		
	}
	
	public function action_add()
	{
		// Can user add new characters at this time?
		if ( ! $this->user->can('character_add'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// Must be logged in to add a character
			if ($status === Policy_Character_Add::NOT_LOGGED_IN)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'character.add.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				// Redirect to login screen; come back once finished
				$this->session->set('follow_login', $this->request->url());
				$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
			}
			
			// Unspecified reason for denial
			else if ($status === Policy_Character_Add::NOT_ALLOWED)
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
			$character_post = Arr::get($this->request->post(), 'character', array());
						
			// Create the character
			try
			{
				$character = ORM::factory('character')->create_character($user, $character_post, array('name', 'profession'));
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('character.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				$this->request->redirect(Route::url('character'));
			}
			catch(ORM_Validation_Exception $e)
			{			
				$this->view->errors = $e->errors('character');
				
				// We have no valid Model_Character, so we have to pass the form values back directly
				$this->view->values = $character_post;
			}
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
			
			// Unspecified reason for denial
			if ($status === Policy_Remove_Character::NOT_ALLOWED)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'character.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('character'));
			}
		}
				
		// Remove
		$character->delete();
		
		Notices::add('success', 'msg_info', array('message' => Kohana::message('character.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		$this->request->redirect(Route::url('character'));
	}
	
	public function action_edit()
	{
		// Load character model
		$character = ORM::factory('character', array('name' => $this->request->param('character')));
		
		// Is user allowed to edit this character?
		if ( ! $this->user->can('character_edit', array('character' => $character)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// Unspecified reason for denial
			if ($status === Policy_Edit_Character::NOT_ALLOWED)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'character.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('character'));
			}
		}
		
		// Valid csrf, etc
		if ($this->valid_post())
		{
			// Extract character data from $_POST
			$character_post = Arr::get($this->request->post(), 'character', array());
			
			// Set data to character model and save
			$character->values($character_post);
			$character->save();
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'character.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		
		// Pass character data to view class
		$this->view->character = $character;
	}
}