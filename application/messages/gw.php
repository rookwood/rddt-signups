<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'character' => array(
		'add'    => array(
			'not_logged_in'     => 'You must be logged in to add new characters',
			'not_allowed'       => 'You may not add new characters at this time',
			'success'           => 'Character added successfully',
		),
		'edit'   => array(
			'not_allowed'       => 'You may not edit your characters at this time',
			'not_owner'         => 'You may only edit your own characters',
		),
		'remove' => array(
			'not_allowed'       => 'You may not remove characters at this time',
			'not_owner'         => 'You may only remove characters that you own',
			'success'           => 'Character removed successfully',
		)
	),
	'event' => array(
		'view'   => array(
			'not_allowed'       => 'You are not authorized to view details for this event.',
		),
		'add'    => array(
			'not_allowed'       => 'You are not authorized to create new events.',
			'failed '           => 'There was an error creating the event.  Please check the highlighted fields.',
			'success'           => 'Event created successfully',
		),
		'edit'   => array(
			'not_allowed'       => 'You do not have permission to edit this event.',
			'not_owner'         => 'You may only edit events that you created.',
		),
		'remove' => array(
			'not_allowed'       => 'You are not allowed to cancel this event.',
			'not_owner'         => 'You may only cancel events that you created.',
			'start_time_passed' => 'You may not cancel events once their start time has passed.',
		),
		'signup' => array(
			'not_allowed'       => 'You do not currently have permission to sign-up for this event.',
			'success'           => 'You are now signed-up for this event.',
			'failed'            => 'There was an error in your information.  Please check the highlighted fields.',
			'already_enrolled'  => 'You are already signed-up for this event.  If you with to change to another character, cancel your current spot first.',
		),
		'withdraw' => array(
			'success'           => 'You are no longer signed-up for this event.',
			'failed'            => 'There was an error cancelling your spot.  Please try again.',
			'not_signed_up'     => 'You cannot withdraw from an event for which you did not sign-up.',
			'start_time_passed' => 'You cannot withdraw from events once their start time has passed.',
		),
	),
);