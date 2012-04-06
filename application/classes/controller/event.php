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
			Notices::add('error', 'msg_info', array('message' => Kohana::message('event', 'event.view.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
			Notices::add('error', 'msg_info', array('message' => Kohana::message('event', 'event.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
					'time', 'dungeon_id', 'description', 'status_id', 'user_id', 'title', 'build', 'url',
				));
				
				// Notification
				Notices::add('success', 'msg_info', array('message' => Kohana::message('event', 'event.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
			}
			catch(ORM_Validation_Exception $e)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('event', 'event.add.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				
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
			Notices::add('error', 'msg_info', array('message' => Kohana::message('event', 'event.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('event', 'event.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
			Notices::add('error', 'msg_info', array('message' => Kohana::message('event', 'event.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
			Notices::add('error', 'msg_info', array('message' => Kohana::message('event', 'event.signup.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
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
			$event->add('characters', $character);
			
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
			
			// TODO add purifier here
			$signup->comment = $event_post['comment'];
			
			$signup->save();
			
			// Notification of sign-up
			Notices::add('success', 'msg_success', array('message' => Kohana::message('event', 'event.signup.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect(Route::url('event', array('action' => 'display', 'id' => $event->id)));
		}
		else
		{
			// Not a valid post (came to this url directly or bad )
			$this->request->redirect(Route::url('event'));
		}
	
	}

}