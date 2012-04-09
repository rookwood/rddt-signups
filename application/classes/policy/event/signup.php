<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Event_Signup extends Policy {

	const NOT_ALLOWED   = 1;
	const STANDBY_ONLY  = 2;
	const PRIVATE_EVENT = 3; //NYI
	const NOT_LOGGED_IN = 4;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Must be logged in to sign-up for events
		if ( ! Auth::instance()->logged_in())
		{
			return self::NOT_LOGGED_IN;
		}
		
		if ($extras['slots'] <= $extras['filled'])
		{
			return self::STANDBY_ONLY;
		}
		
		return TRUE;
		
	}

}