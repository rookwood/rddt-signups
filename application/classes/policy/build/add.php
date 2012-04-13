<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Build_Add extends Policy {

	public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// Very secure policy here
		return TRUE;
	}
}