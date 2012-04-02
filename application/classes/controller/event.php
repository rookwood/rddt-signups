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
		
		if ( ! $this->user->can('view_event', array('event' => $event)))
		{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.view.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('event'));
		}
		
		$this->view->event = $event;
	}
	
	public function action_add()
	{
		if ( ! $this->user->can('add_event'))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		if ($this->valid_post())
		{
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			$event = ORM::factory('event')->create_event($this->user, $event_post, array(
				'time', 'dungeon_id', 'description', 'status'
			));
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'event.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
	}
	
	public function action_remove()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $this->user->can('remove_event', array('event' => $event)))
		{
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
		
		if ( ! $this->user->can('edit_event', array('event' => $event)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		if ($this->valid_post())
		{
			$event = Arr::get($this->request->post(), 'event', array());
			
			$event->values($event);
			$character->save();
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('koreg', 'event.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		
		$this->view->character = $event;
		
	}
	
	public function action_signup()
	{
		// Load the event object
		$event = ORM::factory('event', array('id' => $this->request->param('id')));
		
		if ( ! $this->user->can('signup_event', array('event' => $event)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('koreg', 'event.signup.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('event'));
		}
		
		if ($this->valid_post())
		{
			$event_post = Arr::get($this->request->post(), 'event', array());
			
			$character = ORM::factory('character', array('name' => $event_post['name']));
			
			if ( ! $character->loaded())
				thow new Exception('Charcter not found.');
			
			// Add user to event
			$event->add('character', $character);
			
			$signup = ORM::factory('signup', array('event_id' => $event->id, 'character_id' => $character->id));
			
			$signup->status  = ($event_post['status'] === 'standby') ? 'standby' : 'ready';
			
			// TODO add purifier here
			$signup->comment = $event_post['comment'];
			
			Notices::add('success', 'msg_success', array('message' => Kohana::message('koreg', 'event.signup.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			$this->request->redirect(Route::url('event', array('action' => 'display', 'id' => $event->id)));
		}
		else
		{
			$this->request->redirect(Route::url('event'));
		}
	
	}

}