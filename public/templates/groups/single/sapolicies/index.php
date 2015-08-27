<?php
/**
* Template used for displaying the policies tab in the Salud America group
*/
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

if ( sa_is_section_front() ) {
    // Show the top section on the front page only.
    if ( $paged == 1 ) {
    ?>
    <div class="content-row clear">
        <div class="third-block spans-2">
            <h2 class="screamer sablue no-top-margin">New Healthy Changes!</h2>

            <p>Our team is curating the newest policy-based healthy changes popping up in Latino areas nationwide.</p>
            <p>These are great examples of ways you can get involved, either joining these efforts or starting a similar change in your town!</p>
            <?php
            if ( function_exists('bp_share_post_button') ) {
                bp_share_post_button();
            }
            ?>
        </div>
        <div class="third-block fill-height">
            <?php
            //if ( ! is_user_logged_in() ) {
                ?>
                <div class="background-sapink" style="padding:0.8em;">
                    <h4 class="aligncenter" style="color:white;margin:0;">Why be a Salud Leader?</h4>
                    <p class="aligncenter" style="margin-bottom:0;">Get free stuff and join with others to reduce Latino obesity. <br />
                        <a href="/register/?salud-america=1" title="Register Now" class="button" style="margin-top:.6em; color:">Register Now</a>
                    </p>
                </div>
                <?php
            //}
            ?>
                <div class="background-saorange" style="padding:0.8em;">
                    <h4 class="aligncenter" style="color:white;margin:0;">Map your town!</h4>
                    <p class="aligncenter" style="margin-bottom:0;">See what’s happening, meet allies, and add your own healthy changes.
                    </p>
                </div>
        </div>
    </div>

    <hr />

    <?php } // end if ( $paged = 1 ) ?>
        <div class="content-row">
            <?php
                bp_get_template_part( 'groups/single/sapolicies/policy-loop' );
            ?>
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