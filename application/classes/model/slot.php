<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for different roles filled by players for a given event build.  A slot
 * will belong to potentially many builds through a m:n relationship called functions.
 */
class Model_Slot extends ORM {

	// Relationships
	protected $_has_many = array(
		'builds' => array(
			'model'   => 'build',
			'through' => 'functions',
		),
		'professions' => array(
			'model'   => 'profession',
			'through' => 'professions_slots',
		),
		'signups' => array(),
	);
}