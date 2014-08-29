(function($) {
	$(function() {
		
		// Check to make sure the input box exists
		if( 0 < $('#start_date').length ) {
			$('#start_date').datepicker({
                showButtonPanel: true,
                dateFormat: "dd M yyyy",
                defaultDate: new Date( $('#start_date').val() ),
                changeYear: true,
                changeMonth: true
            });
		} // end if
		// Check to make sure the input box exists
		if( 0 < $('#end_date').length ) {
			$('#end_date').datepicker({
                showButtonPanel: true,
                dateFormat: "dd M yyyy",
                defaultDate: new Date( $('#end_date').val() ),
                changeYear: true,
                changeMonth: true
            });
		} // end if
		
	});
}(jQuery));