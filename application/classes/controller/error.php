<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Error extends Controller {
	
	public function action_404()
	{
		$this->response->body('404\'d');
	}
	public function action_503()
	{
		$this->response->body('Maintenance Mode');
	}
	 
	public function action_500()
	{
		$this->response->body('Internal Server Error');
	}
}