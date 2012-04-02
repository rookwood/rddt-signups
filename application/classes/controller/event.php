<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Event extends Abstract_Controller_Website {

	public function action_index()
	{
		// Retreive all future events and ones that started in the last hour
		$events = ORM::factory('event')
			->where('time', '<', strftime('%F', time() - Date::HOUR))
			->find_all()
			->as_array();
			
		// Pass events to the view class
		$this->view->events = $events;
	}
	
	public function action_display()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user view this event?
		if ( ! $this->user->can('event_view', array('event' => $event)))
		{
				// Error notification
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.view.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
		}
		
		// Pass event data to the view class
		$this->view->event = $event;
	}
	
	public function action_add()
	{
		// Can this user add new events?
		if ( ! $this->user->can('event_add'))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf
		if ($this->valid_post())
		{
			// Extract event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			// Create new event object
			$event = ORM::factory('event')->create_event($this->user, $event_post, array(
				'time', 'dungeon_id', 'description', 'status'
			));
			
			// Notification
			Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'event.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
	}
	
	public function action_remove()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user remove this event?
		if ( ! $this->user->can('event_remove', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Remove
		$event->delete();
		
		Notices::add('success', 'msg_info', array('message' => Kohana::message('event.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		
		$this->request->redirect(Route::url('event'));
	}
	
	public function action_edit()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user edit this event's details?
		if ( ! $this->user->can('event_edit', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf, etc.
		if ($this->valid_post())
		{
			// Get event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			// Save data to event object
			$event->values($event_post);
			$character->save();
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'event.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		
		// Pass event object to the view class
		$this->view->character = $event;
		
	}
	
	public function action_signup()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		// Can user sign-up for this event?
		if ( ! $this->user->can('event_signup', array('event' => $event)))
		{
			// Error notification
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.signup.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		// Valid csrf, etc
		if ($this->valid_post())
		{
			// Extract event data from $_POST
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			// Load character object
			$character = ORM::factory('character', array('name' => $event_post['name']));
			
			if ( ! $character->loaded())
				thow new Exception('Charcter not found.');
			
			// Add user to event
			$event->add('character', $character);
			
			// Load sign-up (pivot) object to fill in details
			$signup = ORM::factory('signup', array('event_id' => $event->id, 'character_id' => $character->id));
			
			// User signing-up as active or standby?
			$signup->status  = ($event_post['status'] === 'standby') ? 'standby' : 'ready';
			
			// TODO add purifier here
			$signup->comment = $event_post['comment'];
			
			// Notification of sign-up
			Notices::add('success', 'msg_success', array('message' => Kohana::message('koreg', 'event.signup.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect(Route::url('event', array('action' => 'display', 'id' => $event->id)));
		}
		else
		{
			// Not a valid post (came to this url directly or bad )
			$this->request->redirect(Route::url('event'));
		}
	
	}

}