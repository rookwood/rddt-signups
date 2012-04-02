<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base controller that should be extended by any controller that would require user
 * verification (e.g. email address) for access
 *
 */
abstract class Abstract_Controller_Verified extends Abstract_Controller_Website {

	public function before()
	{
		// Who is logged in (or empty Model_User if not logged in)
		$this->user = Auth::instance()->get_user();
		
		// If they are logged in but unverified, do not pass Go; do not collect $200
		if (Auth::instance()->logged_in() AND ! $this->user->is_a('verified_user'))
		{
			$this->request->redirect(Route::url('jail'));
		}
		
		return parent::before();
	}


}