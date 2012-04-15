<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Dungeon_Add extends Policy {

	const NOT_LOGGED_IN = 1;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		if ( ! Auth::instance()->logged_in())
			return self::NOT_LOGGED_IN;
			
		return TRUE;
	}

}