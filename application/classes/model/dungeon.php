<?php defined('SYSPATH') or die('No direct access allowed.');/** * Model for dungeons to be used by events */class Model_Dungeon extends ORM {	// Relationships
	protected $_has_many = array('events' => array());	public function rules()	{		return array(			'name' => array(				array('not_empty'),				array('max_length', array(':value', 30)),				array(array($this, 'unique'), array('name', ':value')),			),		);	}		public function add_dungeon($data)	{		return $this->values($data, array('name'))->create();	}}