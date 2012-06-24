<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Events model
 */
class Model_Event extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'dungeon' => array(),
		'user'    => array(),
		'status'  => array(),
		'build'   => array(),
		'status'  => array(),
	);
	
	protected $_has_many   = array(
		'signups'    => array('model' => 'signup'),
		'characters' => array(
			'model'    => 'character',
			'through'  => 'signups',
		),
	);
	
	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 50)),
			),
			'time' => array(
				array('not_empty'),
			),
		);
	}
	
	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels()
	{
		return array(
			'time'             => 'Time',
			'title'            => 'Event title',
		);
	}

	public static function event_list($filter, Model_ACL_User $user = NULL, $id = NULL)
	{
		switch ($filter)
		{
			// Show events that the user has ever signed up for.
			case 'mine':
				if ( ! $user)
					throw new Kohana_Exception('Must be logged in to search your events.');
					
				// Build sub queries to join current user -> characters -> signups -> events
				$sub1 = DB::select('characters.id')->from('characters')->join('users')->on('characters.user_id', '=', 'users.id')->where('users.id', '=', $user->id);
				$sub2 = DB::select('signups.event_id')->from('signups')->join(array($sub1, 'characters'))->on('characters.id', '=', 'signups.character_id')->where('signups.status_id', '!=', Model_Status::CANCELLED);
				$sub3 = DB::select('events.id')->from('events')->join(array($sub2, 'signups'))->on('signups.event_id', '=', 'events.id');
				
				// Execute our query
				$events = $sub3->execute();
					
				// Build array of event IDs
				foreach ($events as $event)
				{
					$ids[] = $event['id'];
				}
				
				if (empty($ids))
					return array();
				
				// Pass event object data to the view
				$events = ORM::factory('event')->where('id', 'IN', $ids)->order_by('time', 'ASC')->find_all();
			break;
			
			// Show all events that started before the current time()
			case 'past':
				$events = ORM::factory('event')
					->where('time', '<', time())
					->and_where('status_id', '!=', Model_Status::CANCELLED)
					->order_by('status_id', 'ASC')
					->order_by('time', 'ASC')
					->find_all();
			break;
			
			// Show all events with dungeon id of $_GET['id']
			case 'dungeon':
				$events = ORM::factory('event')
					->where('time', '>', time() - Date::HOUR)
					->and_where('status_id', '!=', Model_Status::CANCELLED)
					->order_by('dungeon_id', 'ASC')
					->find_all();
			break;
			
			// Show all events 
			case 'time':
			// same as current
			default:
				$events = ORM::factory('event')
					->where('time', '>', time() - Date::HOUR)
					->and_where('status_id', '!=', Model_Status::CANCELLED)
					->order_by('status_id', 'ASC')
					->order_by('time', 'ASC')
					->find_all();
			break;
		}
		
		return $events;
	}
	
	/**
	 * Create a new event
	 */
	 public function create_event(Model_ACL_User $user, $values, $expected)
	 {
		// Convert date+time to epoch timestamp
		$time_string = (string) $values['time'] ." ". (string) $values['date'];
		
		$time = strtotime($time_string);
		
		if ($time !== FALSE)
		{
			// Offset timestamp from user's timezone to GMT for storage
			$time = Date::offset('Europe/London', $values['timezone']) + $time;
		}
		
		// Convert dungeon name to id
		$dungeon = ORM::factory('dungeon', array('name' => $values['dungeon']));
		
		// Convert status name to id
		$status = Model_Status::SCHEDULED;
		
		// Get character id
		$character = ORM::factory('character', array('name' => $values['character']));
		
		// Get build id
		$build = ORM::factory('build', array('name' => $values['build']));
		
		// Add remaining values needed
		$values['dungeon_id']   = $dungeon->id;
		$values['status_id']    = $status;
		$values['time']         = $time;
		$values['character_id'] = $character->id;
		$values['user_id']      = $user->id;
		$values['build_id']     = $build->id;
		
		// Create record and save
		return $this->values($values, $expected)->create();
	 }
	 
	/**
	 * Create a new event
	 */
	 public function edit_event(Model_ACL_User $user, $values, $expected)
	 {
		// Convert date+time to epoch timestamp
		$time_string = (string) $values['time'] ." ". (string) $values['date'];
		$time = strtotime($time_string);
		
		// Offset timestamp from user's timezone to GMT for storage
		$time = Date::offset('Europe/London', $values['timezone']) + $time;
		
		// Convert dungeon name to id
		$dungeon    = ORM::factory('dungeon', array('name' => $values['dungeon']));
		
		// Convert status name to id
		$status    = Model_Status::SCHEDULED;
		
		// Get character id
		$character = ORM::factory('character', array('name' => $values['character']));
		
		// Get build id
		$build = ORM::factory('build', array('name' => $values['build']));
		
		// Add remaining values needed
		$values['dungeon_id']   = $dungeon->id;
		$values['status_id']    = $status;
		$values['time']         = $time;
		$values['character_id'] = $character->id;
		$values['user_id']      = $user->id;
		$values['build_id']     = $build->id;
		
		// Save record
		return $this->values($values, $expected)->save();
	 }

}