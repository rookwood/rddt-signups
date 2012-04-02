<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for verification keys (e.g. for email verification)
 */
class Model_Key extends ORM {

	// Relationships
	protected $_belongs_to = array(
		'user' => array('model' => 'user'),
	);

}