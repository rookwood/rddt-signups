<?php defined('SYSPATH') or die('No direct script access.');

abstract class Abstract_Controller_Website extends Controller {

	/**
	 * @var  object  the content View object
	 */
	public $view;
	
	/**
	 * Checks user state (e.g. logged in) and attempts to set a default view based on the controller action
	 */
	public function before()
	{
		$this->session = Session::instance();
		
		// Auto login users if possible and cookie has been set
		if (! Auth::instance()->logged_in())
		{
			try {
				$auth = Auth::instance()->auto_login();
			}
			catch (Exception $e) {
				Notices::add('error', 'msg_error', array('message' => (string) $e, 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
		}
		
		// Get our currently logged in user (or an empty user model)
		if (! isset($this->user))
		{
			$this->user = Auth::instance()->get_user();
			if ( ! isset($this->user->timezone))
			{
				$this->user->timezone = 'America/Chicago';
				Notices::add('info', 'info', array('message' => 'Until you log in, all times are displayed in the America/Chicago timezone.', 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
		}
		
		// Set default title and content views (path only)
		$directory  = $this->request->directory();
		$controller = $this->request->controller();
		$action     = $this->request->action();

		// Removes leading slash if this is not a subdirectory controller
		$controller_path = trim($directory.'/'.$controller.'/'.$action, '/');

		try
		{
			$this->view = Kostache::factory('page/'.$controller_path)
				->assets(Assets::factory());
		}
		catch (Kohana_Exception $x)
		{
			// The View class could not be found, so the controller action is repsonsible for making sure this is resolved.
			$this->view = NULL;
		}

		return parent::before();
	}

	/**
	 * Ensures that a view has been set and passes it to the response object.
	 */
	public function after()
	{
		// If content is NULL, then there is no View to render
		if ($this->view === NULL)
			throw new Kohana_Exception('There was no View created for this request.');

		// Do we want to show the profiler toolbar?
		$this->view->profiler = Kohana::$config->load('site')->get('show_profiler');
		
		// Don't render layout on ajax requests
		if ($this->request->is_ajax())
			$this->view->render_layout = FALSE;
		
		$this->response->body($this->view);
	}

	/**
	 * Returns true if post request and has a valid CSRF
	 *
	 * @return  bool
	 */
	public function valid_post()
	{
		if ($this->request->method() !== HTTP_Request::POST)
			return FALSE;

		if (Request::post_max_size_exceeded())
		{
			Notices::add('error', __('Max filesize of :max exceeded.', array(':max' => ini_get('post_max_size').'B')));
			return FALSE;
		}

		$csrf = $this->request->post('csrf');
		$has_csrf = ! empty($csrf);
		$valid_csrf = $has_csrf AND Security::check($csrf);

		if ($has_csrf AND ! $valid_csrf)
		{
			// CSRF was submitted but expired
			Notices::add('error', __('This form has expired. Please try submitting it again.'));
		
			return FALSE;
		}
		
		return $has_csrf AND $valid_csrf;
	}
}