<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'user' => array(
		'registration' => array(
			'not_allowed'       => 'Registration is currently closed.  Please speak to your system administrator for an account.',
			'completed'         => 'You have already completed the registration process.',
			'password'          => 'Passwords did not match.',
			'invalid_post'      => 'This form has expired.  Please reload and try again.',
		),
		'login' => array(
			'already_logged_in' => 'You are already logged in.',
			'failed'            => 'Invalid username or password.',
			'banned'            => 'This account has been disabled.  Please speak to your system administrator for details.',
		),
		'logout' => array(
			'success'           => 'You have been logged out.',
		),
		'edit' => array(
			'not_logged_in'     => 'Please log in to edit your profile.',
			'not_allowed'       => 'You are not permitted to make changes to your profile at this time.',
			'success'           => 'Profile updated.',
			'fail'              => 'There was an error saving your information.  Please ensure that you completed the form correctly.',
		),
		'registration_email' => array(
			'subject'           => 'Registration conformation email',
			'sender'            => 'noreply@gw.roycewells.com',
			'bad_key'           => 'Invalid key submitted.  Please check the email you received during registration.',
			'completed'         => 'This key has already been used.',
			'success'           => 'Your email has been verified.  Thanks!',
			'banned'            => 'Your account has been deavtivated by an admin and cannot be reactivated via this means.',
			'not_required'      => 'Email verification is not required at this time.',
		),
		'username_email' => array(
			'not_found'         => 'There is no account associated with that email address.',
			'subject'           => '[rddt]Events Username',
			'sender'            => 'noreply@gw.roycewells.com',
			'success'           => 'Your username has been sent to your email address',
		),
		'password_email' => array(
			'not_found'         => 'There is no account associated with that email address.',
			'subject'           => '[rddt]Events Password',
			'sender'            => 'noreply@gw.roycewells.com',
			'success'           => 'Password reset instructions have been sent to your email.',
			'reset'             => 'Your password has been reset to :password.  We suggest changing this to something more easily remembered.',
		),

	),
	'admin' => array(
		'user' => array(
			'edit' => array(
				'denied'       => 'You are not allowed to edit other users\' profiles.',
				'success'      => 'User profile information saved',
			),
			'create' => array(
				'denied'       => 'You are not allowed to create new users.',
				'success'      => 'New user created.',
				'password'     => 'Passwords did not match.',
			),
			'disable' => array(
				'denied'       => 'You are not allowed to disable user accounts.',
				'success'      => 'User disabled',
			),
			'search' => array(
				'denied'      => 'You do not have permission to search through the user listing.',
			),
		),
		'role' => array(
			'edit' => array(
				'denied'       => 'You are not allowed to edit roles.',
				'success'      => 'Role info updated',
			),
			'remove' => array(
				'denied'       => 'You are not allowed to remove roles.',
				'success'      => 'Role removed',
			),
			'create' => array(
				'denied'       => 'You are not allowed to create new roles.',
				'success'      => 'Role added',
			),
		),
		'settings' => array(
			'set' => array(
				'success'      => 'Settings saved.',
			),
		),
	),
	// Email registration info
	'registration' => array(
			'subject'      => 'Registration conformation email',
			'sender'       => 'registration@gw.roycewells.com',
			'bad_key'      => 'Invalid key submitted.  Please check the email you received during registration.',
			'completed'    => 'This key has already been used.',
			'success'      => 'Your email has been verified.  Thanks!',
			'banned'       => 'Your account has been deavtivated by an admin and cannot be reactivated via this means.',
			'not_required' => 'Email verification is not required at this time.',
	),
	'generic' => array(
		'validation' => 'There were errors on the form, please correct the highlighted fields.',
	),
);