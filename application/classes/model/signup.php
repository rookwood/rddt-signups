<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Event sign-up model
 */
class Model_Signup extends ORM {

	// Relationships
	protected $_has_many = array(
		'events'     => array(),
		'characters' => array(),
	);

}