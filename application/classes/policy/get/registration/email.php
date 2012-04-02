<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Get_Registration_Email extends Policy {

	const REGISTRATION_COMPLETED = 1;
	const ACCOUNT_DEACTIVATED    = 2;
	const NOT_REQUIRED           = 3;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)	{
		$config = Kohana::$config->load('registration');
		
		// Do we require email verification for new accounts?
		if ($config->get('require_email_verification'))
		{
			// Has this process already been completed?
			if ($user->is_a('verified_user'))
			{
				return self::REGISTRATION_COMPLETED;
			}
			
			// Has the user been banned / deactivated
			if ( ! $user->is_a('login'))
			{
				return self::ACCOUNT_DEACTIVATED;
			}
			
			// Allow the email to be sent
			return TRUE;
		}
		else
		{
			// Email verification not required
			return self::NOT_REQUIRED;
		}
		
		// This shouldn't ever happen but is here as a safety default
		return FALSE;
	}
}