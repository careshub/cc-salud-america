<?php
$custom_fields = get_post_meta( get_the_ID() );
// echo '<pre>'; print_r( $custom_fields ); echo '</pre>';
$is_active = sa_video_contest_is_active( get_the_ID() );
$user_can_vote = sa_video_contest_current_user_can_vote( get_the_ID() );
// Has the user already voted?
$user_vote = sa_video_contest_get_current_user_vote( get_the_ID() );

$end_date = sa_convert_to_human_date( $custom_fields['sa_expiry_date'][0] );

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
        <form action="" method="POST" enctype="multipart/form-data" name="salud-america-video-contest-signup">
            <p style="margin-bottom:0.6em;"><strong>Join the hub <em>Salud America!</em></strong></p>
            <label style="margin-bottom:0.6em;"><input type="checkbox" name="join_salud_america_hub" id="join_salud_america_hub" value="agreed" checked="checked" /> Yes, I&rsquo;m interested in work by Salud America! to reduce Latino childhood obesity.</label><br />
            <label><input type="checkbox" name="salud_newsletter_acceptance" id="salud_newsletter_acceptance" value="agreed" checked="checked" /> I would like to receive email updates on this topic.</label>
            <p class="info" style="margin-bottom:0.6em;"><em>Periodically, Salud America! sends out news updates and brief surveys.</em></p>
            <?php
            $nonce_value = 'sa_video_contest_join_submit_' . get_current_user_id();
            wp_nonce_field( 'sa_video_contest_join_submit', $nonce_value );
            ?>
            <input type="hidden" id="sa_video_contest_submit_referrer" name="sa_video_contest_submit_referrer" value="<?php echo ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
            <input type="submit" id="sa_video_contest_join_submit" name="sa_video_contest_join_submit" alt="Join Salud America!" value="Join Salud America!" />
        </form>
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
        <?php // The content is the description of the contest in this case.
        the_content();

        // If the contest is active, then we present the videos in order 1-6.
        if ( $is_active ) {
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
                    $nonce_value = 'sa_video_contest_vote_' . get_the_ID() . '_' . get_current_user_id();
                    wp_nonce_field( 'sa_video_contest_vote', $nonce_value );
                    ?>
                    <input type="hidden" id="sa_video_contest_submit_referrer" name="sa_video_contest_submit_referrer" value="<?php echo ( is_ssl() ? 'https://' : 'http://' ) .  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
                    <input type="submit"  id="sa_video_contest_submit_vote" name="sa_video_contest_submit_vote" alt="Submit Your Vote" value="Submit Your Vote" class="spacious aligncenter"/>
                </form>
                <?php
            } // End $user_can_vote
        } else { // $is_active is false
            // If the contest is finished, show the winner first
            $vote_results = sa_video_contest_count_votes( get_the_ID() );
            $first_video = true;
            $open_runner_up_div = true;
            $close_runner_up_div = false;
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
                                    <h4>WINNER: <?php echo $video_title; ?></h4>
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
        } // End $is_active check

        ?>
    </div><!-- .entry-content -->
    <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
</article><!-- #post -->
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#sa_video_contest_submit_vote").prop('disabled', true);
        jQuery('input[name="sa_video_contest_selection"]').on( 'change', function(){
            jQuery("#sa_video_contest_submit_vote").prop('disabled', false);
        });
    });
</script>