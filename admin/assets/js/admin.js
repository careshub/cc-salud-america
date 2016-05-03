(function ( $ ) {
	"use strict";
	var wp             = window.wp,
		sa_ticker_item = wp.template( "salud-ticker-items-block" ),
		tickerData     = new Object();

	$(function () {
		// Add color picker to the ticker item
	    if ( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ) {
	        jQuery( '#sa_ticker_item_leader_color' ).wpColorPicker({
	        	// Supply the SA colors as presets.
	        	palettes: ['#ef4036', '#F48120', '#FDB913', '#EB008B', '#92278F', '#60b035', '#72BF44', '#0088CF'],
	        	change: function( event, ui ) {
							var hexcolor = $( this ).wpColorPicker( 'color' );
							$( "#sa-ticker-marquee .sa-ticker-item-type-label" ).css( "background-color", hexcolor );
						}
	        	});
	    }
	    // Create/update ticker item preview.
	    updateTicker();
	    $( "#post :input" ).on( "change", updateTicker );
	});

	function updateTicker() {
		// Fake the data object.
		tickerData.id = 'ID';
		tickerData.sa_ticker_item_leader_color = $( "#sa_ticker_item_leader_color" ).val();
		tickerData.sa_ticker_item_leader_text = $( "#sa_ticker_item_leader_text" ).val();
		tickerData.nice_date = $( "#timestamp b" ).text().split(",")[0];
		tickerData.title = new Object();
		tickerData.title.rendered = $( "#title" ).val();
		if ( $( "#sa_ticker_item_link" ).val().length ) {
			tickerData.sa_ticker_item_link = '#';
		} else {
			tickerData.sa_ticker_item_link = '';
		}

	    // Update the item.
	    $( '#sa-ticker-marquee' ).empty();
	    $( '#sa-ticker-marquee' ).append( sa_ticker_item( tickerData ) );
	}

}(jQuery));