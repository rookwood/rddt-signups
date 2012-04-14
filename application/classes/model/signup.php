<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Event sign-up model
 */
class Model_Signup extends ORM {
	
	// Relationships
	protected $_has_many = array(
		'events'     => array(),
		'characters' => array(),
	);
	
	protected $_belongs_to = array('slot' => array());

	/**
	 * Tests if a user is signed up for a given event on any of their characters
	 *
	 * @param   object  Model_ACL_User - the user to test against
	 * @param   objecvt Model_Event    - the event to test against
	 * @return  bool
	 */
	public static function is_signed_up(Model_ACL_User $user, Model_Event $event)
	{
		// Get all characters
		$characters = $user->characters->find_all();
		
		foreach ($characters as $character)
		{
			// Search for record of this character being signed-up for this event
			$slot = ORM::factory('signup', array('event_id' => $event->id, 'character_id' => $character->id));
			
			// If we find that record, the use is already signed-up
			if ($slot->loaded())
			{
				if ($slot->status_id != Model_Status::CANCELLED)
				{
					return TRUE;
				}
			}
		}
		
		// Nothing found; no sign-up present
		return FALSE;
	}
}