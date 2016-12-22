<?php

do_action( 'bp_before_group_header' );
$plugin_base_url = sa_get_plugin_base_uri();
$big_bets_base_url = sa_get_section_permalink( 'big_bets' );
$big_bets = get_terms( 'sa_advocacy_targets', array(
    'hide_empty' => 0,
    'exclude'    => array( 74550, 74551, 74552 ),
 ) );
?>

<div id="item-header-content">

    <div class="salud-header clear">
        <?php /* ?>
        <a href="<?php bp_group_permalink(); ?>" class="logo"><?php bp_group_avatar() ?></a>
        <h1 class="sa-group-header-title">Salud America! <br /><span class="salud-tagline">Growing Healthy Change</span></h1>
        <?php */ ?>
        <a href="<?php bp_group_permalink(); ?>" class="logo"><img src="<?php echo sa_get_plugin_base_uri() . 'public/images/SA-logo-2015-250x170.png' ?>" alt="Salud America logo" /></a>
        <div class="big-bets-banners-header">
        <?php
            foreach ( $big_bets as $term ) {
                ?>
                <a href="<?php echo $big_bets_base_url . $term->slug; ?>" class="big-bet-icon-link" title="Link to Big Bet archive: <?php echo $term->name; ?>"><img src="<?php echo $plugin_base_url . 'public/images/big_bets/icons-with-titles/' . $term->slug . '-112x150.png' ?>" alt="Icon for Big Bet: <?php echo $term->name; ?>" class="big-bet-icon" /></a>
                <?php
            }
        ?>
        </div>
        <div class="sa-kids-photo"><img src="<?php echo $plugin_base_url . 'public/images/pointing-girl-205x150.jpg'; ?>" alt="Girl pointing at the Big Bets header"></div>
        <div class="sa-social-icon-wrapper">
            <div class="sa-social-bar-items">
                <?php salud_hub_search_form(); ?>
                <div class="sa-social-icons">
                    <a href='http://twitter.com/saludtoday' target="_blank" class="sa-twitterx28"></a>
                    <a href='http://www.facebook.com/pages/SaludToday/160946931268' target="_blank" class="sa-facebookx28"></a>
                    <a href='http://www.youtube.com/user/SaludToday' target="_blank" class="sa-youtubex28"></a>
                    <a href='http://instagram.com/saludtoday' target="_blank" class="sa-instagramx28"></a>
                </div>
            </div>
        </div>

    </div>

    <!-- <span class="highlight clear"><?php bp_group_type(); ?></span>  -->
    <!-- <span class="activity clear"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span> -->

    <?php //do_action( 'bp_before_group_header_meta' ); ?>

    <!-- <div id="item-meta"> -->
        <?php /* ?>
        <div id="item-actions">

        <?php if ( bp_group_is_visible() ) : ?>

            <h3><?php _e( 'Group Admins', 'buddypress' ); ?></h3>

            <?php bp_group_list_admins();

            do_action( 'bp_after_group_menu_admins' );

            if ( bp_group_has_moderators() ) :
                do_action( 'bp_before_group_menu_mods' ); ?>

                <h3><?php _e( 'Group Mods' , 'buddypress' ); ?></h3>

                <?php bp_group_list_mods();

                do_action( 'bp_after_group_menu_mods' );

            endif;

        endif; ?>

        </div><!-- #item-actions -->
        <?php */ ?>

        <?php //bp_group_description(); ?>

        <?php /* ?>
        <div id="item-buttons">

            <?php do_action( 'bp_group_header_actions' ); ?>

        </div> <!-- #item-buttons -->
        <?php */ ?>


        <?php //do_action( 'bp_group_header_meta' ); ?>

    <!-- </div> -->
</div><!-- #item-header-content -->

<?php
do_action( 'bp_after_group_header' );
do_action( 'template_notices' );
// One that only fires on the SA group:
do_action( 'sa_bp_after_group_header' );
?>