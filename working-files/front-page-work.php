** Notices will be created from active contests, active take actions and the tweetchat subjects.
<div class="notice" style="border-left:8px solid red;background-color:#F3F3F3;padding: 1px 2em 1em;">
    <h4 style="color:black;margin-bottom:.5em;"><a href="<?php echo home_url( 'salud-america-video-contest' ); ?>" style="text-decoration:none;color:black"><span class="uppercase" style="text-transform:uppercase; color: red;">Vote &amp; Win: </span>&ensp;Pick Your Fave #SaludHeroes of Active Play by 3/25/15 &amp; Win a Prize!</a></h4>
    <a href="<?php echo home_url( 'salud-america-video-contest' ); ?>" class="button">Vote now</a>
    <h4 style="color:black"><a href="http://www.care2.com/go/z/22646564" style="text-decoration:none;color:black"><span class="uppercase" style="text-transform:uppercase; color: red;">Take Action</span>&emsp;Tell Kellogg: Stop Marketing High-Sugar Recipes to Latino Families</a></h4>
    <?php /* ?><p>Summer is here, and that means camp for kids to make new friends and try new things—but it also potentially means being exposed to unhealthy sugary drinks.</p><?php */?>
    <a href="http://www.care2.com/go/z/22646564" target="_blank" class="button">Sign the Petition</a>

    <h4 style="color:black;margin-top: 1em;margin-bottom:.5em;"><a href="<?php echo home_url( 'salud-america/tweetchat' ); ?>" style="text-decoration:none;color:black"><span class="uppercase" style="text-transform:uppercase; color: red;">Tweetchat 3/17:</span>&ensp;&ldquo;Closer to My Grocer&rdquo;</a></h4>
    <a href="https://twitter.com/SaludToday" target="_blank" class="button" >Follow the conversation</a>&emsp;<a href="<?php echo home_url( 'salud-america/tweetchat' ); ?>" class="button">Learn more</a>
</div>


<h3 class="screamer sagreen">How can you fight Latino childhood obesity in your area?</h3>

<?php sa_get_random_hero_video(); ?>

<p class="intro-text" style="font-size:1.2em;">Obesity threatens the health of Latino kids.</p>

<p><strong>Growing Healthy Change</strong> brings you healthy changes happening in your community right now, and shows how to start your own change.</p>

<p>Find new policies, stories, and research to reduce Latino childhood obesity&mdash;like unlocking playgrounds after school&mdash;in your city, school, county, state, and nation.</p>

<p>Learn from our Salud Heroes how you can make a change, too.</p>

<p>Get started!</p>

<div class="find-changes">
    <h3 class="screamer saorange">1. Find Changes</h3>

        <div style="margin-bottom:1.6em;">
            <h4 style="margin-top:0;">By Keyword</h4>
            <?php if ( function_exists('sa_searchpolicies') ) {
            sa_searchpolicies('/search-results');
        } ?>
        </div>

    <div class="row">

        <div class="half-block">
            <h4 style="margin-top:0;">By Topic</h4>
            <?php
            $advocacy_targets = get_terms('sa_advocacy_targets');
            foreach ($advocacy_targets as $target) {
                ?>
                <div class="column1of3 mini-text"><a href="<?php cc_the_cpt_tax_intersection_link( 'sapolicies', 'sa_advocacy_targets', $target->slug ) ?>" title="<?php echo $target->description; ?>"><span class="<?php echo $target->slug; ?>x90"></span><br /><?php echo $target->name; ?></a></div>
            <?php } //end foreach ?>
            [sa_advocacy_target_icons columns="3" section="changes"]

        </div>

        <div class="half-block">
            <h4 style="margin-top:0;">By Location</h4>
            <a href="http://maps.communitycommons.org/policymap/" title="link to interactive map of changes"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/salud_america/policy-map-sm.jpg" alt='Map of Changes' class="no-box"></a><br />
            <a href="http://maps.communitycommons.org/policymap/">Browse changes happening in your area</a>
        </div>
    </div>
    <h4>Recent Changes</h4>
    <div class="row">
        <?php
        //Grab the 3 most recent success stories
            $args = array (
                    'post_type' => 'sapolicies',
                    'posts_per_page' => 3,
                    // 'tax_query' => array(
                    //  array(
                    //      'taxonomy' => 'sa_resource_cat',
                    //      'field' => 'slug',
                    //      'terms' => array( 'success-stories' ),
                    //  )
                    // )
                );
            //Grab the possible advocacy targets
            $advocacy_targets = get_terms('sa_advocacy_targets');
            foreach ($advocacy_targets as $target) {
                $possible_targets[] = $target->slug;
            }
            $ssquery = new WP_Query( $args );
            while ( $ssquery->have_posts() ) {
            // print_r($possible_targets);

                $ssquery->the_post();
                global $post;
                setup_postdata( $post );

                // echo '<li class="third-block"><h5>' . $target->name . '</h5>';
                echo '<div class="third-block">';
                ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >

                    <?php
                    if ( has_post_thumbnail()) {
                        //Use the post thumbnail if it exists
                        the_post_thumbnail('feature-front-sub');
                        echo '<br />';
                    } else {
                        //Otherwise, use some stand-in images by advocacy target
                        $terms = get_the_terms( $post->ID, 'sa_advocacy_targets' );
                        if ( !empty ($terms) ) :
                            //loop through the terms to find a usable (unique) image
                            foreach ($terms as $term) {
                                if ( in_array( $term->slug, $possible_targets ) ) {
                                    $advo_target = $term->slug;
                                    break;
                                }
                            }
                            //If an advo_target didn't get set, we'll set one at random
                            if ( !( $advo_target ) ) {
                                $advo_target = current($possible_targets);
                                // $advo_target = next_targe;
                                // print_r(current($possible_targets));
                            }

                            // echo PHP_EOL . $advo_target;

                            //Delete that value from the possible values
                                $key_to_delete = array_search($advo_target, $possible_targets);
                                if ( false !== $key_to_delete ) {
                                    unset( $possible_targets[$key_to_delete] );
                                }

                        endif; //check for empty terms

                        echo '<img src="' . get_stylesheet_directory_uri() . '/img/salud_america/advocacy_targets/' . $advo_target . 'x300.jpg" > ';
                        unset($advo_target);
                    }

                    echo '<h5 class="entry-title">' . get_the_title() . '</h5></a>';
                    the_excerpt();
                    ?>
                </div>
                 <?php
            }
            wp_reset_postdata();
            ?>
    </div> <!-- .row -->
