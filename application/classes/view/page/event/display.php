<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Display extends Abstract_View_Page {

	/**
	 * @var   object  Event that will be displayed
	 */
	public $event_data;

	/**
	 * @var  array  Profession list to save on database calls
	 */
	protected $_profession_data = array(
			1	=> 'warrior',
			2	=> 'ranger',
			3	=> 'monk',
			4	=> 'necromancer',
			5	=> 'elementalist',
			6	=> 'mesmer',
			7	=> 'ritualist',
			8	=> 'assassin',
			9	=> 'dervish',
			10	=> 'paragon',
	);

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
			'dungeon'      => $event->dungeon->name,
			'host'         => $host->user->username,
			'hostas'       => $host->name,
			'date'         => date('F d, Y', $local_start_time),
			'time'         => date('g:i A T', $local_start_time),
			'time_full'    => date('c', $local_start_time),
			'build'        => $event->build->name,
			'url'          => $event->build->url,
			'title'        => $event->title,
			'description'  => $event->description,
			'status'       => $event->status->name,
			'event_link'   => Route::url('event').'#'.$event->id,
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
		{
			return $attendee_list;
		}
		
		// Load all characters signed-up for the event
		$attendees = $this->event_data->characters->where('status_id', 'IN', array(Model_Status::READY, Model_Status::STANDBY_VOLUNTARY, Model_Status::STANDBY_FORCED))->find_all();

		// Iterate through each attendee and pass their data to output
		foreach ($attendees as $character)
		{
			// Load character's sign-up record
			$signup = ORM::factory('signup', array('character_id' => $character->id, 'event_id' => $this->event_data->id));
			
			// Active attendees
			if ($signup->status_id == Model_Status::READY)
			{
				$out['active'][] = array(
					'profession' => $this->_profession_data[$character->profession_id],
					'name'       => $character->name,
					'role'       => $signup->slot->name,
					'comment'    => $signup->comment,
				);
			}
			// Stand-by attendees (don't care if voluntary or forced stand-by)
			else
			{
				$out['standby'][] = array(
					'profession' => $this->_profession_data[$character->profession_id],
					'name'       => $character->name,
					'role'       => $signup->slot->name,
					'comment'    => $signup->comment,
				);
			}
		}
		
		// If no attendees yet, use 'no signup' message, also caches attendee list
		return isset($out) ? $attendee_list = $out : FALSE;
	}
	
	/**
	 * List of user's characters
	 *
	 * @return  mixed  Array of characters if any present, FALSE if empty
	 */
	public function characters()
	{
		if (empty($this->characters))
			$this->characters =  $this->user->characters->find_all();
		
		foreach ($this->characters as $character)
		{
			$out[] = array(
				'profession' => $this->_profession_data[$character->profession_id],
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
		// Cached results
		static $role_list;
		
		if ( ! empty($role_list))
			return $role_list;
		
		// Slots needed for this build
		$slots = $this->event_data->build->slots->find_all();
				
		if (empty($this->characters))
			$this->characters =  $this->user->characters->find_all();

		// Iterate each role to get name and number of openings remaining
		foreach ($slots as $slot)
		{
			foreach ($this->characters as $character)
			{
				if ($slot->can_use($character))
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
					break;
				}
			}
		}
		
		return isset($out) ? $role_list = $out : FALSE;
	}
	
	/**
	 * Tests if current user can see sign-up form
	 *
	 * @return  bool
	 */
	public function signup()
	{
		return  TRUE === $this->user->can('event_signup', array('event' => $this->event_data));
	}
	
	public function standby_count()
	{
		$data = $this->attendees();
		
		return empty($data['standby']) ? 0 : count($data['standby']);
	}
	
	public function attendee_count()
	{
		$data = $this->attendees();
		
		return empty($data['active']) ? 0 : count($data['active']);
	}
}