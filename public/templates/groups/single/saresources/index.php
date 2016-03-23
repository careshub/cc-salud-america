<?php
/**
* Template used for displaying the resources tab in the Salud America group
*/
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 ;
$user_id = get_current_user_id();
$is_sa_member = groups_is_user_member( $user_id, sa_get_group_id() );

if ( sa_is_section_front() ) {
	  // Show the top section on the front page only.
    if ( $paged == 1 ) {
    ?>
    <div class="content-row clear">
        <div class="third-block spans-2">
            <h2 class="screamer sablue no-top-margin">New Resources for Change!</h2>

            <p><strong>Resources are tools you can use to push for healthy change.</strong></p>

            <p><em>Salud America!</em> is daily curating toolkits, data, webinars and other educational materials to support you in creating obesity-reducing changes in your area.</p>

            <p>These curated resources are great nuts-and-bolts guides to change, as well as helpful content and data that can help you build a case for a decision-maker.</p>

            <p><a href="<?php echo sa_get_group_permalink() . 'share-your-story/'; ?>">Add your own</a> resources, news updates, and stories of change now!</p>

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

	<!-- Begin secondary loop for most recently added resources -->
	<div class="content-row taxonomy-policies">
        <?php sa_post_type_archive_big_bet_filters( 'resources' ); ?>

		<?php bp_get_template_part( 'groups/single/saresources/resource-loop' ); ?>
	</div>
<?php

// Not the section front? OK, let's figure out what to display.
} elseif ( sa_is_archive_search() ) {

?>
	<div class="policy-search">
		<h3 class="screamer sagreen">Search for Resources</h3>
		<?php sa_searchresources(); ?>
	</div>
<?php

} elseif ( sa_is_archive_taxonomy() ) {

	$tax_term = sa_get_requested_tax_term();
    // Special case: Advocacy targets get a special introductory block
    if ( $tax_term->taxonomy == 'sa_advocacy_targets' ) {
        bp_get_template_part( 'groups/single/satermintros/advocacy-target' );
    } ?>

    <div class="taxonomy-policies">
        <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>">Resources in the <?php
        echo $tax_term->name;
        echo ( $tax_term->taxonomy == 'sa_policy_tags' ? ' tag' : ' topic' )
        ?></h3>
        <div class="archive-filter-container background-light-gray">
            See <a href="<?php sa_the_cpt_tax_intersection_link( 'changes', 'sa_advocacy_targets', $tax_term->slug ); ?>">Changes</a>, <a href="<?php sa_the_cpt_tax_intersection_link( 'heroes', 'sa_advocacy_targets', $tax_term->slug ); ?>">Heroes</a>, or <a href="<?php echo trailingslashit( sa_get_section_permalink( 'big_bets' ) ) . $tax_term->slug; ?>">all posts</a> in this topic.
        </div>
		<?php bp_get_template_part( 'groups/single/saresources/resource-loop' ); ?>
    </div>
    <?php

} elseif ( sa_is_single_post() ) {
    // BuddyPress forces comments closed on BP pages. Override that.
    remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );

	bp_get_template_part( 'groups/single/saresources/single' );

    // BuddyPress forces comments closed on BP pages. Put the filter back.
    add_filter( 'comments_open', 'bp_comments_open', 10, 2 );

}