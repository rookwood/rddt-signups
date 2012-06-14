<?php defined('SYSPATH') or die('No direct script access');

class View_Page_Slot_Index extends Abstract_View_Page {

	public function slot_list()
	{
		foreach ($this->slot_data as $slot)
		{
			$professions = $slot->professions->find_all();
			
			foreach ($professions as $profession)
			{
				$profs_associated[$slot->name][] = $profession->name;
			}
			
			$out[] = array(
				'name' => $slot->name,
				'edit_link' => Route::url('slot', array('action' => 'edit', 'id' => $slot->id)),
				'professions' => (isset($profs_associated[$slot->name])) ? implode(', ', $profs_associated[$slot->name]) : FALSE,
			);
			
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function slot_add_url()
	{
		return Route::url('slot', array('action' => 'add'));
	}
	
	public function slot_list_url()
	{
		return Route::url('slot');
	}
	
	/**
	 * Build a list of letters that are used to create the slot filter-by-letter list
	 *
	 *
	 */
	public function filter_list()
	{
		// Arrays to store data as it is created
		$found = array();
		$out   = array();
		
		// Check each slot in the database
		foreach ($this->all_slots as $slot)
		{
			// Get the first character
			$letter = strtolower($slot->name[0]);
			
			// If the slot starts with a letter
			if (preg_match('/[a-zA-Z]/', $letter))
			{
				// And that letter hasn't already been stored
				if ( ! in_array($letter, $found))
				{
					// Mark that we have slots beginning with this letter
					$found[] = $letter;
				}
			}
			// If the slot doesn't start with a letter
			else
			{
				// Check to see if we have any other non-alphabet slots
				if ( ! in_array('#', $found))
				{
					// Store non-alpha indicator
					$found[] = '#';
					
					// Go on and make the num filter the first output element
					$out[] = array('slots' => TRUE, 'letter' => '#', 'filter' => 'num');
				}
			}
		}
		
		// Check against the whole alphabet
		foreach (range('a', 'z') as $letter)
		{
			// Mark any letter with current slots as present
			if (in_array($letter, $found))
			{
				$out[] = array('slots' => TRUE, 'letter' => $letter, 'filter' => $letter);
			}
			// Otherwise mark as disabled
			else
			{
				$out[] = array('slots' => FALSE, 'letter' => $letter, 'filter' => $letter);
			}
		}
		
		return $out;
	}
}