<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * A function is the model for containing the relationship between event builds and
 * character slots.  This model was necessary since a build will have a varying
 * number of each slot role.
 */
class Model_Function extends ORM {

	// Relationships
	protected $_has_many = array(
		'builds' => array(),
		'slots'  => array(),
	);

}