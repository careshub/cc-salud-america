<?php
/**
* Template used for displaying the resources tab in the Salud America group
*/
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 ;

if ( sa_is_section_front() ) {
	// Show the top section on the front page only.
	if ( $paged == 1 ) {
		?>
		<h3 class="screamer sablue">Want to find resources to help make change in your area?</h3>
		<?php
		 //Display the page content before making the custom loop
		  // while ( have_posts() ) : the_post();
		  //    get_template_part( 'content', 'page-notitle' );
		  //   // comments_template( '', true );
		  // endwhile; // end of the loop.
		  ?>
		  <p>We&rsquo;ve collected a wide variety of the latest ways to get involved and find tool-kits, webinars and training opportunities to learn more.</p>

		<div class="policy-search">
			<?php sa_searchresources(); ?>
		</div>

		<h3 class="screamer sapurple">Browse Resources by Topic</h3>
		<div>

			<?php
			$advocacy_targets = get_terms('sa_advocacy_targets');
			foreach ($advocacy_targets as $target) {
				?>
				<div class="sixth-block mini-text"><a href="<?php sa_the_cpt_tax_intersection_link( 'resources', 'sa_advocacy_targets', $target->slug ); ?>"><span class="<?php echo $target->slug; ?>x90"></span><br /><?php echo $target->name; ?></a></div>
			<?php } //end foreach ?>
		</div>

		<?php
		//Specify the saresourcecat slugs we want to show here
		// If specifying more than one category, make them a comma-separated list
		$resource_cats = array( 'report-2', 'toolkit', 'get-involved' );
		?>

		<div class="row">
		  <h3 class="screamer sagreen">Browse Resources by Type</h3>
		  <?php saresources_get_featured_blocks( $resource_cats );?>
		</div>
	<?php
	} // end if $paged = 1
	?>

	<!-- Begin secondary loop for most recently added resources -->
	<div class="row taxonomy-policies">
		<h3 class="screamer sapink">Latest Resources Added</h3>
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