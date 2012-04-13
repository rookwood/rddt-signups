<?php defined('SYSPATH') or die('No direct script access');

class Policy_Slot_Remove extends Policy {

	public function execute(Model_ACL_User $user, Array $extras = NULL)
	{
		return TRUE;
	}

}