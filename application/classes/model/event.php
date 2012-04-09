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
	);
	
	protected $_has_many   = array(
		'characters' => array(
			'model'    => 'character',
			'through'  => 'signups',
		),
	);
	
	/**
	 * Create a new event
	 */
	 public function create_event(Model_ACL_User $user, $values, $expected)
	 {
		// Convert date+time to epoch timestamp
		$time_string = (string) $values['time'] ." ". (string) $values['date'];
		$time = strtotime($time_string);
		
		// Offset timestamp from user's timezone to GMT for storage
		$time = Date::offset('Europe/London', $values['timezone']) + $time;
		
		// Convert dungeon name to id
		$dungeon    = ORM::factory('dungeon', array('name' => $values['dungeon']));
		
		// Convert status name to id
		$status    = ORM::factory('status', array('name' => 'scheduled'));
		
		// Get character id
		$character = ORM::factory('character', array('name' => $values['character']));
		
		// Get build id
		$build = ORM::factory('build', array('name' => $values['build']));
		
		// Add remaining values needed
		$values['dungeon_id']   = $dungeon->id;
		$values['status_id']    = $status->id;
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
		$dungeon_id = $dungeon->id;
		
		// Convert status name to id
		$status    = ORM::factory('status', array('name' => 'scheduled'));
		$status_id = $status->id;
		
		// Add remaining values needed
		$values['dungeon_id'] = $dungeon_id;
		$values['status_id']  = $status_id;
		$values['user_id']    = $user->id;
		$values['time']       = $time;
		
		// Create record and save
		return $this->values($values, $expected)->save();
	 }

}