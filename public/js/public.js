(function ( $ ) {
	"use strict";
	var advo_target = '';

	// Go when the DOM is ready
	$(function() {
		window.sa = window.sa || {}

		// Make a request and fill the boxes

		$( '#topic-toggle' ).on( 'click', 'a.toggle', function(){
			advo_target = $( this ).attr( "id" );
			get_recent_items( advo_target );
		});
	});

	function get_recent_items( advo_target ) {
		console.log( advo_target );
			wp.ajax.send(
			"cc_sa_get_recent_items", {
				success: refreshRecentItemContent,
				error:   function(){ return ''; },
				data: {
				  // nonce: window.ccgpAJAXNonce,
				  // group_id: $( '#group-id' ).val(),
				  advo_target: advo_target
				}
		});
	}

	function refreshRecentItemContent( data ) {

	}

}(jQuery));

// <script type="text/html" id="tmpl-sa-adv-target-recent-content">
// 	<img class="" src="">
// 	<p class="intro-text"><p>
// 	<a href="" class="button"></a>
// </script>