<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Display extends Abstract_View_Page {

	/**
	 * @var   object  Event that will be displayed
	 */
	public $event_data;

	/**
	 * Creates link to sign up for this event
	 *
	 * @return  string  URL
	 */
	public function sign_up_link()
	{
		return Route::url('event', array('action' => 'signup', 'id' => $this->event_data->id));
	}
	
	/**
	 * Data to be displayed about this event
	 *
	 * @return  array  Event data
	 */
	public function event()
	{
		$event = $this->event_data;
		
		// Calculate start time using user's time offset from GMT
		$local_start_time = Date::offset($this->user->timezone, 'Europe/London') + $event->time;
		
		// Event leader data
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
	
	/**
	 * Data to be displayed about all event attendees
	 *
	 * @return  mixed  Multi-dimensonal array with attendees grouped as active or standby (if 1+ attendees present) or FALSE if empty
	 */
	public function attendees()
	{
		// Cache results as this function causes a lot of database hits
		static $attendee_list;
		
		// Return cached results if available
		if ( ! empty($attendee_list))
			return $attendee_list;
		
		// Statuses needed to test against
		$ready = ORM::factory('status', array('name' => 'ready'))->id;
		$standby_voluntary = ORM::factory('status', array('name' => 'stand-by (voluntary)'))->id;
		$standby_forced    = ORM::factory('status', array('name' => 'stand-by (forced)'))->id;
		
		// Load all characters signed-up for the event
		$attendees = $this->event_data->characters->where('status_id', '=', $ready)->or_where('status_id', '=', $standby_voluntary)->or_where('status_id', '=', $standby_forced)->find_all();
		
		// Iterate through each attendee and pass their data to output
		foreach ($attendees as $character)
		{
			// Load character's sign-up record
			$signup = ORM::factory('signup', array('character_id' => $character->id, 'event_id' => $this->event_data->id));
			
			// Active attendees
			if ($signup->status_id === $ready)
			{
				$out['active'][] = array(
					'profession' => $character->profession->name,
					'name'       => $character->name,
					'role'       => $signup->slot->name,
					'comment'    => $signup->comment,
				);
			}
			// Stand-by attendees (don't care if voluntary or forced stand-by)
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
		
		// If no attendees yet, use 'no signup' message, also caches attendee list
		return isset($out) ? $attendees = $out : FALSE;
	}
	
	/**
	 * List of user's characters
	 *
	 * @return  mixed  Array of characters if any present, FALSE if empty
	 */
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
	
	/**
	 * URL to edit this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */
	public function edit_event()
	{
		if ($this->user->can('event_edit', array('event' => $this->event_data)))
		{
			return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
		}
		return FALSE;
	}
	
	/**
	 * URL to cancel this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */	
	public function remove_event()
	{
		if ($this->user->can('event_remove', array('event' =>$this->event_data)))
		{
			return Route::url('event', array('action' => 'remove', 'id' => $this->event_data->id));
		}
	}
	
	/**
	 * URL to withdraw from this event
	 *
	 * @return  mixed  URL if allowed, FALSE if not
	 */	
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
				
		$characters =  $this->user->characters->find_all();

		// Iterate each role to get name and number of openings remaining
		foreach ($slots as $slot)
		{
			ProfilerToolbar::addData('Testing '.$slot->name, 'slots');
			foreach ($characters as $character)
			{
				ProfilerToolbar::addData('against '.$character->name, 'slots');
				if ($slot->can_use($character))
				{
					ProfilerToolbar::addData($slot->name.' can use '.$character->name, 'slots');
					
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
					break;
				}
			}
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function signup()
	{
		return  TRUE === $this->user->can('event_signup', array('event' => $this->event_data));
	}
}