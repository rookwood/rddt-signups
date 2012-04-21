<?php defined('SYSPATH') or die('No direct access allowed.');

class View_Page_Event_Index extends Abstract_View_Page {

	/**
	 * @var   object  All event data to be displayed on the page
	 */
	public $event_data;
	
	/**
	 * Builds an array of data for all events to be listed
	 *
	 * @return  mixed  (array) Event data or (bool) FALSE
	 */
	public function events()
	{	
		static $event_list;
		
		if ( ! empty($event_list))
			return $event_list;
		
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
		
		return isset($out) ? $event_list = $out : FALSE;
	}
	
	/**
	 * URL pointing to form to add new event
	 *
	 * @return  mixed  (array) Event data or (bool) FALSE
	 */
	public function add_event()
	{
		if ($this->user->can('event_add'))
		{
			return Route::url('event', array('action' => 'add'));
		}
		
		return FALSE;
	}
	
	public function old_events()
	{
		return Route::url('event').URL::query(array('filter' => 'past'));
	}
	public function filters()
	{
		// Cache results as to save database hits
		static $filter_list;
		
		// Return cached results if available
		if ( ! empty($filter_list))
		{
			return $filter_list;
		}
		
		$out['top'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'current')),
			'text' => 'All current events',
		);		
		
		$out['top'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'mine')),
			'text' => 'My events',
		);
		
		$out['top'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'past')),
			'text' => 'Past events',
		);
		
		foreach (ORM::factory('dungeon')->find_all() as $dungeon)
		{
			$out['dungeon'][] = array(
				'url'  => Route::url('event').URL::query(array('filter' => 'dungeon', 'id' => $dungeon->id)),
				'text' => $dungeon->name,
			);
		}
		
		return $filter_list = $out;
		
	}
}