<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_User extends Abstract_Controller_Website {

	/**
	 * View and update the user's information
	 */
	public function action_manage()
	{
		// Does this user have permision to edit their profile?
		if (! $this->user->can('edit_own_profile'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			if ($status === Policy_Edit_Own_Profile::NOT_LOGGED_IN)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'user.edit.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->session->set('follow_login', $this->request->url());
				$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
			}
			else if ($status === Policy_Edit_Own_Profile::NOT_ALLOWED)
			{
				Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'user.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
		}
		
		// Alias for user and profile
		$user = $this->user;
		$profile = $user->profile;		
		
		// Is the form submitted correctly w/ CSRF token?
		if ($this->valid_post())
		{
			// Extract user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
			
			// Don't let the username change
			unset($user_post['username']);
			
			try 
			{
				// Update all user data
				$user->update_user($user_post, array('email', 'password'));
				
				// Update all profile data
				$profile->values($profile_post);
				$profile->save();
				// User notification
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'user.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
	
	/**
	 * User registration page and form processing
	 */
	public function action_register()
	{		
		if ( ! $this->user->can('register'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;

			if ($status === Policy_Register::REGISTRATION_COMPLETED)
			{
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration.completed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
			else if ($status === Policy_Register::REGISTRATION_CLOSED)
			{
				Notices::add('denied', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
		}
		
		// If the form is submitted via POST and the CSRF token is valid
		if ($this->valid_post())
		{
			// Extract the user data from $_POST
			$user_post    = Arr::get($this->request->post(), 'user',    array());
			$profile_post = Arr::get($this->request->post(), 'profile', array());
				
			if ($user_post['password'] === $user_post['password_confirm'])
			{
				try 
				{
					// Create our user
					$user = ORM::factory('user')->create_user($user_post, array('username', 'email', 'password'));
					
					// Add the 'login' role; without this new users will be unable to log in.
					$user->add('roles', ORM::factory('role')->where('name', '=', 'login')->find());
					
					// Create the user's profile
					$profile = ORM::factory('profile')->create_profile($user, $profile_post, array('first_name', 'last_name', 'occupation', 'favorite_game', 'birthdate'));
					
					// Creation complete, log in the user
					$login = Auth::instance()->login($user_post['username'], $user_post['password'], FALSE);
					
					// Check if email verification is required to complete registration
					$config = Kohana::$config->load('registration');
					
					if ($config->get('require_email_verification'))
					{
						$this->request->redirect(Route::url('email registration'));
					}
					else
					{
						// No email verification required
						$user->add_role('verified_user');
						
						// Redirect to the main page
						$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
					}
					
				}
				catch (ORM_Validation_Exception $e)
				{
					// User notification
					Notices::add('error', 'msg_error', array('message' => Kohana::message('koreg', 'generic.validation'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
					
					$this->view->errors = $e->errors('validation');
					
					// We have no valid Model_User, so we have to pass the form values back directly
					$this->view->values = Arr::merge($user_post, $profile_post);
				}
			}
			else 
			{
				// User notification
				Notices::add('error', 'msg_error', array('message' => Kohana::message('koreg', 'user.registration.password'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				// We have no valid Model_User, so we have to pass the form values back directly
				$this->view->values = Arr::merge($user_post, $profile_post);
				
			}
		}
		else if ($this->request->method() == HTTP_Request::POST)
		{
			// User notification
			Notices::add('error', 'msg_error', array('message' => Kohana::message('koreg', 'user.registration.invalid_post'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
	}
	
	/**
	 * Log-in page and form processing
	 */
	public function action_login()
	{
		if ( ! $this->user->can('login'))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// This should be the only reason one cannot attempt a login
			if ($status === Policy_Login::LOGGED_IN)
			{			
				Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'user.login.already_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome',)));
			}
		}
	
		if ($this->valid_post())
		{
			// Extract the user data from $_POST
			$user_post = Arr::get($this->request->post(), 'user', array());
			$remember = array_key_exists('remember', $this->request->post()) ? (bool) $this->request->post('remember') : FALSE;
			
			// Try to log in
			$user = Auth::instance()->login($user_post['username'], $user_post['password'], $remember);

			if ($user)
			{
				// Check to see if we had saved a user destination before forcing login
				$follow_login = $this->session->get_once('follow_login', FALSE);
				
				if ($follow_login)
				{
					$this->request->redirect($follow_login);
				}
				else
				{
					$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
				}
			}
			else
			{
			// User notification
			Notices::add('error', 'msg_error', array('message' => Kohana::message('koreg', 'user.login.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
		}
	}
	
	/**
	 * User log-out
	 */
	public function action_logout()
	{
		// Do the logout - destroys session, etc
		Auth::instance()->logout();
		
		// User notification
		Notices::add('info', 'msg_error', array('message' => Kohana::message('koreg', 'user.logout.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		// Redirect
		$this->request->redirect(Route::url('user', array('controller' => 'user', 'action' => 'login')));
	}

	/**
	 * Notification that the user must verify their email address
	 * Users will be automatically redirected here by any controller extended Abstract_Controller_Verefied
	 */
	public function action_jail(){}

	/**
	 * Sends registration email to the current user
	 */
	public function action_email()
	{
		// Redirect if not logged in
		if ( ! Auth::instance()->logged_in())
		{
			$this->request->redirect(Route::url('user', array('action' => 'login')));
		}
		
		// Make sure this user needs / can receive registration email
		if ($this->user->can('get_registration_email'))
		{			
			// Set up values needed by Swift Mailer
			$email = Email::factory(Kohana::message('koreg', 'user.registration_email.subject'), NULL)
				->message
				(
					// Using Kostache view for our message body
					Kostache::factory('email/registration')
						->set('user', $this->user)
						->set('key', $this->user->get_key('registration')),
					// MIME type
					'text/html'
				)
				->to($this->user->email)
				->from(Kohana::message('koreg', 'user.registration_email.sender'))
				->send();
		}
		else
		{
			// Check why email registration verification is denied
			if (Policy::$last_code === Policy_Get_Registration_Email::REGISTRATION_COMPLETED)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration_email.completed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
			else if (Policy::$last_code === Policy_Get_Registration_Email::ACCOUNT_DEACTIVATED)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration_email.banned'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
			else if (Policy::$last_code === Policy_Get_Registration_Email::NOT_REQUIRED)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration_email.not_required'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

				$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
			}
			else
			{
				throw new HTTP_Exception_404;
			}
		}
	}
	
	/**
	 * Checks that registration key submitted matches the one we created for our user
	 */
	public function action_check()
	{
		// Relevant info from query string
		$check_key  = Arr::get($this->request->query(), 'key',      FALSE);
		$check_user = Arr::get($this->request->query(), 'username', FALSE);
		$action     = Arr::get($this->request->query(), 'action',  'registration');
		
		// Find our user
		$user = ORM::factory('user', array('username' => $check_user));
				
		// Compare keys
		if ($check_key === $user->get_key($action))
		{
			// Keys match, what did we just verify?
			switch($action)
			{
				case 'registration':
					// Set user verified flag
					$user->add_role('verified_user');
					
					Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration_email.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

					break;
				
				case 'reset_password':
					// Reset password to random string
					$new_pw = $user->reset_password();
					
					Notices::add('success', 'msg_info', array('message' => __(Kohana::message('koreg', 'user.password_email.reset'), array(':password' => $new_pw)), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

					break;
				
				// Add more cases here as needed
				
				default:
					throw new HTTP_Exception_404('Invalid check case.');
			}

			$this->request->redirect(Route::url('default', array('controller' => 'welcome', 'action' => 'index')));
		}
		else
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'user.registration_email.bad_key'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));

			$this->request->redirect(Route::url('default', array('controller' => 'welcome')));
		}
	}

	/**
	 * Sends email for password reset
	 */
	public function action_lostpw()
	{
		if ( ! $this->user->can('get_lost_password'))
		{
			throw new HTTP_Exception_404('Policy failure');
		}
		
		if ($this->valid_post())
		{
			$this->user = ORM::factory('user')->where('email', '=', $this->request->post('email'))->find();
			
			// If no user found for provided email address
			if ( ! $this->user->loaded())
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'user.password_email.not_found'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				// Build the email
				$email = Email::factory(Kohana::message('koreg', 'user.password_email.subject'), NULL)
					->message
					(
						// Using Kostache view for our message body
						Kostache::factory('email/password')
							->set('user', $this->user),
						// MIME type
						'text/html'
					)
					->to($this->user->email)
					->from(Kohana::message('koreg', 'user.username_email.sender'))
					->send();
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'user.password_email.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
		}

		// Pass user object to the view
		$this->view->user = $this->user;
	}
	
	/**
	 * Sends email with lost username
	 */
	public function action_lostname()
	{
		if ( ! $this->user->can('get_lost_username'))
		{
			throw new HTTP_Exception_404;
		}
		
		if ($this->valid_post())
		{
			$this->user = ORM::factory('user')->where('email', '=', $this->request->post('email'))->find();
			
			// If no user found for provided email address
			if ( ! $this->user->loaded())
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'user.username_email.not_found'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				// Build the email
				$email = Email::factory(Kohana::message('koreg', 'user.username_email.subject'), NULL)
					->message
					(
						// Using Kostache view for our message body
						Kostache::factory('email/username')
							->set('user', $this->user),
						// MIME type
						'text/html'
					)
					->to($this->user->email)
					->from(Kohana::message('koreg', 'user.username_email.sender'))
					->send();
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'user.username_email.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
		}
		
		$this->view->user = $this->user;
	}
	
	public function action_profile()
	{
	
	}
}