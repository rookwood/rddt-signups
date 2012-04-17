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

	public function add_build(array $build, array $quantity)
	{
		$this->name = $build['name'];
		$this->url  = $build['url'];
		
		$this->create();

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