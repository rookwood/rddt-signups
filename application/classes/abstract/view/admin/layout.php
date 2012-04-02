<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Base class for Admin-area views
 */
abstract class Abstract_View_Admin_Layout extends Abstract_View_Page {

	/**
	 * @var  array List of mustache partials
	 */
	protected $_partials = array(
		'user_table'  => 'partials/admin/table/user',
		'role_table'  => 'partials/admin/table/role',
		'role_form'   => 'partials/admin/form/role',
		'user_form'   => 'partials/admin/form/user',
		'user_search' => 'partials/admin/form/user/search',
	);
	
	/**
	 * @var  bool  Should roles be displayed in the view
	 */
	public $show_roles = TRUE;
	
	/**
	 * Route for creating new role
	 *
	 * @return string  URL
	 */
	public function button_action_create_role()
	{
		return Route::url('admin', array('controller' => 'role', 'action' => 'create'));
	}
	
	/**
	 * Route for creating new user
	 *
	 * @return  string  URL
	 */
	public function button_action_create_user()
	{
		return Route::url('admin', array('controller' => 'user', 'action' => 'create'));
	}
	
	/**
	 * Route for user search
	 *
	 * @return  string  URL
	 */
	public function user_search_action()
	{
		return Route::url('admin', array('controller' => 'user', 'action' => 'search'));
	}
	
	/**
	 * Links to be displayed
	 *
	 * @return  Array  Links used on the page
	 */
	public function links()
	{
		// This should be passed from the controller, but just in case...
		if ( ! $this->user)
		{
			$this->user = Auth::instance()->get_user();
		}
		
		$links = array();
		
		// Link back to publicly-viewable area of site
		$links[] = array(
			'location' => '/',
			'text'     => 'Main Site',
		);
		
		// Administrative sections
		if ($this->user->can('use_admin'))
		{
			$links[] = array(
				'location' => Route::url('admin'),
				'text'     => 'Admin dashboard',
				'icon'     => array('src' => '/media/img/icons/application_form.png'),
			);
			
			$links[] = array(
				'location' => Route::url('admin', array('controller' => 'role')),
				'text'     => 'Roles',
			);
			
			$links[] = array(
				'location' => Route::url('admin', array('controller' => 'user')),
				'text'     => 'Users',
			);
			
			$links[] = array(
				'location' => Route::url('admin', array('controller' => 'dashboard', 'action' => 'settings')),
				'text'     => 'Settings',
			);
		}

		// Display log out link
		if (Auth::instance()->logged_in())
		{
			$links[] = array(
				'location' => Route::url('user', array('controller' => 'user', 'action' => 'logout')),
				'text'     => 'Log out'
			);
		}
		
		return $links;
	}
}