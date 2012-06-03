<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Contains elements to be displayed on the page
 */
class Abstract_View_Page extends Abstract_View_Layout {
	
	/**
	 * @var Page title
	 */
	public $title = 'RedditGW Events';
	
	/**
	 * @var  object  Current user
	 */
	public $user;
	
	/**
	 * Display notifications to the user, most commonly errors
	 *
	 * @return  string  html data for notice display
	 */
	public function notices()
	{
		$notices = array();
		
		foreach (Notices::get(TRUE, TRUE) as $notice)
		{			
			// Build our data array
			$notices[] = (object) array(
				'type'          => $notice['type'],
				'message'       => $notice['values']['message'],
				'hash'          => $notice['values']['hash'],
				'is_persistent' => $notice['values']['is_persistent'],
				'key'           => $notice['key']
			);
		}
		
		$output = '';
		
		foreach ($notices as $notice)
		{
			// It's not a mustache template, but it came with the module.  Don't judge me.
			$output .= View::factory('notices/notice')->set('notice', $notice);
		}
		
		return $output;
	}
	
	/**
	 * List of navigation links to be used in menu bar
	 * Child classes should set their own and Arr::merge() with 
	 * parent::links() as needed.
	 *
	 * The links array is indexed with each element being an associative
	 * array with this structure:
	 * 'location' => URL for link
	 * 'text'     => Anchor text
	 * 'icon'     => [optional] Image prepended to link text
	 *
	 * @return  Array  List of links
	 */
	public function links()
	{
		if ( ! $this->user)
		{		
			$this->user = Auth::instance()->get_user();
		}
		
		$links = array();
		
		// Admin links
		if ($this->user->can('use_admin'))
		{
			$links[] = array(
				'text'		=> 'Administrator',
				'icon'		=> array('src' => '/media/img/dropdown_arrow.png'),
				'dropdown'	=> array(
					'dropdown_links' => array(
						array(
							'location' => Route::url('admin'),
							'text'     => 'Dashboard',
						),
						
						array(
							'location' => Route::url('admin', array('controller' => 'role')),
							'text'     => 'Roles',
						),
						
						array(
							'location' => Route::url('admin', array('controller' => 'user')),
							'text'     => 'Users',
						),
						
						array(
							'location' => Route::url('admin', array('controller' => 'dashboard', 'action' => 'settings')),
							'text'     => 'Settings',
						),
					),
				),
			);
		}
		
		// Link to resend verification email
		if ( ! $this->user->is_a('verified_user') AND $this->user->can('get_registration_email'))
		{
			$links[] = array(
				'location' => Route::url('email registration', array('action' => 'send')),
				'text'     => 'Resend verification email'
			);
		}
		
		// New account registration link
		if ($this->user->can('register'))
		{
			$links[] = array(
				'location' => Route::url('user', array('controller' => 'user', 'action' => 'register')),
				'text'     => 'Create an account',
			);
		}
		
		// Login link
		if ($this->user->can('login'))
		{
			$links[] = array(
				'location' => Route::url('user', array('controller' => 'user', 'action' => 'login')),
				'text'     => 'Log in'
			);
		}
		
		/* User account links:
		my account
		my profile
		my characters
		my events
		logout 	*/
		// And other links..
		if (Auth::instance()->logged_in())
		{
			if ($this->user->can('edit_own_profile'))
			{
				$links[] = array(
					'text'		=> 'My Account',
					'icon'		=> array('src' => '/media/img/dropdown_arrow.png'),
					'dropdown'	=> array(
						'dropdown_links' => array(
							array(
								'location' => Route::url('user', array('controller' => 'user', 'action' => 'manage')),
								'text'     => 'My Profile'
							),
							
							array(
								'location' => Route::url('character'),
								'text'     => 'My Characters',
							),
							
							array(
								'location' => Route::url('event'),
								'text'     => 'My Events',
							),
							
							array(
								'location' => Route::url('user', array('controller' => 'user', 'action' => 'logout')),
								'text'     => 'Log out'
							),
						),
					),
				);
			}

		
			$links[] = array(
				'location' => Route::url('event'),
				'text'     => 'Events',
			);
			
			$links[] = array(
				'location' => Route::url('build'),
				'text'     => 'Builds',
			);
			
			$links[] = array(
				'location' => Route::url('slot'),
				'text'     => 'Slots',
			);
			
			$links[] = array(
				'location' => Route::url('dungeon'),
				'text'     => 'Dungeons',
			);
			
		}
		
		return $links;
	}
	
	/**
	 * Uses Kohana's Security class to generate tokens
	 * providing basic protection agasint cross-site 
	 * request forgery.
	 *
	 * @return  string  HTML for hidden form element with CSRF token
	 */
	public function csrf()
	{
		return Form::hidden('csrf', Security::token());
	}
	
	/**
	 * Displays basic profiling information conditionally if
	 * $this->profiler is TRUE
	 *
	 * @see     APPPATH./classes/abstract/controller/website.php line 47
	 * @return  string  HTML data for toolbar
	 */
	public function stats()
	{
		return ProfilerToolbar::render(FALSE);
	}
	
	/**
	 * Set assets group to be used
	 */
	public function assets($assets)
	{
		$assets->group('default');
		return parent::assets($assets);
	}

}