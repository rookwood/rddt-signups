<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Policy for determining if a user must sign-up as stand-by
 */
class Policy_Event_Signup_Active extends Policy {

	const NOT_ALLOWED   = 1;
	const STANDBY_ONLY  = 2;
	
	/**
	 * Extras array should contain the following:
	 *
	 * $extras['event']  object  Model_Event
	 * $extras['slot']   object  Model_Slot
	 */
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Can't signup as active for a role that is already filled out
		if ( ! $extras['slot']->slot_available($extras['event']))
		{
			return self::STANDBY_ONLY;
		}
		
		return TRUE;
	}
}