<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Display extends Abstract_View_Page {

	/**
	 * @var   object  Event that will be displayed
	 */
	public $event_data;

	public function sign_up_link()
	{
		return Route::url('event', array('action' => 'signup', 'id' => $this->event_data->id));
	}
	
	public function event()
	{
		$event = $this->event_data;
		
		// Calculate start time using user's time offset from GMT
		$local_start_time = Date::offset($this->user->timezone, 'Europe/London') + $event->time;
		
		return array(
			'date'         => date('Y M d', $local_start_time),
			'time'         => date('g:i a', $local_start_time),
			'description'  => $event->description,
			'status'       => $event->status->name,
			'host'         => $event->user->username,
		);
	}
	
	public function attendees()
	{
		return $this->event_data->characters->find_all();
	}
}