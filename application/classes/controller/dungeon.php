<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dungeon extends Abstract_Controller_Website {

	public function action_index()
	{
		$dungeons = ORM::factory('dungeon')->find_all();
		
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
					$this->view->errors = $e->errors('dungeon');
					$this->view->values = $dungeon_post;
				}
			}
		}
	}
}