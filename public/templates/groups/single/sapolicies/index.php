<?php
/**
* Template used for displaying the policies tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$user_id = get_current_user_id();
$is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );

if ( sa_is_section_front() ) {
    // Show the top section on the front page only.
    if ( $paged == 1 ) {
    ?>
    <div class="content-row clear">
        <div class="third-block spans-2">
            <h2 class="screamer sablue no-top-margin">New Healthy Changes!</h2>

            <p><strong>Healthy changes are happening right now.</strong></p>

            <p><em>Salud America!</em> is daily curating changes in policies, systems, and organizations that are making progress to reduce Latino childhood obesity across the nation.</p>

            <p>Browse these curated changes to find great examples of ways you can get involved, either joining these efforts or starting a similar change in your area.</p>

            <p><a href="<?php echo sa_get_group_permalink() . 'share-your-story/'; ?>">Add your own</a> news updates, resources, and stories of change!</p>

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

    <?php } // end if ( $paged = 1 ) ?>

        <div class="content-row">
            <?php sa_post_type_archive_big_bet_filters( 'changes' ); ?>

            <?php bp_get_template_part( 'groups/single/sapolicies/policy-loop' ); ?>
        </div>
<?php

// Not the section front? OK, let's figure out what to display.
} elseif ( sa_is_archive_search() ) {

?>
    <div class="policy-search">
        <!--<form id="sa-policy-search" class="standard-form" method="get" action="/search-results">-->
        <h3 class="screamer sagreen">Search for Changes by Keyword</h3>
        <?php sa_searchpolicies(); ?>
    </div>
<?php

} elseif ( sa_is_archive_taxonomy() ) {

    $tax_term = sa_get_requested_tax_term();
    // Special case: Advocacy targets get a special introductory block
    if ( $tax_term->taxonomy == 'sa_advocacy_targets' ) {
        bp_get_template_part( 'groups/single/satermintros/advocacy-target' );
    } ?>

    <div class="taxonomy-policies">
        <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>">Changes in the <?php
        echo $tax_term->name;
        echo ( $tax_term->taxonomy == 'sa_policy_tags' ? ' tag' : ' topic' )
        ?></h3>
        <div class="archive-filter-container background-light-gray">
            See <a href="<?php sa_the_cpt_tax_intersection_link( 'resources', 'sa_advocacy_targets', $tax_term->slug ); ?>">Resources</a>, <a href="<?php sa_the_cpt_tax_intersection_link( 'heroes', 'sa_advocacy_targets', $tax_term->slug ); ?>">Heroes</a>, or <a href="<?php echo trailingslashit( sa_get_section_permalink( 'big_bets' ) ) . $tax_term->slug; ?>">all posts</a> in this topic.
        </div>
        <?php bp_get_template_part( 'groups/single/sapolicies/policy-loop' ); ?>
    </div>
    <?php

} elseif ( sa_is_single_post() ){
    // BuddyPress forces comments closed on BP pages. Override that.
    remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );

    bp_get_template_part( 'groups/single/sapolicies/single' );

    // BuddyPress forces comments closed on BP pages. Put the filter back.
    add_filter( 'comments_open', 'bp_comments_open', 10, 2 );

} // if ( sa_is_section_front() ) :