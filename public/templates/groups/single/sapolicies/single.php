<?php
/**
 * Used to display "Changes"--the sapolicies post type.
 */
$user_id = get_current_user_id();
$is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );

$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post();
    $main_post_id = get_the_ID();
    $post_meta = get_post_custom( $main_post_id );

    // Set up the featured video.
    $video_url = '';
    if ( ! empty( $post_meta['sa_featured_video_url'] ) ) {
        $video_url = current( $post_meta['sa_featured_video_url'] );
    }
    $video_embed_code = '';
    if ( ! empty( $video_url ) ) {
        $video_embed_code = wp_oembed_get( $video_url );
    }
    ?>
    <article id="post-<?php echo $main_post_id; ?>" <?php post_class( 'clear main-article' ); ?>>
        <div class="entry-content clear">
            <header class="entry-header clear">
                <h1 class="entry-title screamer sablue"><?php the_title(); ?></h1>
                <?php sa_single_post_header_meta( $main_post_id ); ?>
            </header>

            <?php // Featured Image and indicator dials ?>
            <div class="sa-featured-image-container">
                <?php
                // Show the featured video if one exists.
                if ( ! empty( $video_embed_code ) ) { ?>
                    <figure class="video-container">
                        <?php echo $video_embed_code; ?>
                    </figure>
                    <?php
                } else {
                    // Else show the post's featured image or a fallback.
                    ?>
                        <?php
                        //First, show the thumbnail or the fallback image.
                        if ( has_post_thumbnail() ) {
                            $thumbnail_id = get_post_thumbnail_id();
                            ?>
                            <div id="attachment_<?php echo $thumbnail_id; ?>" class="wp-caption">
                                <?php the_post_thumbnail( 'feature-front' ); ?>
                                <p class="wp-caption-text"><?php echo get_post( $thumbnail_id )->post_excerpt; ?></p>
                            </div>
                            <?php
                        } else {
                            echo sa_get_advo_target_fallback_image_for_post( $main_post_id, 'feature-front' );
                        }
                }

                // Next we show the dials for this region.
                // @TODO: this could be deferred, pending some changes to the JSON requests/returns.
                $geo_terms = get_the_terms( $main_post_id, 'geographies' );
                // Get the GeoID if possible, else use the whole US
                $geoid = ( ! empty( $geo_terms ) ) ? current( $geo_terms )->description : '01000US';

                if ( $geoid && '01000US' != $geoid ) : ?>
                    <div class="indicator-dials horizontal aligncenter">
                    <h5 style="margin-bottom:0;">Area at a glance</h5>
                        <!-- Default dial -->
                        <div id="dial-779" class="dial-container">
                            <script src='http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=<?php echo $geoid; ?>&id=779'></script>
                        </div>
                        <!-- Placeholders for other dials -->
                        <div id="dial-781" class="dial-container" style="display: none;">
                            <script src='http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=<?php echo $geoid; ?>&id=781'></script>
                        </div>
                        <div id="dial-760" class="dial-container" style="display: none;">
                            <script src='http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=<?php echo $geoid; ?>&id=760'></script>
                        </div>
                        <!-- Dial display controls -->
                        <input type="button" class="dial-controls" value="Poverty rate" data-indicator-id='779' />
                        <input type="button" class="dial-controls" value="Children in Poverty" data-indicator-id='781' />
                        <input type="button" class="dial-controls" value="Pop. With No HS Diploma" data-indicator-id='760' />
                    </div>
                <?php endif; ?>
            </div>

            <?php sa_post_date_author( $main_post_id, 'p' ); ?>

            <?php the_content(); ?>

            <?php
                if ( function_exists('cc_add_comment_button') ) {
                    cc_add_comment_button();
                }

                if ( function_exists('bp_share_post_button') ) {
                    bp_share_post_button();
                }
            ?>

            <?php /* ?>
            <?php if ( ! empty( $post_meta['sa_policytype'][0] ) ) { ?>
                <p class="sa-policy-meta">This change is of the type:
                    <?php echo $post_meta['sa_policytype'][0]; ?>
                </p>
            <?php } ?>
            <?php */ ?>

         </div><!-- .entry-content -->

        <footer class="entry-meta clear">
            <?php
            sa_post_terms_meta( $main_post_id, 'sapolicies' );
            cc_the_policy_progress_tracker( $post_meta['sa_policystage'][0] );
            edit_post_link( 'Edit This Post', '<span class="edit-link">', '</span>', $main_post_id );
            ?>
        </footer>

    </article><!-- #post -->
<?php
comments_template();
endwhile; // end of the loop. ?>

<script type="text/javascript">
    var geoid = '<?php echo $geoid; ?>';
    // var s = document.createElement("script");
    // s.type = "text/javascript";
    // s.src = "http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=" + geoid + "&id=" + id;


    // function changeDial(id) {
    //     var dial = document.getElementById('dial');
    //     if (!document._write) document._write = document.write;
    //     document.write = function (str) {
    //         dial.innerHTML += str;
    //     };

    //     while (dial.firstChild) {  dial.removeChild(dial.firstChild); }
    //     dial.appendChild(s);
    // }

    jQuery( document ).ready(function( $ ) {
        $( '.dial-controls' ).click(function( e ) {
            var indicator_id = jQuery( this ).data( 'indicator-id' );
            var target_div_id = 'dial-' + indicator_id;
            var target_div_empty = jQuery( '#' + target_div_id ).html() ? false : true;

            console.log( "Handler for .click() called." );
            console.log( "id is: " + indicator_id );
            console.log( "target_div_empty is: " + target_div_empty );
            console.log( "geoid is: " + geoid );

            // This doesn't work as is.
            // if ( target_div_empty ) {
            //     // If the target div is empty, populate it.

            //     // This is a hack. This widget should only be loaded at page load because it uses document.write().
            //     // We're loading these widgets asynchronously, so we have to overload doc.write.
            //     var dial_container = document.getElementById( target_div_id );
            //     console.log( "dial_container is: " );
            //     console.log( dial_container );

            //     if ( ! document._write ) {
            //         document._write = document.write;
            //     }
            //     document.write = function (str) {
            //         dial_container.innerHTML += str;
            //     };

            //     console.log( "target url: http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=" + geoid + "&id=" + indicator_id );

            //     // Fetch the widget
            //     // var s = document.createElement("script");
            //     // s.type = "text/javascript";
            //     // s.src = "http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=" + geoid + "&id=" + indicator_id;
            //     jQuery.ajax({
            //           url: "http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=" + geoid + "&id=" + indicator_id,
            //           dataType: "script",
            //           cache: true,
            //           crossDomain: true
            //     }).success(function( data, textStatus, jqxhr ) {
            //         // console.log( data ); // Data returned
            //         // console.log( textStatus ); // Success
            //         // console.log( jqxhr.status );
            //     });

            // }

            // Next, hide all of the dial containers then show the requested div.
            $( '.dial-container' ).hide();
            $( '#' + target_div_id ).show();

        });
    });
</script>

