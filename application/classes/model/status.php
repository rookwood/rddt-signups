<?php defined('SYSPATH') or die('No direct script access.');

class Model_Status extends ORM {

	// These MUST correspond to their record IDs in the database
	const SCHEDULED         = 1;
	const CANCELLED         = 2;
	const READY             = 3;
	const STANDBY_FORCED    = 4;
	const STANDBY_VOLUNTARY = 5;
	
	// Relations
	protected $_has_many = array('events' => array());

}