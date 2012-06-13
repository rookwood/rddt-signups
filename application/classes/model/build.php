<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for team compositions used by events.  A given build has
 * many different slots of varying numbers defined through the m:n
 * relationship called functions.
 */
 class Model_Build extends ORM {

	// Relationships
	protected $_has_many = array(
		'slots' => array(
			'through' => 'functions',
			'model' => 'slot',
		),
		'events' => array(),
	);

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 50)),
				array(array($this, 'unique'), array('name', ':value')),
			),
			'url' => array(
				array('url'),
			),
		);
	}
	
	public static function max_player_count($build)
	{
		if ( ! $build instanceof Model_Build)
		{
			$build = ORM::factory('build', array('name' => $build));
		}
		
		$functions = ORM::factory('function')->where('build_id', '=', $build->id)->find_all();
		
		$quantity = 0;
		
		foreach ($functions as $slot)
		{
			$quantity += $slot->number;
		}
		
		return $quantity;
	}
	
	/**
	 * Add a new build
	 *
	 * @chainable
	 * @param   array   Build name, url
	 * @param   array   Slot quantities
	 * @return  object  $this
	 */
	public function add_build(array $build, array $quantity)
	{
		// Set name and url
		$this->name = $build['name'];
		$this->url  = $build['url'];
		
		// Create new record
		$this->create();
		
		// Now add slot relations
		foreach ($quantity as $name => $quantity)
		{
			// Make sure sensibile data is used
			if (is_numeric($quantity))
			{
				$slot = ORM::factory('slot', array('name' => $name));
				
				$function = ORM::factory('function', array('slot_id' => $slot->id, 'build_id' => $this->id));
				
				// Use integers here
				$quantity = (int) $quantity;
				
				if ($function->loaded())
				{
					// Remove empty slots
					if ($quantity === 0)
					{
						$function->delete();
					}
					else
					{
						$function->number = $quantity;
					
						$function->save();
					}
				}
				else
				{
					// Make new relation
					$function->build_id = $this->id;
					$function->slot_id  = $slot->id;
					$function->number   = $quantity;
					$function->create();
				}
			}
		}
		
		return $this;
	}
	
	public function edit_build(array $build, array $quantity)
	{
		$this->name = $build['name'];
		$this->url  = $build['url'];
		
		$this->save();
		
		foreach ($quantity as $name => $quantity)
		{
			if (is_numeric($quantity))
			{
				$slot = ORM::factory('slot', array('name' => $name));
				
				$function = ORM::factory('function', array('slot_id' => $slot->id, 'build_id' => $this->id));
				
				// Use integers here
				$quantity = (int) $quantity;
				
				if ($function->loaded())
				{
					// Remove empty slots
					if ($quantity === 0)
					{
						$function->delete();
					}
					else
					{
						$function->number = $quantity;
					
						$function->save();
					}
				}
				else
				{
					$function->build_id = $this->id;
					$function->slot_id  = $slot->id;
					$function->number   = $quantity;
					$function->create();
				}
			}
		}
	}
}