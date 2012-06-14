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
			
			$player_count = ORM::factory('signup')->where('event_id', '=', $event->id)->and_where('status_id', '=', Model_Status::READY)->count_all();
			$player_total = Model_Build::max_player_count($event->build->name);
			// Build event array
			$out[] = array(
				'details_link'  => Route::url('event', array('action' => 'display', 'id' => $event->id)),
				'date'          => date('F d, Y',  $local_start_time),
				'time'          => date('g:i A ', $local_start_time).Date::timezone_abbr($this->user->timezone),
				'time_full'     => date('c',       $local_start_time),
				'title'         => $event->title,
				'status'        => $event->status->name,
				'host'          => ORM::factory('character', $event->character_id)->name,
				'build'         => $event->build->name,
				'url'           => $event->build->url,
				'dungeon'       => $event->dungeon->name,
				'player_count'  => $player_count,
				'player_total'  => $player_total,
				'signup_status' => $this->player_count_status($player_count, $player_total),
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
		// This function is bad, and I should feel bad
		$filter_key = Request::$current->query('filter');
		$filter_key = isset($filter_key) ? $filter_key : 'current';
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'current')),
			'text' => 'Current events',
			'key'  => 'current',
		);		
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'mine')),
			'text' => 'My events',
			'key'  => 'mine',
		);
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'past')),
			'text' => 'Past events',
			'key'  => 'past',
		);
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'dungeon')),
			'text' => 'Dungeon',
			'key'  => 'dungeon',
		);
		
		$out['bottom'][] = array(
			'url'  => Route::url('event').URL::query(array('filter' => 'time')),
			'text' => 'Start time',
			'key'  => 'time',
		);
		
		$index = 0;

		foreach ($out['bottom'] as $filter)
		{
			if (array_search($filter_key, $filter) !== FALSE)
			{
				$out['top'] = $filter;
				unset($out['bottom'][$index]);
			}

			$index += 1;
		}

		// Reindex for mustache... not sure why this is necessary
		$out['bottom'] = array_values($out['bottom']);
		
		return $out;
	}
	
	protected function player_count_status($active, $total)
	{
		switch (TRUE)
		{
			case $active == 0:
				return 'empty';
			break;
			case ($active / $total) <= 0.5:
				return 'low';
			break;
			case $active >= $total:
				return 'full';
			break;
			default:
				return 'high';
			break;
		}
	}
}