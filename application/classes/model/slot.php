<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for different roles filled by players for a given event build.  A slot
 * will belong to potentially many builds through a m:n relationship called functions.
 */
class Model_Slot extends ORM {
	
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
	
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 20)),
				array(array($this, 'unique'), array('name', ':value')),
			),
		);
	}
	
	public function slot_available(Model_Event $event)
	{		
		// Is the number of total slots greater than the number filled?
		return (bool) (Model_Function::slot_count($event->build, $this) > $this->slots_filled($event));
	}
	
	public function slots_filled(Model_Event $event)
	{		
		// Count how many slots are taken up by sign-ups
		$slots_filled = DB::select(array('COUNT("id")', 'count'))
			->from('signups')
			->where('event_id',       '=', $event->id)
			->and_where('slot_id',    '=', $this->id)
			->and_where('status_id', '!=', Model_Status::CANCELLED)
			->and_where('status_id', '!=', Model_Status::STANDBY_VOLUNTARY)
			->and_where('status_id', '!=', Model_Status::STANDBY_FORCED)
			->as_object()
			->execute();
		
		return $slots_filled[0]->count;
	}
	
	public function add_slot($data)
	{
		// Add slot record
		$this->name = $data['name'];
		$this->create();
		
		// Add profession associations
		foreach ($data['profession'] as $profession)
		{
			$this->add('professions', ORM::factory('profession', array('name' => $profession)));
		}
		
		return $this;
	}
	
	public function edit_slot($data)
	{
		// Save new name if changed
		if ($this->name != $data['name'])
		{
			$this->name = $data['name'];
			$this->save();
		}
		// Deal with profession relationships
		foreach (Model_Profession::profession_list() as $profession)
		{
			// Check if present; add if not
			if (in_array($profession, $data['profession']))
			{
				if ( ! $this->has('professions', ORM::factory('profession', array('name' => $profession))))
				{
					$this->add('professions', ORM::factory('profession', array('name' => $profession)));
				}
			}
			// Check if absent, delete if so
			else
			{
				if ($this->has('professions', ORM::factory('profession', array('name' => $profession))))
				{
					$this->remove('professions', ORM::factory('profession', array('name' => $profession)));
				}
			}
		}
		
		return $this;
	}
	
	public function can_use($character)
	{
		if (is_string($character))
		{
			$character = ORM::factory('character', array('name' => $character));
		}
		
		if ($this->has('professions', $character->profession))
		{
			return TRUE;
		}
		return FALSE;
	}
}