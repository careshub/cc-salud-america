<?php
/**
* Template used for displaying the policies tab in Salud America
*/
// echo '<pre>';
// echo "is single?: ";
// var_dump( sa_is_single_post() );
// echo "is tax archive?: ";
// var_dump( sa_is_archive_taxonomy() );
// echo "is section front?: ";
// var_dump( sa_is_section_front() );
// echo "term: ";
// var_dump(sa_get_requested_tax_term());
// echo '</pre>';
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

if ( sa_is_section_front() ) {
    // Show the top section on the front page only.
    if ( $paged == 1 ) {
    ?>
        <div class="policy-search">
            <!--<form id="sa-policy-search" class="standard-form" method="get" action="/search-results">-->
            <h3 class="screamer sagreen">Search for Changes by Keyword</h3>
            <?php
            // @ need to redo the search function to also use this page.
            // maybe action = /?s=term
            if ( function_exists('sa_searchpolicies') ) { 
                sa_searchpolicies('/search-results'); 
            } ?>
        </div>

        <?php if ( function_exists('sa_location_search') ) {
            sa_location_search();
        } ?>
                
        <div class="browse-topics">
            <h3 class="screamer sablue">Browse Changes by Topic</h3>
                <?php 
                $args = array(
                    'taxonomy' => 'sa_advocacy_targets'
                );
                $categories = get_categories( $args );
                echo '<div class="row clear">';
                $i=0;

                foreach ( $categories as $category ) { 
                    //Loop through each advocacy target
                    // $cat_object = get_term_by('slug', $category->slug, 'sa_advocacy_targets');
                    // print_r($cat_object);
                    $cat_slug = $category->slug;
                    $section_title = $category->name;
                    $section_description = $category->description;
                    ++$i;
                    ?>
                    <div class="half-block salud-topic <?php echo $cat_slug; ?>">
                        <a href="<?php sa_the_cpt_tax_intersection_link( 'policies', 'sa_advocacy_targets', $cat_slug ) ?>" class="<?php echo $cat_slug; ?>  clear">
                            <span class="<?php echo $cat_slug; ?>x90 alignleft"></span>
                            <h4><?php echo $section_title; ?></h4>
                        </a>
                        <p><?php echo $section_description; ?></p>
                    </div>
                    <?php 
                    if ( $i%2 == 0 ) {
                        echo '</div>
                        <div class="row clear">';
                    }
                } // End advocacy target loop 
                echo '</div>';
                 ?>

        </div>
        <?php } // end if ( $paged = 1 ) ?>  
    <div class="row">
        <h3 class="screamer sapink">Newest Changes</h3>
        <?php
            bp_get_template_part( 'groups/single/sapolicies/policy-loop' );
        ?>
    </div>
<?php
// Not the section front? OK, let's figure out what to display.
} else {

    if ( sa_is_single_post() ){
        bp_get_template_part( 'groups/single/sapolicies/single' );
    } else if ( sa_is_archive_taxonomy() ) {
        $tax_term = sa_get_requested_tax_term();
        // Special case: we're looking at an advocacy target
        if ( $tax_term->taxonomy == 'sa_advocacy_targets' ) {
           bp_get_template_part( 'groups/single/sapolicies/advocacy-targets' );
        } else {
            //Taxonomy term is set, but not an advocacy target
            ?>
            <div class="taxonomy-policies">
                <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>">Changes in the <?php 
                echo $tax_term->name; 
                echo ( $tax_term->taxonomy == 'sa_policy_tags' ? ' tag' : ' topic' )
                ?></h3>
                    
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', 'sa-policy-short' ); ?>
                    <?php comments_template( '', true ); ?>
                <?php endwhile; // end of the loop. ?>
                <?php twentytwelve_content_nav( 'nav-below' ); ?>
            </div>
            <?php
        }
        
    }
} // if ( sa_is_section_front() ) :