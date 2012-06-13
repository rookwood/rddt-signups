<?php defined('SYSPATH') or die('No direct access allowed.');

class Policy_Event_Withdraw extends Policy {

	const START_TIME_PASSED = 1;	const NOT_SIGNED_UP     = 2;		public function execute(Model_ACL_User $user, array $extras = NULL)
	{
		// No cancelling on past events		if ($extras['event']->time < time())		{			return self::START_TIME_PASSED;		}				// Is this user enrolled in this event?		$enrolled = FALSE;				$characters = $user->characters->find_all();		$cancelled = ORM::factory('status', array('name' => 'cancelled'))->id;				foreach ($characters as $character)		{			$signup = ORM::factory('signup')				->where('character_id', '=', $character->id)				->and_where('status_id', '!=', $cancelled)
				->and_where('event_id', '=', $extras['event']->id)				->find_all();						if (count($signup) !== 0)			{				// We've found one, break and move on				$enrolled = TRUE;				break;			}		}				if ( ! $enrolled)		{			// Not enrolled			return self::NOT_SIGNED_UP;		}		else		{			// User is enrolled, permission granted			return TRUE;		}
	}

}