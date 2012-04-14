<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Build extends Abstract_Controller_Website {

	public function action_index()
	{		
		$this->view->build_data = ORM::factory('build')->where('visibility', '=', 1)->find_all();
	}
	
	public function action_add()
	{
		if ( ! $this->user->can('build_add'))
		{
			ProfilerToolbar::addData('failing policy: '.Policy::$last_code);
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->view = Kostache::factory('page/build/index')
				->assets(Assets::factory())
				->set('build_data', ORM::factory('build')->where('visibility', '=', 1)->find_all());
		}
		else
		{
			if ($this->valid_post())
			{
				$build_post    = Arr::get($this->request->post(), 'build',    array());
				$quantity_post = Arr::get($this->request->post(), 'quantity', array());
				
				try
				{
					$build = ORM::factory('build');
					$build->add_build($build_post, $quantity_post);
				}
				catch(ORM_Validation_Exception $e)
				{
					Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				}
				
				$this->view->build_data = Arr::merge($build_post, $quantity_post);
			}
		}
	}
	
	public function action_edit()
	{	
		$build = ORM::factory('build', $this->request->param('id'));
		
		if ( ! $this->user->can('build_edit', array('build' => $build)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->view = Kostache::factory('page/build/index')
				->assets(Assets::factory())
				->set('build_data', ORM::factory('build')->where('visibility', '=', 1)->find_all())
				->set('user', $this->user);
		}
		else
		{
			if ($this->valid_post())
			{
				$build_post    = Arr::get($this->request->post(), 'build',    array());
				$quantity_post = Arr::get($this->request->post(), 'quantity', array());
				
				try
				{
					$build->edit_build($build_post, $quantity_post);
				}
				catch(ORM_Validation_Exception $e)
				{
					Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				}
			}
			
			$this->view->build_data = $build;
			
		}
		$this->view->user = $this->user;
	}
	
	public function action_remove()
	{
		$build = ORM::factory('build', $this->request->param('id'));

		if ( ! $this->user->can('build_remove', array('build' => $build->id)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		else
		{
			$build->visibility = 0;
			$build->save();
			
			Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
		}
		
		$this->view = Kostache::factory('page/build/index')
			->assets(Assets::factory())
			->set('build_data', ORM::factory('build')->where('visibility', '=', 1)->find_all())
			->set('user', $this->user);
	}

}