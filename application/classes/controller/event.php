<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Event extends Abstract_Controller_Website {

	public function action_index()
	{
		$filter = Arr::get($this->request->query(), 'filter', 'default');
		$id     = Arr::get($this->request->query(), 'id',     FALSE);
			
		if ($filter == 'mine' AND ! Auth::instance()->logged_in())
		{
			$this->request->redirect(Route::url('user', array('action' => 'login')));
		}
		
		// Pass events to the view class
		$this->view->event_data = Model_Event::event_list($filter, $this->user, $id);
		$this->view->filter_message = Kohana::message('gw', 'filter.'.$filter);
	}
	
	public function action_display()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
	
		// Can user view this event?
		if ( ! $this->user->can('event_view', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.view.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Pass event data to the view class
		$this->view->event_data = $event;
		$this->view->user = $this->user;
	}
	
	public function action_add()
	{
		// Can this user add new events?
		if ( ! $this->user->can('event_add'))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf
		if ($this->valid_post())
		{
			// Extract event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			try
			{
				// Create new event object
				$event = ORM::factory('event')->create_event($this->user, $event_post, array(
					'time', 'dungeon_id', 'description', 'status_id', 'user_id', 'title', 'build_id', 'url', 'character_id', 'user_id'
				));
				
				// Notification
				Notices::add('info', 'msg_info', array('message' => Kohana::message('gw', 'event.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				// Setup display of created event
				$this->view = Kostache::factory('page/event/display')
					->assets(Assets::factory())
					->set('event_data', $event);
			}
			catch(ORM_Validation_Exception $e)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.add.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				$this->view->errors = $e->errors('event');
				$this->view->values = $event_post;
			}
		}
		else
		{
			// Was build submitted via query?
			$build = $this->request->query('build');
			
			if ($build)
				$this->view->build_id = $build;
		}
	}
	
	public function action_edit()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $event->loaded())
			throw new HTTP_Exception_404;
		
		// Can user edit this event's details?
		if ( ! $this->user->can('event_edit', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf, etc.
		if ($this->valid_post())
		{
			// Get event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			try
			{
				// Save data to event object
				$event->edit_event($this->user, $event_post, array(
						'time', 'dungeon_id', 'description', 'status_id', 'user_id', 'title', 'build_id',
				));
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'event.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				// Setup display of edited event
				$this->view = Kostache::factory('page/event/display')
					->assets(Assets::factory());
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->view->errors = $e->errors('event');
				$this->view->values = $event_post;
			}
		}
		else
		{
			$this->view->values = $event->as_array();
		}
		
		// Pass event object to the view class
		$this->view->event_data = $event;
		
	}
	
	public function action_remove()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user remove this event?
		if ( ! $this->user->can('event_remove', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Cancel the event (will be hidden from view)
		$event->status_id =  Model_Status::CANCELLED;
		$event->save();
		
		Notices::add('success', 'msg_info', array('message' => Kohana::message('event.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		// Show event list
		$this->request->redirect(Route::url('event'));
	}
	
	public function action_signup()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user sign-up for this event?
		if ( ! $this->user->can('event_signup', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.signup.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf, etc
		if ($this->valid_post())
		{
			// Extract event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			// Load character object
			$character = ORM::factory('character', array('name' => $event_post['character']));
			
			if ( ! $character->loaded())
				throw new Exception('Charcter not found.');
			
			// Add user to event
			try
			{
				$event->add('characters', $character);
			}
			catch(Database_Exception $e)
			{
				// Duplicate entry, which means user was signed up, but cancelled. We can safely move on from here with no intervention.
			}
			
			// Load sign-up (pivot) object to fill in details
			$signup = ORM::factory('signup', array('event_id' => $event->id, 'character_id' => $character->id));
			
			if ( ! $signup->loaded())
			{
				throw new Exception('failed to load signup record');
			}
			
			// Load slot to test if available
			$slot = ORM::factory('slot', array('name' => $event_post['slot']));
			
			// If slots are full, you have to sign-up as standby
			if ( ! $this->user->can('event_signup_active', array('event' => $event, 'slot' => $slot)))
			{
				$policy_status = Policy::$last_code;
				
				if ($policy_status === Policy_Event_Signup_Active::STANDBY_ONLY)
				{
					$signup_status = ORM::factory('status', Model_Status::STANDBY_FORCED);
					Notices::add('warning', 'msg_warning', array('message' => Kohana::message('gw', 'event.signup.standby_forced'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				}
			}
			else
			{
				// If slots available, allow user preference
				$signup_status = ORM::factory('status', array('name' => $event_post['status']));
			}
			
			// Ensure that valid status was given
			if ( ! $signup_status->loaded())
			{
				// Default to forced stand-by on error
				$signup_status = Model_Status::STANDBY_FORCED;
			}
			else
			{
				$signup_status = $signup_status->id;
			}
			
			// Set sign-up status
			$signup->status_id  = $signup_status;
			
			// Get slot info
			$slot = ORM::factory('slot', array('name' => $event_post['slot']));
			$signup->slot_id = $slot->id;
			
			// TODO add purifier here
			$signup->comment = $event_post['comment'];
			
			$signup->save();
			
			// Notification of sign-up
			Notices::add('success', 'msg_success', array('message' => Kohana::message('gw', 'event.signup.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			// Setup display of event
			$this->view = Kostache::factory('page/event/display')
					->assets(Assets::factory())
					->set('event_data', $event);
		}
		else
		{
			// Not a valid post (came to this url directly or bad )
			Notices::add('error', 'msg_success', array('message' => Kohana::message('gw', 'event.signup.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
	}

	public function action_withdraw()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Get all of the user's characters that might have signed up
		$characters = $this->user->characters->find_all();
		
		if ( ! $this->user->can('event_withdraw', array('event' => $event)))
		{
			// Not allowed, get the reason why
			$status = Policy::$last_code;
			
			// User wasn't actually signed-up for this event
			if ($status === Policy_Event_Withdraw::NOT_SIGNED_UP)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.withdraw.not_signed_up'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
			}
			// Tried to cancel after event had started
			elseif ($status === Policy_Event_Withdraw::START_TIME_PASSED)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.withdraw.start_time_passed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
			}
			// Unspecified policy failure... this shouldn't really happen
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.withdraw.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
			}
		}
		// User may cancel - now we have to find where they signed-up and remove it
		else
		{
			// Build array of character IDs
			foreach ($characters as $character)
			{
				$ids[] = $character->id;
			}
			
			// Get sign-up records with any of the user's charcter IDs
			$signup = ORM::factory('signup')
				->where('character_id', 'IN', $ids)
				->find_all();
			
			// Cancellation status
			$cancelled = Model_Status::CANCELLED;
			
			try
			{
				// Change sign-up status to cancelled
				foreach ($signup as $signup)
				{
					$signup->status_id = $cancelled;
					$signup->save();
				}
				
				// Bump someone from forced stand-by list up to this slot
				$standby_forced = Model_Status::STANDBY_FORCED;
				$ready = Model_Status::READY;
				
				$bump = ORM::factory('signup')->where('event_id', '=', $event->id)->and_where('status_id', '=', $standby_forced)->order_by('timestamp', 'DESC')->find(1);
				if ($bump->loaded())
				{
					$bump->status_id = $ready;
					$bump->save();
				}
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'event.withdraw.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			// Something bad happened... log it for fixing
			catch(Exception $e)
			{
				throw new HTTP_Exception_500;
			}
		}
		$this->view = Kostache::factory('page/event/display')
			->assets(Assets::factory());
		
		$this->view->event_data = $event;
		$this->view->user = $this->user;
	}
}