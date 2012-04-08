<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Event extends Abstract_Controller_Website {

	public function action_index()
	{
		$status = ORM::factory('status', array('name' => 'cancelled'));
		
		// Retreive all future events and ones that started in the last hour
		$events = ORM::factory('event')
			->where('time', '>', strftime('%s', time() - Date::HOUR))
			//->and_where('status_id', '!=', $status->id)
			->order_by('status_id', 'ASC')
			->order_by('time', 'ASC')
			->find_all();
			
		// Pass events to the view class
		$this->view->event_data = $events;
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
					'time', 'dungeon_id', 'description', 'status_id', 'user_id', 'title', 'build', 'url', 'character_id', 'user_id'
				));
				
				// Notification
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'event.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
			}
			catch(ORM_Validation_Exception $e)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'event.add.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
				$this->view->errors = $e->errors('event');
				$this->view->values = $event_post;
			}
		}
	}
	
	public function action_edit()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
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
						'time', 'dungeon_id', 'description', 'status_id', 'user_id', 'title', 'build', 'url',
				));
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'event.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
		$status = ORM::factory('status', array('name' => 'cancelled'));
		$event->status_id = $status->id;
		$event->save();
		
		Notices::add('success', 'msg_info', array('message' => Kohana::message('event.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
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
			
			// User signing-up as active or standby?
			$signup_status = ORM::factory('status', array('name' => $event_post['status']));
			
			if ( ! $signup_status->loaded())
			{
				// Default to stand-by on error
				$signup_status = ORM::factory('status', array('name' => 'stand-by'));
			}
			
			$signup->status_id  = $signup_status->id;
			$signup->role = $event_post['role'];
			
			// TODO add purifier here
			$signup->comment = $event_post['comment'];
			
			$signup->save();
			
			// Notification of sign-up
			Notices::add('success', 'msg_success', array('message' => Kohana::message('gw', 'event.signup.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect(Route::url('event', array('action' => 'display', 'id' => $event->id)));
		}
		else
		{
			// Not a valid post (came to this url directly or bad )
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
			
			ProfilerToolbar::addData($ids, 'IDs');
			
			// Get sign-up records with any of the user's charcter IDs
			$signup = ORM::factory('signup')
				->where('character_id', 'IN', $ids)
				->find_all();
			
			ProfilerToolbar::addData($signup, 'Signup object');
			
			// Cancellation status
			$cancelled = ORM::factory('status', array('name' => 'cancelled'))->id;
			
			try
			{
				// Change sign-up status to cancelled
				foreach ($signup as $signup)
				{
					$signup->status_id = $cancelled;
					$signup->save();
				}
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'event.withdraw.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				//$this->request->redirect(Route::url('event'));			
			}
			// Something bad happened... log it for fixing
			catch(Exception $e)
			{
				die('Exception');exit;
			}
		}
		$this->view = Kostache::factory('page/event/display')
			->assets(Assets::factory());
		
		$this->view->event_data = $event;
		$this->view->user = $this->user;
	}
}