</div> <!-- find-changes -->

<!-- <h3 class="screamer sablue">2. Learn from Success Stories</h3> -->
<!-- <div class="learn-from-success-stories">

</div> -->

<h3 class="screamer sapurple">2. Learn to Create Change</h3>
<div class="row clear">
    <h4 style="margin-top:0;">See the Changes a Salud Hero Can Make</h4>

    <?php
    //Grab the 3 most recent success stories
        $args = array (
                'post_type' => 'sa_success_story',
                'posts_per_page' => 3,
            );
        $ssquery = new WP_Query( $args );
        while ( $ssquery->have_posts() ) {

            $ssquery->the_post();
            global $post;
            setup_postdata( $post );
            // echo '<li class="third-block"><h5>' . $target->name . '</h5>';
            echo '<div class="third-block">';
            ?>
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >

                <?php
                if ( has_post_thumbnail()) {
                    the_post_thumbnail('feature-front-sub');
                    echo '<br />';
                    }

                the_title();
                ?>
                </a>
            </div>
             <?php
        }
        wp_reset_postdata();
        ?>
    </div>  <!-- end .row -->

<div class="row">
    <div class="half-block" style="margin-top:0;">
        <h4 style="margin-top:0;">What's Change?</h4>
        <a href="/salud-america/what-is-change"><img src='/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/WhatsChange_icon.png' alt='Active Play' class="no-box" style="width:25%; float:left; margin-right:5%;"></a>
        <p>Find out what "change" really means and all the science behind it.<br />
            <a href="/salud-america/what-is-change" class="button" title="Learn what change means.">Learn more</a></p>
    </div>

    <div class="half-block" style="margin-top:0;">
        <h4 style="margin-top:0;">Get help to Make a Change</h4>
        <a href='/salud-america/saresourcespage/'><img src='/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/Resoucesmakechange_icon.png' alt='Active Play' class="no-box" style="width:25%; float:left; margin-right:5%;"></a>
        <p>Use research, toolkits, and other elements to learn about healthy change.<br />
            <a href="/saresources/" class="button" title="Learn about healthy change.">Learn more</a></p>
    </div>
</div>

<h3 class="screamer sablue">3. Be a Salud Hero</h3>

<div class="row">
    <div class="half-block" style="margin-top:0;">
        <h4 style="margin-top:0;">Making a Change?</h4>
        <a href='/salud-america/share-your-own-stories/'><img src='/wp-content/themes/CommonsRetheme/img/salud_america/Salud_Platform_WebReady_files/BeaStar_icon.png' alt='Share Your Change' style="width:25%; float:left; margin-right:5%;" class="no-box"></a>
        <p>If you or someone you know is starting a change or already made a change, let us know. <br />
            We can write it up, possibly film it, and share it nationwide!<br />
            <a href="/salud-america/share-your-own-stories/" class="button" title="Share your story.">Share your story or alert us to a change</a>
            <!-- <a href="/salud-america/share-your-own-stories/" class="button" title="Alert us to a change.">Alert us to a change.</a> --></p>
    </div>

    <div class="half-block" style="margin-top:0;">

        <iframe width="450" height="250" src="//www.youtube.com/embed/8I4T08MONBA?rel=0;showinfo=0;controls=0" frameborder="0" allowfullscreen></iframe>
                </div>
</div>