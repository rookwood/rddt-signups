<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Character_Add extends Abstract_View_Page {
	
	/**
	 * @var  array  Errors encountered during form validation
	 */
	public $errors;
	
	/**
	 * Form action for adding characters
	 *
	 * @return  string  form action url
	 */
	public function action()
	{
		return Route::url('character', array('action' => 'add'));
	}

	/**
	 * List of professions for <select> dropdown
	 *
	 * @return  array  list of professions
	 */
	public function professions()
	{
		return Model_Profession::profession_list();
	}
	
}