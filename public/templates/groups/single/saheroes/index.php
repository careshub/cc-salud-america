<?php
/**
* Template used for displaying the heroes tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
// Should we display the video archive?
$archive_style = ( isset( $_GET['style'] ) && $_GET['style'] == 'videos'  ) ? 'videos' : '';


if ( sa_is_section_front() ) {

    if ( $archive_style == 'videos') {
        // THink this could go away, but leaving here just in case.
        ?>
        <h3 class="screamer sablue">Salud Heroes Video Archive</h3>
        <?php
        bp_get_template_part( 'groups/single/saheroes/hero-loop-video' );

    } else {
        // Show the top section on the front page only.
        if ( $paged == 1 ) {
        ?>
        <div class="content-row clear">
            <div class="third-block spans-2">
                <h2 class="screamer sablue no-top-margin">New Salud Heroes!</h2>

                <p><strong>Salud Heroes are champions of healthy change.</strong><p>

                <p>They are people like you&mdash;children, parents, teachers, health workers&mdash;who learn of childhood obesity, get an idea to do something about it, mobilize support, and drive policy and system changes in schools and communities.</p>

                <p><em>Salud America!</em> curates the stories of Salud Heroes through a <a href="<?php echo sa_get_group_permalink() . 'what-is-change/the-science-behind-healthy-change/'; ?>">step-by-step process of change</a> to inspire you to make a similar change in your area.</p>

                <p><a href="<?php echo sa_get_group_permalink() . 'share-your-story/'; ?>">Add your own</a> Salud Heroes stories, news updates, and resources now!</p>

                <?php
                if ( function_exists('bp_share_post_button') ) {
                    bp_share_post_button();
                }
                ?>
            </div>
            <div class="third-block fill-height">
                <?php
                if ( ! $user_id || ! $is_sa_member ) {
                    ?>
                    <div class="background-sapink" style="padding:0.8em;">
                        <h4 class="aligncenter" style="color:white;margin:0;">Why be a Salud Leader?</h4>
                        <p class="aligncenter" style="margin-bottom:.5em;">Get free stuff and join with others to reduce Latino obesity.
                        </p>
                        <?php if ( ! $user_id ) : ?>
                            <div class="aligncenter"><a href="/register/?salud-america=1" title="Register Now" class="button" style="margin-top:.6em;text-shadow:none;">Register Now</a></div>
                        <?php elseif ( ! $is_sa_member ) : ?>
                            <div class="aligncenter" style="text-shadow:none;"><?php bp_group_join_button(); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
                    <div class="background-saorange" style="padding:0.8em;">
                        <h4 class="aligncenter" style="color:white;margin:0;">Map your town!</h4>
                        <p class="aligncenter" style="margin-bottom:.5em;">See what’s happening, meet allies, and add your own healthy changes.
                        </p>
                        <a href="http://maps.communitycommons.org/policymap/"><img src="<?php echo sa_get_plugin_base_uri() . 'public/images/us_map_w_policiesx525.jpg'; ?>" alt="Map of US showing location of changes."></a>

                    </div>
            </div>
        </div>

        <hr />

        <?php
        } // end if ( $paged = 1 )
        ?>
        <div class="content-row">
            <?php bp_get_template_part( 'groups/single/saheroes/hero-loop' ); ?>
        </div>
        <?php
     } // END non-advocacy target version
    // Show the top section on the front page only.
// Not the section front? OK, let's figure out what to display.
} elseif ( sa_is_archive_taxonomy() ) {

    $tax_term = sa_get_requested_tax_term();
    // Special case: we're looking at an advocacy target
    if ( $tax_term->taxonomy == 'sa_advocacy_targets' ) {
        bp_get_template_part( 'groups/single/satermintros/advocacy-target' );
    }
    //Taxonomy term is set, but not an advocacy target
    ?>
    <div class="taxonomy-policies">
        <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>">Changes in the <?php
        echo $tax_term->name;
        echo ( $tax_term->taxonomy == 'sa_policy_tags' ? ' tag' : ' topic' )
        ?></h3>
        <?php bp_get_template_part( 'groups/single/saheroes/hero-loop' ); ?>
    </div>
    <?php

} elseif ( sa_is_single_post() ){
    // Store BP's dummy post data temporarily.
    global $post;
    $dummy_post = $post;

    // BuddyPress forces comments closed on BP pages. Override that.
    remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );

    bp_get_template_part( 'groups/single/saheroes/single' );
    comments_template();

    // BuddyPress forces comments closed on BP pages. Put the filter back.
    add_filter( 'comments_open', 'bp_comments_open', 10, 2 );

    // Put BP's dummy post data back.
    $post = $dummy_post;


} // if ( sa_is_section_front() ) :