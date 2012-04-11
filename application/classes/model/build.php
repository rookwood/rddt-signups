<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for team compositions used by events.  A given build has
 * many different slots of varying numbers defined through the m:n
 * relationship called functions.
 */
 class Model_Build extends ORM {

	// Relationships
	protected $_has_many = array(
		'slots' => array(
			'through' => 'functions',
			'model' => 'slot',
		),
		'events'    => array(),
	);
}