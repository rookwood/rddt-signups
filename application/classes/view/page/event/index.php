<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Index extends Abstract_View_Page {

	/**
	 * @var   object  All event data to be displayed on the page
	 */
	public $event_data;
	
	public function events()
	{		
		foreach($this->event_data as $event)
		{
			// Calculate start time using user's time offset from GMT
			$local_start_time = Date::offset($this->user->timezone, 'Europe/London') + $event->time;
			
			// Build event array
			$out[] = array(
				'details_link' => Route::url('event', array('action' => 'display', 'id' => $event->id)),
				'date'         => date('Y M d', $local_start_time),
				'time'         => date('g:i a', $local_start_time),
				'description'  => $event->description,
				'status'       => $event->status->name,
				'host'         => $event->user->username,
				'build'        => $event->build,
				'url'          => $event->url,
			);
		}
		
		return isset($out) ? $out : FALSE;
	}

	public function add_event()
	{
		if ($this->user->can('add_event'))
		{
			return Route::url('event', array('action' => 'add'));
		}
		
		return FALSE;
	}
}