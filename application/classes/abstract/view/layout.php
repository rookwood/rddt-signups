<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Contains items needed for view class setup
 */
abstract class Abstract_View_Layout extends Kostache_Layout {
	
	/**
	 * @var Default layout used as page base
	 */
	protected $_layout = 'layout/boilerplate';
	
	/**
	 * @var Assets object containing css/js groups
	 */
	protected $_assets;
	
	/**
	 * Setup for Assets instance
	 *
	 * @param   object  Assets instance
	 * @returns object  $this
	 */
	public function assets($assets)
	{
		$this->_assets = $assets;
		
		return $this;
	}
	
	/**
	 * Deal with assets to be placed in HTML document <head> (e.g. CSS files)
	 */
	public function assets_head()
	{
		if ( ! $this->_assets)
			return '';

		$assets = '';
		foreach ($this->_assets->get('head') as $asset)
		{
			$assets .= $asset."\n\t";
		}

		return $assets;
	}

	/**
	 * Deal with assets to be placed at the bottom of the HTML document <body> (e.g. most javascript files)
	 */
	public function assets_body()
	{
		if ( ! $this->_assets)
			return '';

		$assets = '';
		foreach ($this->_assets->get('body') as $asset)
		{
			$assets .= $asset."\n\t";
		}

		return $assets;
	}	
	
	/**
	 * Overload Kostache render function to add data from Assets class first
	 */
	public function render()
	{
		$content = parent::render();
		
		return str_replace(array('[[assets_head]]',	'[[assets_body]]'), array($this->assets_head(),	$this->assets_body()), $content);
	}	
}