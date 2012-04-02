<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'native' => array(
		'name' => 'koreg_session',
		'lifetime' => Date::WEEK,
	),
	'database' => array(
		'name'      => 'session',
		'encrypted' => TRUE,
		'group'     => 'default',
		'table'     => 'sessions',
		'lifetime'  => Date::WEEK,
	),
	'cookie' => array(
		'encrypted' => TRUE,
		'lifetime' => Date::WEEK,
	),
);