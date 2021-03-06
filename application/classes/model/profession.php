<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Profession-related data
 */
class Model_Profession extends ORM {

	// Profession constants
	const WARRIOR      = 1;
	const RANGER       = 2;
	const MONK         = 3;
	const NECROMANCER  = 4;
	const ELEMENTALIST = 5;
	const MESMER       = 6;
	const RITUALIST    = 7;
	const ASSASSIN    = 8;
	const DERVISH      = 9;
	const PARAGON      = 10;
	
	// Relationships
	protected $_has_many = array(
		'characters' => array(),
		'slots'      => array(
			'through' => 'professions_slots',
			'model'   => 'slot',
		),
	);
	
	/**
	 * Returns array of all current professions
	 *
	 * @return  array
	 */
	public static function profession_list()
	{	
		return array('warrior', 'ranger', 'monk', 'necromancer', 'elementalist', 'mesmer', 'ritualist', 'assassin', 'dervish', 'paragon');
	}
	 
}