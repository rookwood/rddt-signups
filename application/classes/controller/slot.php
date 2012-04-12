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
	
	public function action_edit(){}
	
	public function action_remove(){}
	
}