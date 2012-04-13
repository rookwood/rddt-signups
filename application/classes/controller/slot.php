<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Slot extends Abstract_Controller_Website {
	
	public function action_index()
	{
		$slots = ORM::factory('slot')->find_all();
		
		$this->view->slot_data = $slots;
	}
	
	public function action_add()
	{
		if ( ! $this->user->can('slot_add'))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'slot.add.not_allwed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('slot'));
		}
		
		if ($this->valid_post())
		{
			ProfilerToolbar::addData($_POST);
			$slot_post = Arr::get($this->request->post(), 'slot', array());
			
			try
			{
				$slot = ORM::factory('slot');
				$slot->add_slot($slot_post);
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'slot.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('slot'));
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->view->errors = $e->errors();
				$this->view->values = $slot_post;
			}
		}
	}
	
	public function action_edit()
	{
		$slot = ORM::factory('slot', $this->request->param('id'));
		
		if ( ! $this->user->can('slot_edit', array('slot' => $slot)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'slot.edit.not_allwed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('slot'));
		}
		
		if ($this->valid_post())
		{
			ProfilerToolbar::addData($_POST);
			$slot_post = Arr::get($this->request->post(), 'slot', array());
			
			try
			{
				if ( ! $slot->loaded())
					throw new Exception('slot didn\'t load');
					
				$slot->edit_slot($slot_post);
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'slot.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				//$this->request->redirect(Route::url('slot'));
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->view->errors = $e->errors();
				$this->view->values = $slot_post;
			}
		}
		$this->view->slot_data = $slot;
	}
	
	public function action_remove(){}
	
}