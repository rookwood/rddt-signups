/* Author: 

*/

$(document).ready(function() {
	$('a.event_details').click(function(e) {
		e.preventDefault();
		console.log('click');
		
		var that = this;
		var event_data = $('#event_data');
		
		event_data.fadeOut(1000, function() {
			$.get(that.href, function(data) {
				event_data.html(data).delay(500).fadeIn(1000);
			});
		});
	});
	
	$('#event_filters').change(function(e) {
		window.open(this.value, '_self');
	});
});