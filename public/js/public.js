(function ( $ ) {
	"use strict";
	var wp             = window.wp,
		sa_item_block  = wp.template( "salud-recent-items-block" ),
		sa_ticker_item = wp.template( "salud-ticker-items-block" ),
		processing     = false,
		error          = false,
		term_slug      = '';

	// Go when the DOM is ready.
	$(function() {
		window.sa = window.sa || {}

		// Build the map widget at the right size.
		if ( typeof base_map_widget_src !== 'undefined' ) {
			var width      = jQuery( '#map-widget-container' ).width(),
				height     = 240,
				script_src = '',
				dimensions = '';

			width = Math.round( width );

			// Try to match the height to the height of the "join group" call to action,
			// when the two blocks appear side-by-side, and the second block exists.
			if ( jQuery( window ).width() > 599 && jQuery( '#sa-join-group-action-call' ).height() ) {
				height = jQuery( '#sa-join-group-action-call' ).height();
			} else if ( width > 500 ) {
				// Make a nicely proportioned window on wide screens.
				height = Math.round( width * 2 / 3 );
			}

			dimensions = '&w=' + width + '&h=' + height;

			// This is a hack. This widget should only be loaded at page load because it uses document.write().
			// We're loading these widgets asynchronously, so we have to overload doc.write.
			var widget_container = document.getElementById( 'map-widget-container' );
			if ( ! document._write ) {
				document._write = document.write;
			}
			document.write = function (str) {
				widget_container.innerHTML += str;
			};

			// Fetch the script with the correct arguments
			jQuery.ajax({
				url: base_map_widget_src + dimensions,
				dataType: "script",
				cache: true,
				crossDomain: true
			}).success(function( data, textStatus, jqxhr ) {
				// console.log( data ); // Data returned
				// console.log( textStatus ); // Success
				// console.log( jqxhr.status );
			});
		}

		// Handle requests for displaying recent items in a taxonomy term in the
		// recent items row.
		$( '#topic-toggle' ).on( 'click', 'a.toggle', function( event ){
			event.preventDefault();

			// Do not continue if we are currently fetching a set of results.
			if ( processing !== false ) {
				return;
			}
			processing = true;

			// Which term has been requested?
			term_slug = $( this ).attr( "id" );

			// If the content block already exists, we just refresh visibility.
			if ( $( '.recent-item-cell .entry-content.' + term_slug ).length ) {
				refreshVisibleItems( term_slug )
			} else {
				fetchRecentItems( term_slug );
			}

			processing = false;
		});

		// Hide the advanced section of the form on page load.
		if ( $( '#salud-search-advanced' ).length ) {
			// We leave it visible if any of the "more options" checkboxes are selected.
			// Hide if none are selected.
			if ( ! $( 'input[name="type[]"]:checked' ).length && ! $( 'input[name="topic[]"]:checked' ).length ) {
				$( '#salud-search-advanced' ).hide();
			}
		}

		// Toggle the advanced search part of the form.
		$( "#toggle-advanced-search" ).click(function( e ) {
			e.preventDefault();
			$( '#salud-search-advanced' ).toggle();
		});

		// Interrupt the advanced search form submission so we can clean up the input.
		$( "#salud-advanced-hub-search-submit" ).click(function( e ) {
			e.preventDefault();

			// Grab all the checked type checkboxes and concatenate them to a comma-separated string,
			// then set that value to the form's hidden input.
			var query_string = '';
			var counter = 1;
			$( 'input[name="type[]"]:checked' ).each(function() {
				if ( counter > 1 ) {
					query_string += ',';
				}
				query_string += $( this ).val();
				counter++;
			});

			// Set the value if one exists.
			if ( query_string ) {
				$( '#salud-hub-advanced-search #type' ).val( query_string );
			} else {
				// If no value, disable the input so that the url is cleaner.
				$( '#salud-hub-advanced-search #type' ).prop( 'disabled', true );
			}

			// Same routine for topics
			query_string = '';
			counter = 1;
			$( 'input[name="topic[]"]:checked' ).each(function() {
				if ( counter > 1 ) {
					query_string += ',';
				}
				query_string += $( this ).val();
				counter++;
			});

			// Set the value if one exists.
			if ( query_string ) {
			$( '#salud-hub-advanced-search #topic' ).val( query_string );
			} else {
				// If no value, disable the input so that the url is cleaner.
				$( '#salud-hub-advanced-search #topic' ).prop( 'disabled', true );
			}

			// Submit the form.
			$( '#salud-hub-advanced-search' ).submit();
		});

		// If the ticker marquee exists on this page, populate it.
		if ( $( '#sa-ticker-marquee' ).length ) {
			tickerInit();
			$('#sa-ticker-marquee').hover(
				function() { //mouse enter
					$(this).addClass('pause-ticker');
				},
				function() { //mouse leave
					$(this).removeClass('pause-ticker');
				}
			);
		}
	});

	/**
	 * Initiates the AJAX request to fetch the requested posts.
	 * Data from successful requests is passed to the success callback: addRecentItem()
	 *
	 * @param string term_slug the slug of the requested taxonomy term.
	 */
	function fetchRecentItems( term_slug ) {
		// Add some user feedback once a request starts.
		$( '#' + term_slug + ' .working-indicator' ).addClass( 'ajax-loading' );

			wp.ajax.send(
				"cc_sa_get_recent_items", {
				success: addRecentItem,
				error:   function() { return ''; },
				data: {
				  advo_target: term_slug,
				  exclude_ids: $( '#sa-recent-items-exclude-ids' ).val()
				}
		});
	}

	/**
	 * Create the requested blocks.
	 * Accepts the data object of a successful request and applies the JS template to the data.
	 *
	 * @param obj data The data object returned by sa_get_most_recent_items_by_big_bet().
	 */
	function addRecentItem( data ) {
		var exclude_ids = $( '#sa-recent-items-exclude-ids' ).val().split(',');

		// Build the new blocks and insert them.
		$.each( data.posts, function( key, post_data ) {
			$( '.recent-item-cell.' + key + ' .entry-footer' ).before( sa_item_block( post_data ) );
			exclude_ids.push( post_data.post_id );
		});

		// Hide the visible blocks and show the new blocks.
		refreshVisibleItems( data.term_slug );

		// Add the newly fetched post IDs to the "exclude_ids" input.
		$( '#sa-recent-items-exclude-ids' ).val( exclude_ids.toString() );
	}

	/**
	 * Show the blocks for the requested term. (Hide the others.)
	 *
	 * @param string term_slug the slug of the requested taxonomy term.
	 */
	function refreshVisibleItems( term_slug ) {
		// Fade out the currently visible blocks and fade in the new blocks.
		$( '.recent-item-cell .entry-content' ).filter( ':visible' ).fadeOut( 'fast', function() {
			$( '.recent-item-cell .entry-content.' + term_slug ).fadeIn('fast');
		});

		// Hide the loading indicator.
		// This is kind of a weird place for this, but it covers some edge cases.
		$( '#' + term_slug + ' .working-indicator.ajax-loading' ).removeClass( 'ajax-loading' );

		// Add the "active" class to the currently selected advocacy target.
		$( '.topic-selector a.active' ).removeClass('active');
		$( '.topic-selector a#' + term_slug ).addClass('active');
	}

	/**
	 * Initiates the SA ticker.
	 * Data from successful requests is passed to the success callback: addRecentItem()
	 *
	 * @param string term_slug the slug of the requested taxonomy term.
	 */
	function tickerInit() {
		$.ajax( {
			url: '/wp-json/wp/v2/sa_ticker_items?filter[posts_per_page]=6',
			cache: false
		} ).done(
			function( data ){
				$.each( data, function( index, data ) {
					$( '#sa-ticker-marquee' ).append( sa_ticker_item( data ) );
				});
				// Begin the animation.
				$( '.sa-ticker-placeholder' ).slideUp( function() {
					$( this ).remove();
				} );
				setInterval( function() { scrollTicker() }, 8000 );
			}
		);
	}

	/**
	 * Show the most recent ticker items, if the ticker container exists.
	 *
	 * @param string term_slug the slug of the requested taxonomy term.
	 */
	function scrollTicker() {
		// Only scroll if the user isn't hovering over the item.
		if ( ! $( '#sa-ticker-marquee' ).hasClass( 'pause-ticker' ) ) {
			$( '#sa-ticker-marquee li:first' ).slideUp( function() {
				$( this ).appendTo( $( '#sa-ticker-marquee' ) ).slideDown();
			});
		}
	}

}(jQuery));