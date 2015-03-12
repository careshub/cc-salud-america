<?php          
//First section is used if this page is an advocacy target taxonomy page. Second section renders page if tax_term is set but not advocacy target, like a resource category. Standard archives are rendered using the else section (would also be the front page).
$tax_term = sa_get_requested_tax_term();
//Get the page intro content, which is stored as a page with the same slug as the target area.
$args = array (
    'pagename' => 'salud-america/sa-advocacy-targets-intros/' . $tax_term->slug,
    'post_type' => 'page'
    );
// print_r($wp_query->query_vars);
$page_intro = new WP_Query( $args );
// print_r($page_intro);

while ( $page_intro->have_posts() ) : $page_intro->the_post(); ?>
    <article  id="post-<?php the_ID(); ?>" <?php post_class('advocacy_target_introduction'); ?>>

    <?php 
        //Get the page header image ?>
        <header>
             <img class="size-full wp-image-16768 no-box" alt="Topic header for <?php echo $tax_term->name ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/img/salud_america/topic_headers/<?php echo $tax_term->slug ?>.jpg" />
         </header>
     <?php the_content(); ?>
        <div class="clear clear-both">
            <!-- TODO: Need to figure out what the pattern is here, or generalize it somehow! -->
            <?php if ( $tax_term->slug == 'sa-active-play' ) { ?>
                                            
            <div class="column1of3 aligncenter">
               <img class="size-full no-box wp-image-18047 aligncenter" alt="research-review-icon_again2" src="/wp-content/uploads/2013/08/Research_review.png" /><b>Research Review</b><br /><br />
                <a href="/wp-content/uploads/2013/08/Active-Play-Research-Review.pdf" class=" button  aligncenter">Download</a></p>
            </div>

            <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18049 aligncenter" alt="AP_brief_2" src="/wp-content/uploads/2013/08/AP_brief.png" /><b>Issue Brief</b><br /><br />
                <div class="pad"><a href="/wp-content/uploads/2013/08/Active-Play-Issue-Brief.pdf" class=" button  aligncenter">Download in English</a></div>
                <div class="pad"><a href="/wp-content/uploads/2014/02/SpanishActive-Play-Issue-Brief.pdf" class=" button  aligncenter">Download in Spanish</a></div>
            </div>

            <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18050 aligncenter" alt="AP_info_2" src="/wp-content/uploads/2013/08/AP_info.png" /><b>Infographic</b><br /><br />
                <div class="pad"><a href="/wp-content/uploads/2013/08/Active-Play-Infographic-875.jpg" class=" button  aligncenter">Download in English</a></div>
                <div class="pad"><a href="wp-content/uploads/2014/02/ActivePlay_Infographic_SPN_sml.jpg" class=" button  aligncenter">Download in Spanish</a></div>
            </div>  

            <?php } else if ( $tax_term->slug == 'sa-active-spaces' ) { ?>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18047 aligncenter" alt="research-review-icon_again2" src="/wp-content/uploads/2013/08/Research_review.png" /></a><b>Research Review</b><br /><br />
                    <a href="/wp-content/uploads/2013/08/Active-Spaces-Research-Review.pdf" class=" button  aligncenter">Download</a></p>
                </div> 

                <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18049 aligncenter" alt="AP_brief_2" src="/wp-content/uploads/2013/08/AS_brief2.png" /><b>Issue Brief</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Active-Spaces-Issue-Brief.pdf" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/SpanishActive-Spaces-Issue-Brief.pdf" class=" button  aligncenter">Download in Spanish</a></div>
                </div>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18050 aligncenter" alt="AP_info_2" src="/wp-content/uploads/2013/08/AS_info.png" /><b>Infographic</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Active-Spaces-Infographic-875.jpg" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Salud_ActiveSpaces_Infographic_SPN_sml.jpg" class=" button  aligncenter">Download in Spanish</a></div>
                </div> 
                                            
            <?php } else if ( $tax_term->slug == 'sa-better-food-in-neighborhoods' ) { ?>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18047 aligncenter" alt="research-review-icon_again2" src="/wp-content/uploads/2013/08/Research_review.png" /></a><b>Research Review</b><br /><br />
                    <a href="/wp-content/uploads/2013/08/BetterFoodintheNeighborhood-ResearchReview.pdf" class=" button  aligncenter">Download</a></p>
                </div>

                <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18049 aligncenter" alt="AP_brief_2" src="/wp-content/uploads/2013/08/FN_brief2.png" /><b>Issue Brief</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Better-Food-in-the-Neighborhood-Issue-Brief.pdf" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/SpanishBetter-Food-in-Neighborhoods-Issue-Brief.pdf" class=" button  aligncenter">Download in Spanish</a></div>
                </div>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18050 aligncenter" alt="AP_info_2" src="/wp-content/uploads/2013/08/FN_info.png" /><b>Infographic</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Better-Food-in-the-Neighborhood-Infographic-875.jpg" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Salud_BetterFoods_Infographic_SPN_sml_0.jpg" class=" button  aligncenter">Download in Spanish</a></div>
                </div> 

            <?php } else if ( $tax_term->slug == 'sa-healthier-marketing' ) { ?>
                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18047 aligncenter" alt="research-review-icon_again2" src="/wp-content/uploads/2013/08/Research_review.png" /></a><b>Research Review</b><br /><br />
                    <a href="/wp-content/uploads/2013/08/Healthier-Marketing-Research-Review.pdf" class=" button  aligncenter">Download</a></p>
                </div>

                <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18049 aligncenter" alt="AP_brief_2" src="/wp-content/uploads/2013/08/HM_brief2.png" /><b>Issue Brief</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Healthier-Marketing-Issue-Brief.pdf" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/SpanishHealthier-Marketing-Issue-Brief.pdf" class=" button  aligncenter">Download in Spanish</a></div>
                </div>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18050 aligncenter" alt="AP_info_2" src="/wp-content/uploads/2013/08/HM_info2.png" /><b>Infographic</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Healthier-Marketing-Infographic-875.jpg" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Salud_HealthierMarketing_Infographic_SPN_sml.jpg" class=" button  aligncenter">Download in Spanish</a></div>
                </div> 

            <?php } else if ( $tax_term->slug == 'sa-healthier-school-snacks' ) { ?>
                
                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18047 aligncenter" alt="research-review-icon_again2" src="/wp-content/uploads/2013/08/Research_review.png" /></a><b>Research Review</b><br /><br />
                    <a href="/wp-content/uploads/2013/08/Healthier-School-Snacks-Research-Review.pdf" class=" button  aligncenter">Download</a></p>
                </div>

                <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18049 aligncenter" alt="AP_brief_2" src="/wp-content/uploads/2013/08/SS_brief2.png" /><b>Issue Brief</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Healthier-School-Snacks-Issue-Brief.pdf" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/SpanishHealthier-School-Snacks-Issue-Brief.pdf" class=" button  aligncenter">Download in Spanish</a></div>
                </div>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18050 aligncenter" alt="AP_info_2" src="/wp-content/uploads/2013/08/SS_info.png" /><b>Infographic</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2013/08/Healthier-School-Snacks-Infographic-875.jpg" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Healthy-school-sancks-spn-875.jpg" class=" button  aligncenter">Download in Spanish</a></div>
                </div> 

            <?php } else if ( $tax_term->slug == 'sa-sugary-drinks' ) { ?>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18047 aligncenter" alt="research-review-icon_again2" src="/wp-content/uploads/2013/08/Research_review.png" /></a><b>Research Review</b><br /><br />
                    <a href="wp-content/uploads/2014/02/Sugary-Drinks-research-review.pdf" class=" button  aligncenter">Download</a></p>
                </div> 

                <div class="column1of3 aligncenter">
                <img class="size-full no-box wp-image-18049 aligncenter" alt="AP_brief_2" src="/wp-content/uploads/2014/02/SD_brief2.png" /><b>Issue Brief</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Sugary-Drinks-issue-brief.pdf" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/SpanishSugary-Drinks-Issue-Brief.pdf" class=" button  aligncenter">Download in Spanish</a></div>
                </div>

                <div class="column1of3 aligncenter">
                    <img class="size-full no-box wp-image-18050 aligncenter" alt="AP_info_2" src="/wp-content/uploads/2014/02/SD_info.png" /><b>Infographic</b><br /><br />
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Sugary-Drinks-Infographic-875.png" class=" button  aligncenter">Download in English</a></div>
                    <div class="pad"><a href="/wp-content/uploads/2014/02/Salud_SugaryDrinks_Infographic_SPN_sml.jpg" class=" button  aligncenter">Download in Spanish</a></div>
                </div> 

            <?php } ?>
       </div>
   </article>
<?php                    
endwhile; // end of the loop.
?>
<div class="taxonomy-policies">
    <h3 class="screamer <?php sa_the_topic_color( $tax_term->slug ); ?>">Resources in the <?php 
        echo $tax_term->name; 
        echo ( $tax_term->taxonomy == 'sa_policy_tags' ? ' tag' : ' topic' );
    ?></h3>
    <?php bp_get_template_part( 'groups/single/saresources/resource-loop' ); ?>
</div>