(function($) {
	$(function() {
		
		// Check to make sure the input box exists
		if( 0 < $('#start_date').length ) {
			$('#start_date').datepicker({
                dateFormat: "d MM yy"
            });
		} // end if
		// Check to make sure the input box exists
		if( 0 < $('#end_date').length ) {
			$('#end_date').datepicker({
                dateFormat: "d MM yy"
            });
		} // end if
		
	});
}(jQuery));