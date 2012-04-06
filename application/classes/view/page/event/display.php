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
			'description'  => $event->title,
			'status'       => $event->status->name,
			'host'         => $event->user->username,
			'build'        => $event->build,
			'url'          => $event->url,
		);
	}
	
	public function attendees()
	{
		$attendees = $this->event_data->characters->find_all();
		
		foreach ($attendees as $character)
		{
			$out[] = array(
				'profession' => $character->profession->name,
				'name'       => $character->name,
			);
		}
		
		return isset($out) ? $out : FALSE;
	}
	
	public function characters()
	{
		$characters =  $this->user->characters->find_all();
		
		foreach ($characters as $character)
		{
			$out[] = array(
				'profession' => $character->profession->name,
				'name'       => $character->name,
			);
		}
		
		return $out;
	}
	
	public function edit_event()
	{
		if ($this->user->can('event_edit', array('event' => $this->event_data)))
		{
			return Route::url('event', array('action' => 'edit', 'id' => $this->event_data->id));
		}
		return FALSE;
	}
	
	public function remove_event()
	{
		if ($this->user->can('event_remove', array('event' =>$this->event_data)))
		{
			return Route::url('event', array('action' => 'remove', 'id' => $this->event_data->id));
		}
	}
}