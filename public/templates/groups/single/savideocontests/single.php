<?php
$post_id = get_the_ID();
$custom_fields = get_post_meta( $post_id );
// echo '<pre>'; print_r( $custom_fields ); echo '</pre>';
$is_active = sa_video_contest_is_active( $post_id );
$user_can_vote = sa_video_contest_current_user_can_vote( $post_id );
// Has the user already voted?
$user_vote = sa_video_contest_get_current_user_vote( $post_id );

$end_date = sa_convert_to_human_date( $custom_fields['sa_expiry_date'][0] );

// Show the whole post, not just the excerpt.
global $more;
$more = 1;
// echo '<pre>'; var_dump( get_post_meta( get_the_ID(), 'sa_video_contest_votes', true ) ); echo '</pre>';

$post_class = 'sa-video-contest';
if ( $is_active ) {
    $post_class .= ' active-contest';
}

// If it is still fresh and the current user can vote, make the contest actionable, otherwise, make it a good show. All others display as links.
// If it is not active, show the winner first, then the others.
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php
// First, we'll need to provide an info box to help the visitor register/log in and join the group.
$user_id = get_current_user_id();
$is_sa_member = false;
if ( $user_id && groups_is_user_member( $user_id, sa_get_group_id() ) ) {
    $is_sa_member = true;
}
if ( $is_active && ( ! $user_id || ! $is_sa_member ) ) {
?>
    <div class="info" id="message">
<?php
    if ( ! $user_id ) {
        // User isn't logged in.
        ?>
        <p>To vote, you must <a class="login-link" href="<?php echo wp_login_url( ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'] ); ?>" title="Log in">log in</a>. If you don't have a Community Commons account and would like to join us, please <a href="<?php echo site_url( bp_get_signup_slug() . '?salud-america=1' ); ?>">register</a>.</p>
        <?php
    } else {
        // User doesn't belong to the SA group.
        ?>
        <p style="margin-bottom:0.6em;">You must be registered with Salud America before you can vote.</p>
        <?php echo sa_get_auxiliary_signup_form(); ?>
        <?php
    }
?>
    </div> <!-- .info -->
<?php
}
?>
    <div class="entry-content">
        <header class="entry-header clear">
            <h3 class="entry-title screamer saorange"><?php the_title(); ?></h3>
            <?php
            if ( $is_active ) {
                ?>
                <p class="meta">This contest runs until <?php echo $end_date; ?>.</p>
                <?php
            } else {
                ?>
                <p class="meta">This contest ended <?php echo $end_date; ?>.</p>
                <?php
            }
            salud_the_target_icons(); ?>
        </header>
        <div class="video-contest-intro-text">
            <?php // The content is the description of the contest in this case.
            the_content();
            ?>
        </div>
        <?php

        // If the contest is active, then we present the videos in order 1-6.
        if ( $is_active ) {
            // Add the contest rules.
            ?>
            <div id="video-contest-rules">
                <a href="#" class="button toggle">Click for Contest Rules</a>
                <p class="rules">
                    This contest is open to everyone (except <em>Salud America!</em> staff or grantees). The contest begins on <?php echo get_the_date(); ?>, and ends at 11:59 p.m. CST on <?php echo $end_date; ?>. To enter, individuals must first register with the <em>Salud America!</em> website, and then click to vote for their favorite video among the potential choices. Each registered user may cast only one vote. Casting a vote enters the registered user into a drawing for a T-shirt and jump rope package. The drawing&rsquo;s winner will be notified via email. The winner must contact us directly at <a href="mailto:saludamerica@uthscsa.edu">saludamerica@uthscsa.edu</a> to claim their prize package. Entry into drawing is subject to all applicable laws and regulations.

                </p>
            </div>

            <div class="video-contest-container clear">
            <?php

            // Add the voting form wrapper and controls if the user can vote
            if ( $user_can_vote ) {
                ?>
                <form action="" method="POST" enctype="multipart/form-data" name="video_contest_ballot">
                    <?php
                } // End $user_can_vote
                    // Display the videos
                    for ( $i = 1; $i < 7 ; $i++ ) {
                        if ( ! empty( $custom_fields[ 'sa_video_contest_title_' . $i ][0] ) && ! empty( $custom_fields[ 'sa_video_contest_url_' . $i ][0] ) ) {
                            $video_title = apply_filters( 'the_title', $custom_fields[ 'sa_video_contest_title_' . $i ][0] );
                            $video_embed_code = wp_oembed_get( $custom_fields[ 'sa_video_contest_url_' . $i ][0] );
                            if ( $video_embed_code ) {
                                $selected_class = ( $user_vote == $i ) ? ' selected-video' : '';
                                ?>
                                <div class="half-block compact<?php echo $selected_class; ?>">
                                    <h4><?php echo $video_title; ?></h4>
                                    <div class="video-container-group clear">
                                        <figure class="video-container">
                                            <?php echo $video_embed_code; ?>
                                        </figure>
                                        <?php if ( $user_can_vote ) { ?>
                                            <div class="input-container">
                                                <input type="radio" value="<?php echo $i; ?>" id="radio-input-<?php echo $i; ?>" name="sa_video_contest_selection"><label for="radio-input-<?php echo $i; ?>" class="fancy-checkbox">&ensp;Choose this video</label>
                                            </div>
                                        <?php } elseif ( $user_vote == $i ) { ?>
                                            <figcaption>Your selection. Good luck!</figcaption>
                                        <?php } // End $user_can_vote/has_voted ?>
                                    </div>
                                </div>
                                <?php
                            } // End if $video_embed_code

                        }
                    }
            if ( $user_can_vote ) {
            ?>
                    <input type="hidden" name="video_contest_id" value="<?php the_ID(); ?>" />
                    <?php
                    $nonce_value = 'sa_video_contest_vote_' . $post_id . '_' . get_current_user_id();
                    wp_nonce_field( 'sa_video_contest_vote', $nonce_value );
                    ?>
                    <input type="hidden" id="sa_video_contest_submit_referrer" name="sa_video_contest_submit_referrer" value="<?php echo ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
                    <input type="submit"  id="sa_video_contest_submit_vote" name="sa_video_contest_submit_vote" alt="Submit Your Vote" value="Submit Your Vote" class="spacious aligncenter"/>
                </form>
                <?php
            } // End $user_can_vote
            ?>
            </div> <!-- .video-contest-container -->
            <?php
        } else { // $is_active is false
            // If the contest is finished, show the winner first
            $vote_results = sa_video_contest_count_votes( $post_id );
            $first_video = true;
            $open_runner_up_div = true;
            $close_runner_up_div = false;
            ?>
            <div class="video-contest-container">
            <?php
            foreach ( $vote_results as $i => $num_votes ) {
                if ( ! empty( $custom_fields[ 'sa_video_contest_title_' . $i ][0] ) && ! empty( $custom_fields[ 'sa_video_contest_url_' . $i ][0] ) ) {
                        $video_title = apply_filters( 'the_title', $custom_fields[ 'sa_video_contest_title_' . $i ][0] );
                        $video_embed_code = wp_oembed_get( $custom_fields[ 'sa_video_contest_url_' . $i ][0] );
                        if ( $video_embed_code ) {
                            if ( $first_video ) {
                                // This is the winning video
                                $first_video = false;
                                $second_video = true;
                                ?>
                                <div class="winning-video">
                                    <h4 class="winning-video">WINNER: <?php echo $video_title; ?></h4>
                                    <div class="video-container-group">
                                        <figure class="video-container">
                                            <?php echo $video_embed_code; ?>
                                        </figure>
                                    </div>
                                </div>
                                <?php
                            } else {
                                if ( $open_runner_up_div ){
                                    $open_runner_up_div = false;
                                    $close_runner_up_div = true;
                                    echo '<div class="row">';
                                }
                                ?>
                                <div class="half-block compact<?php echo $selected_class; ?>">
                                    <h4><?php echo $video_title; ?></h4>
                                    <div class="video-container-group">
                                        <figure class="video-container">
                                            <?php echo $video_embed_code; ?>
                                        </figure>
                                        <?php if ( $user_can_vote ) { ?>
                                            <label class="aligncenter"><input type="radio" value="<?php echo $i; ?>" name="sa_video_contest_selection">&ensp;Choose this video</label>
                                        <?php } elseif ( $user_vote == $i ) { ?>
                                            <figcaption>Your selection. Good luck!</figcaption>
                                        <?php } // End $user_can_vote/has_voted ?>
                                    </div>
                                </div>
                                <?php
                            } // not the first video
                        } // End if $video_embed_code
                } // End check for empty attributes
            }
            if ( $close_runner_up_div ) {
                echo '</div>'; // Close the div we opened after the first video
            }
            ?>
            </div> <!-- .video-contest-container -->
            <?php
        } // End $is_active check
        // Show other contests in a list.
            $videos = new WP_Query( array(
                'post_type' => 'sa_video_contest' ,
                'posts_per_page' => -1,
                'posts__not_in' => array( $post_id ),

            ) );
            if ( $videos->have_posts() ) :
                ?>
                <div class="other-video-contests clear">
                    <h5>Check Out Our Other Video Contests!</h5>
                    <ul class="previous-video-contests no-bullets compact">
                    <?php
                    while ( $videos->have_posts() ) : $videos->the_post();
                    ?>
                        <li><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></li>
                    <?php
                    endwhile;
                    ?>
                    </ul>
                </div>
                <?php
            endif;
            ?>
    </div><!-- .entry-content -->
    <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', $post_id ); ?>
</article><!-- #post -->
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#sa_video_contest_submit_vote").prop('disabled', true);
        jQuery('input[name="sa_video_contest_selection"]').on( 'change', function(){
            jQuery("#sa_video_contest_submit_vote").prop('disabled', false);
        });
    });
</script>
