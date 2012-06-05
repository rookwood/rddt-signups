<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

Kohana::$environment = Kohana::TESTING;

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => '',
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

Cookie::$salt = Kohana::$config->load('cookie')->get('salt');
Cookie::$expiration = Kohana::$config->load('cookie')->get('expiration');

Session::$default = 'native';

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'profiler'   => MODPATH.'profilertoolbar', // Alert's Profiler Toolbar
	'assets'        => MODPATH.'assets',          // Synapse Studio's asset manager
	'auth'          => MODPATH.'auth',            // Basic authentication
	// 'cache'      => MODPATH.'cache',           // Caching with multiple backends
	'database'      => MODPATH.'database',        // Database access
	'email'         => MODPATH.'email',           // Kohana wrapper for SwiftMailer
	'kostache'      => MODPATH.'kostache',        // Class-based views / logicless templates
	'notices'       => MODPATH.'notices',         // Synapse Studios - user notification
	'orm'           => MODPATH.'orm',             // Object Relationship Mapping
	// 'unittest'   => MODPATH.'unittest',        // Unit testing
	'userguide'     => MODPATH.'userguide',       // User guide and API documentation
	'vendo-acl'     => MODPATH.'vendo-acl',       // Vendo's policy-based authorization system
));

// Now that the database module has been loaded...
Kohana::$config->attach(new Config_Database, FALSE);

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */ 
Route::set('admin', '<directory>(/<controller>(/<action>(/<name>)))', array('directory' => 'admin'))
	->defaults(array(
		'directory'  => 'admin',
		'controller' => 'dashboard',
		'action'     => 'index',
	));
	
Route::set('user', '<action>(/<name>)', array('action' => 'manage|register|profile|lostpw|lostname|email|check|login|logout'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'manage'
	));
	
Route::set('email registration', 'registration/<action>', array('action' => 'email|check'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'email'
	));
	
Route::set('jail', 'verification')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'jail'
	));

Route::set('profile', 'profile(/<user>)')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'profile'
	));

Route::set('event', 'event(/<action>(/<id>))', array('action' => 'add|remove|edit|display|withdraw|signup'))
	->defaults(array(
		'controller' => 'event',
		'action'     => 'index',
	));

Route::set('character', 'character(/<action>(/<id>))', array('action' => 'add|remove|edit'))
	->defaults(array(
		'controller' => 'character',
		'action'     => 'index',
	));

Route::set('build', 'build(/<action>(/<id>))', array('action' => 'add|remove|edit'))
	->defaults(array(
		'controller' => 'build',
		'action'     => 'index',
	));

Route::set('slot', 'slot(/<action>(/<id>))', array('action' => 'add|remove|edit'))
	->defaults(array(
		'controller' => 'slot',
		'action'     => 'index',
	));

Route::set('dungeon', 'dungeon(/<action>(/<id>))', array('action' => 'add|remove|edit'))
	->defaults(array(
		'controller' => 'dungeon',
		'action'     => 'index',
	));

Route::set('error', 'error/<action>')
	->defaults(array(
		'controller' => 'error',
		'action'     => '404',
	));
	
Route::set('cleanroom', 'cleanroom')
	->defaults(array(
		'controller' => 'cleanroom',
		'action'     => 'index',
	));
	
Route::set('default', '')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));