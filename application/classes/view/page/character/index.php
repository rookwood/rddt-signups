<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Character_Index extends Abstract_View_Page {

	/**
	 * @var  array  Array of characters
	 */
	public $characters;
	
	/**
	 * @var  bool   Presence of characters to be iterated
	 */
	public $count = FALSE;
	
	/**
	 * @var  array  Profession list to save on database calls
	 */
	protected $_profession_data = array(
			1	=> 'warrior',
			2	=> 'ranger',
			3	=> 'monk',
			4	=> 'necromancer',
			5	=> 'elementalist',
			6	=> 'mesmer',
			7	=> 'ritualist',
			8	=> 'assassin',
			9	=> 'dervish',
			10	=> 'paragon',
	);
	
	/**
	 * Returns a list of the current user's characters
	 *
	 * @return  array  Characters
	 */
	public function character_list()
	{
		foreach ($this->characters as $character)
		{
			$out[] = array(
				'name'       => $character->name,
				'profession' => $this->_profession_data[$character->profession_id],
				'edit_url'   => Route::url('character', array('action' => 'edit', 'id' => $character->id)),
			);
		}
		
		return $out;
	}
	
	/**
	 * URL for the form action to add a new character or FALSE if not allowed
	 *
	 * @return  mixed  (string) URL or (bool) FALSE
	 */	
	public function character_add_link()
	{
		if ($this->user->can('character_add'))
		{
			return Route::url('character', array('action' => 'add'));
		}
		
		return FALSE;
	}
}