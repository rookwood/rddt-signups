<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Build_Edit extends Policy {

	const NOT_LOGGED_IN = 1;
	const LOCKED        = 2;
	const NOT_OWNER     = 3;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		if ( ! Auth::instance()->logged_in())
		{
			return self::NOT_LOGGED_IN;
		}
		return TRUE;
	}
}