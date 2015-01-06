<?php
$main_post = new WP_Query( sa_get_query() );
while ( $main_post->have_posts() ) : $main_post->the_post(); 
    get_template_part( 'content', 'sapolicies' );
    // comments_template( '', true ); 
endwhile; // end of the loop. ?>

<?php

$geogterm = wp_get_object_terms( $post->ID, 'geographies' );
if( !empty( $geogterm ) && !is_wp_error( $geogterm ) ){
    $geoid = $geogterm[0]->description;
    $geoidstate = '04000' . substr($geoid, 5, 4);
}        
                
?>

<div class="policy-meta">
<h3 class="screamer sapink">Related Data for this Region</h3>

<div class="half-block">
    <h6 style="margin-top:0;">Percent Adults Age 18+ Obese (BMI >= 30)  by County</h6>
    <script src='http://maps.communitycommons.org/jscripts/mapWidget.js?ids=348&vm=348&w=190&h=190&geoid=<?php echo $geoidstate; ?>&l=1'></script>
</div>        

<div class="half-block">
    <div id="dial">
        <script src='http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=<?php echo $geoid; ?>&id=779'></script>
    </div>


    <input type="button" id="btnSubmit1" value="Poverty rate" onclick="changeDial('779')" />
    <input type="button" id="btnSubmit2" value="Children in Poverty " onclick="changeDial('781')" />
    <input type="button" id="btnSubmit3" value="Pop. With No HS Diploma" onclick="changeDial('760')" />
</div>




<!--**********************************************Mike's stuff**********************************-->
<script type="text/javascript">
    function changeDial(id) {
        var geoid = '<?php echo $geoid; ?>';
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.src = "http://maps.communitycommons.org/jscripts/dialWidget.js?geoid=" + geoid + "&id=" + id;
        
        var dial = document.getElementById('dial');
        if (!document._write) document._write = document.write;
        document.write = function (str) {
            dial.innerHTML += str;
        };

        while (dial.firstChild) {  dial.removeChild(dial.firstChild); }
        dial.appendChild(s);
        }

         //Yan may handle this.
  //   jQuery(document).ready(function($) {
  //    // Show the map legend on click
        // $('#_cc-maplegend1').on('click', function(e){
        //     $('#_cc-maplegend1-content').toggle();
        //     e.preventDefault();
        // });
  //   });

</script>



<!--********************************************************************************************** -->              

</div>
<div class="related-policies">
<?php //Get related posts by topic
$source_post_id = $post->ID;
$exclude_posts = array( $source_post_id );
// echo PHP_EOL . 'excluded posts: ';
//  print_r($exclude_posts);
$terms = get_the_terms( $source_post_id, 'sa_advocacy_targets' );
    if ( !empty ($terms) ) :
        foreach ( $terms as $term ) {
            $advocacy_targets_id_array[] = $term->term_id;
        }

        $related_policies_args = array(
            'post_type' => 'sapolicies',
            'post__not_in' => $exclude_posts,
            'posts_per_page' => 3,
            'tax_query' => array(
                array(
                    'taxonomy' => 'sa_advocacy_targets',
                    'field' => 'id',
                    'terms' => $advocacy_targets_id_array,
                )
            )
        );

        $related_policies = new WP_Query( $related_policies_args );
        // print_r($related_policies);
        ?>
        <div class="related-by-topic">
            <h3 class="screamer sagreen">Related Changes by Topic</h3>
            <?php
            while ( $related_policies->have_posts() ): $related_policies->the_post();
                //This template should be the short result
                // get_template_part( 'content', 'sa-policy-short' );
                // $body = apply_filters( 'the_content', get_the_content() );
                // $body = ellipsis( $body );
                ?>
                <div class="third-block">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
                        <div class="entry-content">
                            <header class="entry-header clear">
                                <h4 class="entry-title">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h4>
                                <?php the_excerpt(); ?>
                            </header>

                        </div> <!-- entry-content -->
                    </article>
                </div>
                <?php
                $exclude_posts[] = $post->ID;
            endwhile; // end of the loop.
        ?>
        </div> <!-- #related-by-topic -->
        <?php

    endif; //check for empty terms
    // echo PHP_EOL . 'excluded posts: ';
    // print_r($exclude_posts);

      ?>  

        <div class="related-what-can-you-do clear">
            <h3 class="screamer sablue">What Can You Do?</h3>
                
            <a href="/salud-america/share-your-own-stories/" class="column1of3 aligncenter">
                <img alt="Health" src="/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/BeaStar_icon.png" width="100px"/><br />Start your own change!
            </a>
            
<!--                            <a href="http://##" class="column1of3 aligncenter">-->
                    <span class="column1of3 aligncenter">
                <img alt="Health" src="/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/AddChange_icon.png" width="100px"/><br />Connect with members in your area!
                    </span>
                    <!--                            </a>-->

            <a href="/salud-america/what-is-change/" class="column1of3 aligncenter">
                <img alt="Health" src="/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/WhatsChange_icon.png" width="100px"/><br />See how a change is made
            </a>
        </div>

<?php //Get related posts by tag
$tags = get_the_terms( $source_post_id, 'sa_policy_tags' );
    if ( !empty ($tags) ) :
        foreach ( $tags as $tag ) {
            $policy_tags_array[] = $tag->term_id;
        }
        // echo 'policy-tags: ';
        // print_r($tags);
        // echo PHP_EOL. 'array';
        // print_r($policy_tags_array);

        $related_policies_args = array(
            'post_type' => 'sapolicies',
            'post__not_in' => $exclude_posts,
            'posts_per_page' => 3,
            'tax_query' => array(
                array(
                    'taxonomy' => 'sa_policy_tags',
                    'field' => 'id',
                    'terms' => $policy_tags_array,
                    'operator' => 'IN'
                )
            )
        );

        $related_policies = new WP_Query( $related_policies_args );
        // print_r($related_policies);
        ?>
        <div class="related-by-tag">
            <h3 class="screamer saorange">Related Changes by Tag</h3>
            <?php
            while ( $related_policies->have_posts() ): $related_policies->the_post();
                //This template should be the short result
                // get_template_part( 'content', 'sa-policy-short' );
            ?>
            <div class="third-block">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'sa-item-short-form' ); ?>>
                        <div class="entry-content">
                            <header class="entry-header clear">
                                <h4 class="entry-title">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h4>
                                <?php the_excerpt(); ?>
                            </header>

                        </div> <!-- entry-content -->
                    </article>
                </div>
            <?php
            endwhile; // end of the loop.
        ?>
        </div>
        <?php
    endif; //check for empty terms


      ?>
        
</div> <!-- .related-policies -->