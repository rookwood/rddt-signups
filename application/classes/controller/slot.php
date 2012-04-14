<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Slot extends Abstract_Controller_Website {
	
	public function action_index()
	{
		// Send all slot data to the view class
		$slots = ORM::factory('slot')->find_all();
		
		$this->view->slot_data = $slots;
	}
	
	public function action_add()
	{
		// Is user allowed to add slots?
		if ( ! $this->user->can('slot_add'))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'slot.add.not_allwed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('slot'));
		}
		
		// Valid csrf, etc.
		if ($this->valid_post())
		{
			// Get relevant data from $_POST
			$slot_post = Arr::get($this->request->post(), 'slot', array());
			
			try
			{
				// Create new record
				$slot = ORM::factory('slot');
				$slot->add_slot($slot_post);
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'slot.add.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				$this->request->redirect(Route::url('slot'));
			}
			catch(ORM_Validation_Exception $e)
			{
				// Pass errors and submited data out to the view class
				$this->view->errors = $e->errors();
				$this->view->values = $slot_post;
			}
		}
	}
	
	public function action_edit()
	{
		// Load record to be edited
		$slot = ORM::factory('slot', $this->request->param('id'));
		
		// Can this user edit this slot?
		if ( ! $this->user->can('slot_edit', array('slot' => $slot)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'slot.edit.not_allwed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('slot'));
		}
		
		// Valid csrf, etc.
		if ($this->valid_post())
		{
			// Extract relevant data from $_POST
			$slot_post = Arr::get($this->request->post(), 'slot', array());
			
			try
			{
				// If attempting to edit a non-existant slot, throw exception
				if ( ! $slot->loaded())
					throw new Exception('slot didn\'t load');
					
				// Save data
				$slot->edit_slot($slot_post);
				
				Notices::add('success', 'msg_info', array('message' => Kohana::message('gw', 'slot.edit.success'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
				//$this->request->redirect(Route::url('slot'));
			}
			catch(ORM_Validation_Exception $e)
			{
				// Pass errors and submited values out to the view class
				$this->view->errors = $e->errors();
				$this->view->values = $slot_post;
			}
		}
		$this->view->slot_data = $slot;
	}
	
	public function action_remove()
	{
		// Load record to be removed
		$slot = ORM::factory('slot', $this->request->param('id'));
		
		// Can this user edit this slot?
		if ( ! $this->user->can('slot_edit', array('slot' => $slot)))
		{
			Notices::add('error', 'msg_info', array('message' => Kohana::message('gw', 'slot.remove.not_allwed'), 'is_persistent' => FALSE, 'hash' => Text::random($length = 10)));
			$this->request->redirect(Route::url('slot'));
		}
		
		// Don't want to compeltely remove as that would leave gaps in historical data
		$slot->visibility = 0;
		$slot->save();
		
		$this->request->redirect(Route::url('slot'));
	}
	
}