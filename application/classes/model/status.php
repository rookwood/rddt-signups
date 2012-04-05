<?php defined('SYSPATH') or die('No direct script access.');

class Model_Status extends ORM {

	// Relations
	protected $_has_many = array('events' => array());

}