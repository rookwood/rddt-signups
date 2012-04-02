<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Main administrative dashboard
 */
class Controller_Admin_Dashboard extends Abstract_Controller_Admin {
	
	public function action_index(){}
	
	/**
	 * Settings configurable through admin gui
	 */
	public function action_settings()
	{
		if ($this->valid_post())
		{
			$options_post = Arr::get($this->request->post(), 'options', array());
			
			foreach ($options_post as $group => $setting)
			{
				$config = Kohana::$config->load($group);
				
				foreach ($setting as $option => $value)
				{
					// Probably a better way to do this
					if (strpos($value, 'bool') !== FALSE)
					{
						$value = (bool) substr($value, 0, 1);
					}
					
					$config->set($option, $value);
				}
			}
			Notices::add('info', 'msg_info', array('message' => Kohana::message('koreg', 'admin.settings.set.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
	}
}