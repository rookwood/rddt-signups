<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model of user's character
 */
class Model_Character extends ORM {

	// Relationships
	protected $_belongs_to = array('user' => array());
		
	// Validation rules
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 19)),
				// To be valid names in game, must start with a letter, contain no numbers, and no more than one consecutive space
				array('regex', array('^[a-zA-Z]+( [a-zA-Z]+)*$')),
				array(array($this, 'character_name_available')),
			),
			'profession' => array(
				array(array($this, 'valid_profession'))	,
			),
		);
	}
	
	/**
	 * Create a new character
	 *
	 * @param  object  User who owns the character
	 * @param  array   Character data
	 * @param  array   Fields expected for creation
	 * @return object  ORM Character model
	 */
	public function create_character(Model_ACL_User $user, $values, $expected)
	{
		// Add user id for character relationship
		$values['user_id'] = $user->id;
		$expected[] = 'user_id';
		
		// Change profession name to appropriate id
		$profession = ORM::factory('profession', array('name' => $values['profession']));
		$values['profession_id'] = $profession->id;
		unset $values['profession'];		
		
		// Create the record
		return $this->values($values, $expected)->create();
	}
	
	/**
	 * Check if character name is already in use
	 *
	 * @param  string  Name of character to be checked
	 * @return bool
	 */
	public function character_name_available($name)
	{
		return ORM::factory('character', array('name', $name))->loaded();
	}
	
	/**
	 * Check if profession provided is present in the game
	 *
	 * @param  string  Name of profession to be checked
	 * @return bool
	 */
	public function valid_profession($profession)
	{
		return in_array($profession, Model_Profession::list());
	}
}