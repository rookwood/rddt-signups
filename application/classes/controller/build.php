<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Build extends Abstract_Controller_Website {

	public function action_index()
	{		
		$this->view->build_data = ORM::factory('build')->where('visibility', '=', 1)->order_by('name', 'ASC')->find_all();
	}
	
	public function action_add()
	{
		if ( ! $this->user->can('build_add'))
		{
			$status = Policy::$last_code;
			
			if (Policy::$last_code === Policy_Build_Add::NOT_LOGGED_IN)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.add.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
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
					
					$this->request->redirect(Route::url('build'));
				}
				catch(Exception $e)
				{
					if ($e instanceof Database_Exception)
					{
						$build = ORM::factory('build', array('name' => $build_post['name']));
						$build->visibility = 1;
						$build->edit_build($build_post, $quantity_post);
						
						$this->request->redirect(Route::url('build'));
					}
					else
					{
						Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.add.failed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
						$this->view->errors = $e->errors('validation');
					}
				}
				
				$this->view->build_data = Arr::merge($build_post, $quantity_post);
			}
		}
	}
	
	public function action_edit()
	{	
		$build = ORM::factory('build', $this->request->param('id'));
		
		if ( ! $build->loaded())
			throw new HTTP_Exception_404;
		
		if ( ! $this->user->can('build_edit', array('build' => $build)))
		{
			$status = Policy::$last_code;
			
			if (Policy::$last_code === Policy_Build_Add::NOT_LOGGED_IN)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			elseif (Policy::$last_code === Policy_Build_Add::LOCKED)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.protected'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			elseif (Policy::$last_code === Policy_Build_Add::NOT_OWNER)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.not_owner'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
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
					$build->edit_build($build_post, $quantity_post);
					
					Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'build.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
					$this->request->redirect('build');
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

		if ( ! $this->user->can('build_remove', array('build' => $build)))
		{
			$status = Policy::$last_code;
			
			if (Policy::$last_code === Policy_Build_Add::NOT_LOGGED_IN)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			elseif (Policy::$last_code === Policy_Build_Add::LOCKED)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.protected'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			elseif (Policy::$last_code === Policy_Build_Add::NOT_OWNER)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.not_owner'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'build.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
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