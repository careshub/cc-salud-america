Report for <span id="location-county">Broomfield County</span>, <span id="location-state">Colorado</span>.
<input type="hidden" id="location-geoid" value="05000US08014">
<button id="save-report">Save that report!</button>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Perform AJAX login on form submit
		$('#save-report').on('click', function(e){
			save_report();
		});
	});

	function save_report(){
		jQuery.ajax({
			type: 'POST',
			url: '/wp-admin/admin-ajax.php',
			data: {
				'action': 'save-leader-report-as-doc',
				'geoid': jQuery( "#location-geoid" ).val(),
				'county': jQuery( "#location-county" ).text(),
				'state': jQuery( "#location-state" ).text(),
				'_wpnonce': "<?php echo wp_create_nonce( 'save-leader-report-' . bp_loggedin_user_id() ) ?>",
			},
			success: function( response ){
				// Do something. response.data is the new post ID.
				console.log( response.data );
			},
			error: function( response ){
				// Do something. response.data is the new post ID.
				console.log( response.data );
			}
		});
	}
</script>