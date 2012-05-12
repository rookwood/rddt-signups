<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * A function is the model for containing the relationship between event builds and
 * character slots.  This model was necessary since a build will have a varying
 * number of each slot role.
 */
class Model_Function extends ORM {

	// Relationships
	protected $_has_many = array(
		'builds' => array(),
		'slots'  => array(),
	);

	public static function slot_count($build, $slot)
	{
		// If passed slot name instead of object, convert to object
		if (is_string($slot))
		{
			$slot = ORM::factory('slot', array('name' => $slot));
			
			if ( ! $slot->loaded())
			{
				throw new Exception('Slot not found');
			}
		}
		
		// If passed build name instead of object, convert to object
		if (is_string($build))
		{
			$build = ORM::factory('build', array('name' => $build));
			
			if ( ! $build->loaded())
			{
				throw new Exception('Build not found');
			}
		}
		
		// Get number of slot available for build
		return ORM::factory('function', array('build_id' => $build->id, 'slot_id' => $slot->id))->number;
	}
	
}