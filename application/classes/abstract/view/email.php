<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Base view class for any emails
 */
abstract class Abstract_View_Email extends Kostache_Layout {

	/**
	 * @var  bool  Should the email use the boilerplate html file?
	 */
	public $render_layout = FALSE;
	
}