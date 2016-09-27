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

		// Toggle contest rules on video contest pages.
		// Hide on page load
		$( '#video-contest-rules .rules' ).toggle();
		// Toggle on click
		$( '#video-contest-rules .toggle' ).on( 'click', function(e){
			e.preventDefault();
			$( '#video-contest-rules .rules' ).slideToggle( 'fast' );
		} );

		// Handle controls for spanish/english videos
        $( ".video-container-group.bilingual-video" ).each( function(){
            // Default to the english language version if it exists.
            if ( $( this ).find( ".video-in-english" ).length ) {
                $( this ).find( ".video-in-spanish" ).hide();
            }
        });

        $( ".show-english-video, .show-spanish-video" ).on( "click", function(){
            $( this ).parents( ".bilingual-video" ).find( ".video-in-spanish, .video-in-english" ).toggle();
        });

	    /**************** Report Card **********************/
        var apiHost = "http://services.communitycommons.org/";

        // ajax call to get geoid list
        var getGeoidList = function (api_param, callback) {
            $.ajax({
                type: "get",
                url: apiHost + "api-location/v1/geoid-list/" + api_param,
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                crossDomain: true,
                success: callback,
                error: function (err) {
                    console.log(err);
                }
            });
        };

        // Populate state list
        if ($("#state-list").length) {
            getGeoidList("040", function (data) {
                // append new items -- exclude territories since we don't have data to generate report for those
                $.each(data, function (i, v) {
                    var stateCode = v.geoid.substr(v.geoid.length - 2);
                    if (parseInt(stateCode) < 60) {
                        $("#state-list").append($("<option></option>").val(v.geoid).html(v.name));
                    }
                });
            });
        } else {
            // KISS tracking for loading a report card -- either directly from shared link or this tool
            var geoid = $("#report-card-geoid").length? $("#report-card-geoid").val(): "";
            if (geoid !== "") {
                _kmq.push(['record', 'opened a salud report card', { 'geoid': geoid }]);
            }

            // KISS tracking for clicking various links on the report card
            $.each([".big-bet-icon-link", ".learn-more-link", ".salud-report-card-link"], function (i, v) {
                $(v).on("click", function () {
                    //console.log($(this).attr("title"));
                    _kmq.push(['record', 'clicked link on salud report card', { 'geoid': geoid, 'link': $(this).attr("title") }]);
                });
            });
        }

	    // Handle state-list selection for report card
        $("#state-list").on("change", function () {
            var state = $("#state-list option:selected").text();

            getGeoidList("050?state=" + state, function (data) {
                $("#county-list").show();

                // remove any previous county list
                $("#county-list option").each(function () {
                    if ($(this).val() !== "") $(this).remove();
                });

                // append new counties
                $.each(data, function (i, v) {
                    $("#county-list").append($("<option></option>").val(v.geoid).html(v.name));
                });
            });
        });

	    // Handle county selection for report card
        $("#county-list").on("change", function () {
            var countyGeoId = $("#county-list").val();
            if (countyGeoId !== "") {
                $("#report-wait-message").show();

                // load report card
                document.location = document.location.href.split("?")[0] + "?geoid=" + countyGeoId;
            }
        });

	    //Handle PDF export for report card
        var popup;
        $(".sa-report-export").on("click", function () {
            var pageFont = "https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&#038;subset=latin,latin-ext";

            // local methods - dependent on popup window being set
            // ** display a message
            var popupMsg = function (s) {
                var msg = "<link type='text/css' rel='stylesheet' href='" + pageFont + "' />";
                msg += "<div style='font-family: Open Sans, Helvetica, Arial, sans-serif; text-align:center; line-height: 250%;'>";
                msg += "<h2>" + s + "</h2>";
                msg += "</div>";
                popup.document.body.innerHTML = msg;
            }
            // ** move the popup window to screen center
            var popupCenter = function(width, height) {
                    popup.resizeTo(width, height);
                    popup.moveTo((screen.width - width) / 2, (screen.height - height) / 2);
            };

            // open a new window for displaying PDF document later - set popup as global variable
            if (typeof popup !== "undefined" && popup.location) {
                popup.close();
            }
            popup = window.open("", "newwin", "width=500,height=300");
            popupMsg("Converting report card to Adobe PDF format. Please wait...");
            popupCenter(550, 400);

            // compile html contents
            var htmContent = "<!DOCTYPE html><html><head>";

            // add font and css files
            var hostname = location.protocol;
            hostname += "//" + document.location.hostname + "/";
            $.each([pageFont,
                    hostname + "wp-content/themes/twentytwelve/style.css",
                    hostname + "wp-content/themes/CommonsRetheme/style.css",
                    hostname + "wp-content/plugins/cc-salud-america/public/css/sa-report-card.css"], function (i, css_file) {
                    htmContent += "<link type='text/css' rel='stylesheet' href='" + css_file + "' />";
                });

            // add report content
            var contentId = "sa-report-content";
            var reportContent = $("#"+contentId).html();
            reportContent = reportContent.replace(/src=("|')\//g, 'src="' + hostname);         // append hostname to any relative links
            htmContent += "</head><body class='custom-font-enabled'>" +
                "<div id='" + contentId + "'>" + reportContent + "</div></body></html>";

            // post to the converter API -- logo_list must point to images saved on API server as HiQPdf can only add local images to header/footer
            var post = {
                "content": htmContent,
                "pdf": {
                    "file_name": "SaludReportCard",
                    "show_pagenumber": true,
                    "is_landscape": false,
                    "page_width": 770,          // change to remove extra blank page
                    "logo_list": [apiHost + "assets/images/logo-salud.png",
                        apiHost + "assets/images/logo-rwjf.png"]
                }
            };

            var errMsg = "Conversion to PDF failed. Please try again or contact us.";
            $.ajax({
                type: "POST",
                url: apiHost + "api-converter/v1/htm2pdf/content",
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                crossDomain: true,
                data: JSON.stringify(post),
                success: function (pdfUrl) {
                    if (pdfUrl) {
                        popupMsg("Loading PDF File...");
                        popup.location = pdfUrl;

                        // resize and move PDF popup to screen center
                        popupCenter(1000, screen.height);
                    } else {
                        popupMsg(errMsg);
                    }
                },
                error: function (err) {
                    console.log(err);
                    popupMsg(errMsg);
                }
            });

            // KISS tracking
            _kmq.push(['record', 'exported a salud report card to pdf', {
                'geoid': $("#report-card-geoid").val(),
                'county': $("#report-card-county").val(),
                'state': $("#report-card-state").val()
            }]);
        });

	    // Handle Save Report Card
        $(".sa-report-save").on("click", function () {
            // show a message
            $(".report-save-message").show();

            // post to WP db
            var geoid = $("#report-card-geoid").val();
            var county = $("#report-card-county").val();
            var state = $("#report-card-state").val();
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                data: {
                    'action': 'save-leader-report-as-doc',
                    'geoid': geoid,
                    'county': county,
                    'state': state,
                    '_wpnonce': $("#report-card-wpnonce").val()
                },
                success: function (response) {
                    // Do something. response.data is the new post ID.
                    // console.log(response.data);
                    $(".report-save-message").html("This report card has been saved to your personal library.");
                    $(".sa-report-save").hide();
                },
                error: function (response) {
                    // Do something. response.data is the new post ID.
                    // console.log(response.data);
                    $(".report-save-message").html("Unable to save this report card.");
                }
            });

            // KISS tracking
            _kmq.push(['record', 'saved a salud report card to library', {
                'geoid': geoid,
                'county': county,
                'state': state
            }]);
        });
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
				setInterval( function() { scrollTicker() }, 5000 );
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