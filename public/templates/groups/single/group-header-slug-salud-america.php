<?php

do_action( 'bp_before_group_header' );

?>

<div id="item-header-content">

<!--     <div class="noms clear">
        <h2>
            <div id="item-header-avatar">
                <?php bp_group_avatar( 'width=80&height=80' ) ?>
            </div>
            <a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>"><?php bp_group_name(); ?></a></h2>
    </div> -->
    <div class="salud-header clear">
        <a href="<?php bp_group_permalink(); ?>" class="logo"><?php bp_group_avatar() ?></a>
        <h1 class="sa-group-header-title">Salud America! <br /><span class="salud-tagline">Growing Healthy Change</span></h1>
        <div class="sa-social-icon-wrapper">
            <div class="sa-social-icons visible-maxi">
                <a href='http://www.facebook.com/pages/SaludToday/160946931268' target="_blank" class="facebook-whx26"></a>
                <a href='http://twitter.com/saludtoday' target="_blank" class="twitter-whx26"></a>
                <a href='http://www.youtube.com/user/SaludToday' target="_blank" class="youtube-whx26"></a>
            </div>
        </div>
        <div class="sa-kids-photo"><img src="/wp-content/themes/CommonsRetheme/img/salud_america/sa-kids-335.png"></div>

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
?>