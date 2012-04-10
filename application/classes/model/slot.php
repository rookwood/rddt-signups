<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for different roles filled by players for a given event build.  A slot
 * will belong to potentially many builds through a m:n relationship called functions.
 */
class Model_Slot extends ORM {

	// Used to cache a few statuses.  Saves us up to 14 database hits
	public static $cancelled;
	public static $standby;
	
	// Relationships
	protected $_has_many = array(
		'builds' => array(
			'model'   => 'build',
			'through' => 'functions',
		),
		'professions' => array(
			'model'   => 'profession',
			'through' => 'professions_slots',
		),
		'signups' => array(),
	);
	
	public function slot_available(Model_Event $event)
	{		
		// Is the number of total slots greater than the number filled?
		return (bool) (Model_Function::slot_count($event->build, $this) > $this->slots_filled($event));
	}
	
	public function slots_filled(Model_Event $event)
	{
		// Poor man's status caching
		if ( ! self::$cancelled)
			self::$cancelled = ORM::factory('status', array('name' => 'cancelled'))->id;
		
		if ( ! self::$standby)
			self::$standby = ORM::factory('status', array('name' => 'standby'))->id;
		
		// Count how many slots are taken up by sign-ups
		$slots_filled = DB::select(array('COUNT("id")', 'count'))
			->from('signups')
			->where('event_id',       '=', $event->id)
			->and_where('slot_id',    '=', $this->id)
			->and_where('status_id', '!=', self::$cancelled)
			->and_where('status_id', '!=', self::$standby)
			->as_object()
			->execute();
		
		return $slots_filled[0]->count;
	}
}