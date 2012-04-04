<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Character_Add extends Policy {

	const NOT_LOGGED_IN = 1;
	const NOT_ALLOWED   = 2;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Can't add characters if you aren't logged in
		if ( ! Auth::instance()->logged_in())
		{
			return self::NOT_LOGGED_IN;
		}
		
		// Probably other reasons we might not want to allow creation... will add in the future
		return TRUE;
	}
}