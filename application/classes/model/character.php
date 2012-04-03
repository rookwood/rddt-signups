<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model of user's character
 */
class Model_Character extends ORM {

	// Relationships
	protected $_belongs_to = array('user' => array());
		
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
	
}