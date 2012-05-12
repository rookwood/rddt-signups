<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Event_Signup extends Policy {

	const NOT_ALLOWED        = 1;
	const PRIVATE_EVENT      = 2; //NYI
	const NOT_LOGGED_IN      = 3;
	const START_TIME_PASSED  = 4;
	const ALREADY_SIGNED_UP  = 5;
	const HAVE_NO_CHARACTERS = 6;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Must be logged in to sign-up for events
		if ( ! Auth::instance()->logged_in())
		{
			return self::NOT_LOGGED_IN;
		}
		
		// Can't signup for events whose start time has already passed
		if ($extras['event']->time < time())
		{
			return self::START_TIME_PASSED;
		}
		
		if ($user->characters->count_all() === 0)
		{
			return self::HAVE_NO_CHARACTERS;
		}
		// Can't signup for events in which you are already enrolled
		if (Model_Signup::is_signed_up($user, $extras['event']))
		{
			return self::ALREADY_SIGNED_UP;
		}
		
		// Can't think of any other reason to keep you out at this point
		return TRUE;
	}

}