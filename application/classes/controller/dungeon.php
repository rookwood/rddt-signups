<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dungeon extends Abstract_Controller_Website {

	public function action_index()
	{
		$dungeons = ORM::factory('dungeon')->where('visibility', '=', '1')->order_by('name', 'ASC')->find_all();
		
		$this->view->dungeon_data = $dungeons;
	}
	
	public function action_add()
	{
		if ( ! $this->user->can('dungeon_add'))
		{
			$status = Policy::$last_code;
			
			if (Policy::$last_code === Policy_Dungeon_Add::NOT_LOGGED_IN)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'dungeon.add.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'dungeon.add.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			$this->view = Kostache::factory('page/dungeon/index')
				->assets(Assets::factory())
				->set('dungeon_data', ORM::factory('dungeon')->find_all());
		}
		else
		{
			if ($this->valid_post())
			{
				$dungeon_post = Arr::get($this->request->post(), 'dungeon', array());
				
				try
				{
					$dungeon = ORM::factory('dungeon');
					$dungeon->add_dungeon($dungeon_post);
					
					$this->view = Kostache::factory('page/dungeon/index')
						->assets(Assets::factory())
						->set('dungeon_data', ORM::factory('dungeon')->find_all());
				}
				catch(ORM_Validation_Exception $e)
				{
					$errors = $e->errors('dungeon');
					if (array_key_exists('name', $errors))
					{
						$dungeon = ORM::factory('dungeon', array('name' => $dungeon_post['name'], 'visibility' => '0'));
						if ($dungeon->loaded())
						{
							// Dungeon exists in invisible state
							$dungeon->visibility = 1;
							$dungeon->save();
							$this->request->redirect(Route::url('dungeon'));
						}
					}
					$this->view->errors = $e->errors('dungeon');
					$this->view->values = $dungeon_post;
				}
			}
		}
	}
	
	public function action_edit()
	{
		$dungeon = ORM::factory('dungeon', $this->request->param('id'));
		
		if ( ! $dungeon->loaded())
			throw new HTTP_Exception_404;

		if ( ! $this->user->can('dungeon_edit', array('dungeon' => $dungeon)))
		{
			$status = Policy::$last_code;
			
			if (Policy::$last_code === Policy_Dungeon_Add::NOT_LOGGED_IN)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'dungeon.edit.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'dungeon.edit.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			$this->view = Kostache::factory('page/dungeon/index')
				->assets(Assets::factory())
				->set('dungeon_data', ORM::factory('dungeon')->find_all());
		}
		else
		{
			if ($this->valid_post())
			{
				$dungeon_post = Arr::get($this->request->post(), 'dungeon', array());
				
				$dungeon->values($dungeon_post, array('name'));
				$dungeon->save();
				
				$this->request->redirect(Route::url('dungeon'));
			}
		}
		$this->view->dungeon_data = $dungeon;
	}
	
	public function action_remove()
	{
		$dungeon = ORM::factory('dungeon', $this->request->param('id'));
		
		if ( ! $this->user->can('dungeon_remove', array('dungeon' => $dungeon)))
		{
			$status = Policy::$last_code;
			
			if (Policy::$last_code === Policy_Dungeon_Add::NOT_LOGGED_IN)
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'dungeon.remove.not_logged_in'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			else
			{
				Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'dungeon.remove.not_allowed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			}
			$this->request->redirect(Route::url('dungeon'));
		}
		else
		{
			$dungeon->visibility = 0;
			$dungeon->save();
			
			$this->request->redirect(Route::url('dungeon'));
		}
	}
}