<?php defined('SYSPATH') or die('No direct script access.');

class Policy_Dungeon_Remove extends Policy {

	const NOT_LOGGED_IN = 1;
	const NOT_ALLOWED   = 2;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		if ( ! Auth::instance()->logged_in())
			return self::NOT_LOGGED_IN;
			
		if ($user->is_an('admin') or $user->is_a('leadership'))
		{
			return TRUE;
		}
		
		return self::NOT_ALLOWED;
	}

}