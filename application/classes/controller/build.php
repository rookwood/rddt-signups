<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Build extends Abstract_Controller_Website {

	public function action_index()
	{		
		// Show all non-hidden builds
		$this->view->build_data = ORM::factory('build')->where('visibility', '=', 1)->find_all();
	}
	
	public function action_add()
	{
		// Can this user add new builds?
		if ( ! $this->user->can('build_add', array('build' => $build)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->view = Kostache::factory('page/build/index')
				->assets(Assets::factory())
				->set('build_data', ORM::factory('build')->where('visibility', '=', 1)->find_all());
		}
		else
		{
			// Valid csrf, etc
			if ($this->valid_post())
			{
				// Extract relevant data from $_POST
				$build_post    = Arr::get($this->request->post(), 'build',    array());
				$quantity_post = Arr::get($this->request->post(), 'quantity', array());
				
				try
				{
					// Create new build object and save
					$build = ORM::factory('build');
					$build->add_build($build_post, $quantity_post);
				}
				catch(ORM_Validation_Exception $e)
				{
					// Pass error data out the view clas
					Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
					$this->view->errors = $e->errors();
					$this->view->values = Arr::merge($build_post, $quantity_post);
				}
			}
			
			$this->view->build_data = $build;
		}
	}
	
	public function action_edit()
	{	
		// Load object to be edited
		$build = ORM::factory('build', $this->request->param('id'));
		
		// Can this user edit this build?
		if ( ! $this->user->can('build_edit', array('build' => $build)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->view = Kostache::factory('page/build/index')
				->assets(Assets::factory())
				->set('build_data', ORM::factory('build')->where('visibility', '=', 1)->find_all());
		}
		else
		{
			// Valid csrf, etc.
			if ($this->valid_post())
			{
				// Extract relvant data from $_POST
				$build_post    = Arr::get($this->request->post(), 'build',    array());
				$quantity_post = Arr::get($this->request->post(), 'quantity', array());
				
				try
				{
					// Pass new values to build object and save
					$build->edit_build($build_post, $quantity_post);
				}
				catch(ORM_Validation_Exception $e)
				{
					// Show error notification and provided values
					Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
					$this->view->errors = $e->errors();
					$this->view->values = Arr::merge($build_post, $quantity_post);
				}
			}
			
			$this->view->build_data = $build;
		}
	}
	
	public function action_remove()
	{
		// Load object to be removed
		$build = ORM::factory('build', $this->request->param('id'));
		
		// Can this user remove this build?
		if ( ! $this->user->can('build_remove', array('build' => $build)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->view = Kostache::factory('page/build/index')
				->assets(Assets::factory())
				->set('build_data', ORM::factory('build')->where('visibility', '=', 1)->find_all());
		}
		else
		{
			// We don't want to actually remove it because it would leave gaping holes in past events' data
			$build->visibility = 0;
			$build->save();
		}
	}

}