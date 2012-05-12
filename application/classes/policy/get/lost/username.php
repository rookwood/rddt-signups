<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Get_Lost_Username extends Policy {
	
	const NOT_ALLOWED = 1;
	const LOGGED_IN   = 2;
	
	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		$config = Kohana::$config->load('lost_data');
		
		// Are users allowed to retreive username via email?
		if ($config->get('email_lost_username'))
		{
			// Shouldn't have to get your name if you already logged in
			if (Auth::instance()->logged_in())
			{
				return self::LOGGED_IN;
			}
			
			return TRUE;
		}
		else
		{
			// Not permitted
			return self::NOT_ALLOWED;
		}
		
		// This shouldn't ever happen but is here as a safety default
		return FALSE;
	}

}