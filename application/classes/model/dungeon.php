<?php defined('SYSPATH') or die('No direct access allowed.');/** * Model for dungeons to be used by events */class Model_Dungeon extends ORM {	// Relationships
	protected $_has_many = array('events' => array());	public function add_dungeon($data)	{		return $this->values($data, array('name'))->create();	}}