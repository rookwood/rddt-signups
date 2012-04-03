<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Abstract_Controller_Admin extends Abstract_Controller_Website {
	
	public function before()
	{
		// Save any retrun value from the parent
		$parent = parent::before();
		
		if ( ! $this->user->can('use_admin'))
		{
			// throw new HTTP_Exception_403('Not authorized to access this section');
			Notices::add('denied', 'msg_denied', array('message' => 'You are not authorized for that action', 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			
			// Go to login if needed, otherwise simply return to home
			$this->request->redirect(Auth::instance()->logged_in() ? 
				Route::url('user', array('controller' => 'welcome')) :
				Route::url('user', array('action' => 'login')));
		}
		
		return $parent;
	}
}