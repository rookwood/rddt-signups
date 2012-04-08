<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Character_Edit extends Policy {

	const NOT_OWNER = 1;
	
	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Anyone can edit their own characters
		if ($user->owns($extras['character']))
		{
			return TRUE;
		}
		else
		{
			// Admins can edit anything
			if ($user->is_an('admin'))
			{
				return TRUE;
			}
				
			// Guild leadership can edit other players' characters
			if ($user->is_a('leadership'))
			{
				return TRUE;
			}
			
			// Anyone else shouldn't be here
			return self::NOT_OWNER
		}
	}
}