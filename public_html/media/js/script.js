/* Author: 

*/

$(document).ready(function() {
	// Date picker widget initialization
	$('input[type=date]').datepicker({
		changeYear : true,
		dateFormat : 'yy-mm-dd',
		yearRange  : '1950:+2',
	});
		
	// Automatic background resizing
	var FullscreenrOptions = {  width: 1280, height: 1200, bgID: '#bgimg' };
	jQuery.fn.fullscreenr(FullscreenrOptions);

	
	// Ajax loading of event details
	// can't figure out why ul.header won't work for event binding
	// so using pointless class for the moment but this is problematic
	$('.clickable').click(function() {

		// Find container element for our event data
		var event_data = $(this).find('section.event_data');

		// See if we've already loaded data for this event
		if ( ! $(this).hasClass('loaded'))
		{
			// save reference to clicked element as a starting point for all traversal
			var that = this;
			
			// ajax call to fetch event data
			$.get($(this).data('url'), function(data) {
				event_data.html(data);
				
				// setup for jquery ui tabs
				event_data.find('#tabs').tabs();
				
				// mark event has having all data loaded
				$(that).addClass('loaded');
				
				$(that).toggleClass('event_collapsed event_expanded');
				
				event_data.slideDown('slow');
			});
		}
		else
		{
			// Toggle details display
			if ($(this).hasClass('event_collapsed'))
			{
				$(this).toggleClass('event_collapsed event_expanded');
				
				event_data.slideDown('slow');
			}
			else
			{
				var that = this;
				
				event_data.slideUp('slow', function() {
					$(that).toggleClass('event_collapsed event_expanded');
				});
			}
		}
	});
	
	$('.clickable > section').click(function(e) {
		e.stopPropagation();
	});

	
	// Handling dropdown event filter list
	$('#event_filters').change(function(e) {
		window.open(this.value, '_self');
	});
	
	// Dealing with named anchors on event list page
	if (window.location.hash != '')
	{
		var event_number = window.location.hash;
		
		$(event_number).click();
	}
	
});