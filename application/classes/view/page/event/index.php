<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Index extends Abstract_View_Page {

	/**
	 * @var   object  All event data to be displayed on the page
	 */
	public $event_data;
	
	public function events()
	{
		ProfilerToolbar::addData($this->event_data, 'Event data');
		
		foreach($this->event_data as $event)
		{
			// Calculate start time using user's time offset from GMT
			$local_start_time = Date::offset($this->user->timezone, 'Europe/London') + $event->time;

			// Build event array
			$out[] = array(
				'details_link' => Route::url('event', array('action' => 'display', 'id' => $event->id)),
				'date'         => date('Y M d', $local_start_time),
				'time'         => date('g:i a', $local_start_time),
				'title'        => $event->title,
				'status'       => $event->status->name,
				'host'         => ORM::factory('character', $event->character_id)->name,
				'build'        => $event->build->name,
				'url'          => $event->build->url,
			);
		}
		
		return isset($out) ? $out : FALSE;
	}

	public function add_event()
	{
		if ($this->user->can('event_add'))
		{
			return Route::url('event', array('action' => 'add'));
		}
		
		return FALSE;
	}
}