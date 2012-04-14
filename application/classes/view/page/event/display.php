<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Display extends Abstract_View_Page {

	/**
	 * @var   object  Event that will be displayed
	 */
	public $event_data;

	public function sign_up_link()
	{
		return Route::url('event', array('action' => 'signup', 'id' => $this->event_data->id));
	}
	
	public function event()
	{
		$event = $this->event_data;
		
		// Calculate start time using user's time offset from GMT
		$local_start_time = Date::offset($this->user->timezone, 'Europe/London') + $event->time;
		
		$host = ORM::factory('character', $event->character_id);
		
		return array(
			'date'         => date('Y M d', $local_start_time),
			'time'         => date('g:i a', $local_start_time),
			'description'  => $event->title,
			'status'       => $event->status->name,
			'host'         => $host->user->username,
			'hostas'       => $host->name,
			'build'        => $event->build->name,
			'url'          => $event->build->url,
		);
	}
	
	public function attendees()
	{
		static $attendee_list;
		
		if ( ! empty($attendee_list))
			return $attendee_list;
		
		$ready = ORM::factory('status', array('name' => 'ready'))->id;
		$standby_voluntary = ORM::factory('status', array('name' => 'stand-by (voluntary)'))->id;
		$standby_forced    = ORM::factory('status', array('name' => 'stand-by (forced)'))->id;
		
		// Load all characters signed-up for the event
		$attendees = $this->event_data->characters->where('status_id', '=', $ready)->or_where('status_id', '=', $standby_voluntary)->or_where('status_id', '=', $standby_forced)->find_all();
		
		foreach ($attendees as $character)
		{
			// Load character's sign-up record
			$signup = ORM::factory('signup', array('character_id' => $character->id, 'event_id' => $this->event_data->id));
			
			if ($signup->status_id === $ready)
			{
				$out['active'][] = array(
					'profession' => $character->profession->name,
					'name'       => $character->name,
					'role'       => $signup->slot->name,
					'comment'    => $signup->comment,
				);
			}
			else
			{
				$out['standby'][] = array(
					'profession' => $character->profession->name,
					'name'       => $character->name,
					'role'       => $signup->slot->name,
					'comment'    => $signup->comment,
				);
			}
		}
		
		// If no attendees yet, use 'no signup' message
		return isset($out) ? $attendees = $out : FALSE;
	}
	
	public function characters()
	{
		$characters =  $this->user->characters->find_all();
		
		foreach ($characters as $character)
		{
			$out[] = array(
				'profession' => $character->profession->name,
				'name'       => $character->name,
			);
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function edit_event()
	{
		if ($this->user->can('event_edit', array('event' => $this->event_data)))
		{
			return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
		}
		return FALSE;
	}
	
	public function remove_event()
	{
		if ($this->user->can('event_remove', array('event' =>$this->event_data)))
		{
			return Route::url('event', array('action' => 'remove', 'id' => $this->event_data->id));
		}
	}
	
	public function withdraw()
	{
		if ($this->user->can('event_withdraw', array('event' => $this->event_data)))
		{
			return Route::url('event', array('action' => 'withdraw', 'id' => $this->event_data->id));
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Return a list of roles needed for the build used in this event
	 * Data includes role names and how many of each slot type are open
	 *
	 * @return  array  Role list
	 */
	public function role_list()
	{
		// Slots needed for this build
		$slots = $this->event_data->build->slots->find_all();
				
		// Iterate each role to get name and number of openings remaining
		foreach ($slots as $slot)
		{
			$total = Model_Function::slot_count($this->event_data->build, $slot);
			$available = $total - $slot->slots_filled($this->event_data);
			
			if ($available > 0)
			{
				$out[] = array('name' => $slot->name, 'number' => $available, 'total' => $total);
			}
			else
			{
				$out[] = array('name' => $slot->name, 'number' => FALSE, 'total' => $total);
			}
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function signup()
	{
		return  TRUE === $this->user->can('event_signup', array('event' => $this->event_data));
	}
}