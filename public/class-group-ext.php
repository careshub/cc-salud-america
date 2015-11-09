<?php
/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :

class CC_Salud_America_Policies extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => sa_get_tab_slug( $section = 'policies' ),
            'name' => sa_get_tab_label( $section = 'policies' ),
            'nav_item_position' => 12,
            'access' => 'anyone', // Make this a publicly accessible tab
            'show_tab' => $this->enable_cc_sa_policies_tab() ? 'anyone' : 'noone', // Anyone can see the nav tab
        );
        parent::init( $args );
    }

    function display( $group_id = null ) {
        bp_get_template_part( 'groups/single/sapolicies/index' );
    }

    function enable_cc_sa_policies_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_policies_tab', $setting );
    }

}
bp_register_group_extension( 'CC_Salud_America_Policies' );

class CC_Salud_America_Resources extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => sa_get_tab_slug( $section = 'resources' ),
            'name' => sa_get_tab_label( $section = 'resources' ),
            'nav_item_position' => 13,
            'access' => 'anyone', // Make this a publicly accessible tab
            'show_tab' => $this->enable_cc_sa_resources_tab() ? 'anyone' : 'noone', // Anyone can see the nav tab
        );
        parent::init( $args );
    }

    function display( $group_id = null ) {
        bp_get_template_part( 'groups/single/saresources/index' );
    }

    function enable_cc_sa_resources_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_resources_tab', $setting );
    }

}
bp_register_group_extension( 'CC_Salud_America_Resources' );

class CC_Salud_America_Heroes extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => sa_get_tab_slug( $section = 'heroes' ),
            'name' => sa_get_tab_label( $section = 'heroes' ),
            'nav_item_position' => 14,
            'access' => 'anyone', // Make this a publicly accessible tab
            'show_tab' => $this->enable_cc_sa_heroes_tab() ? 'anyone' : 'noone', // Anyone can see the nav tab
        );
        parent::init( $args );
    }

    function display( $group_id = null ) {
        bp_get_template_part( 'groups/single/saheroes/index' );
    }

    function enable_cc_sa_heroes_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_heroes_tab', $setting );
    }

}
bp_register_group_extension( 'CC_Salud_America_Heroes' );

class CC_Salud_America_Video_Contests extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => sa_get_tab_slug( $section = 'video-contest' ),
            'name' => sa_get_tab_label( $section = 'video-contest' ),
            // 'nav_item_position' => 13,
            'access' => 'anyone', // Make this a publicly accessible tab in the SA group
            'show_tab' => $this->enable_cc_sa_video_contests_tab() ? 'anyone' : 'noone', // Don't create a nav tab
        );
        parent::init( $args );
    }

    function display( $group_id = null ) {
        bp_get_template_part( 'groups/single/savideocontests/index' );
    }

    function enable_cc_sa_video_contests_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_video_contests_tab', $setting );
    }

}
bp_register_group_extension( 'CC_Salud_America_Video_Contests' );

class CC_Salud_America_Take_Action extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => sa_get_tab_slug( $section = 'take_action' ),
            'name' => sa_get_tab_label( $section = 'take_action' ),
            'nav_item_position' => 11,
            'access' => 'anyone', // Make this a publicly accessible tab in the SA group
            'show_tab' => $this->enable_cc_sa_take_action_tab() ? 'anyone' : 'noone', // Don't create a nav tab
        );
        parent::init( $args );
    }

    function display( $group_id = null ) {
        bp_get_template_part( 'groups/single/satakeaction/index' );
    }

    function enable_cc_sa_take_action_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_take_action_tab', $setting );
    }

}
bp_register_group_extension( 'CC_Salud_America_Take_Action' );

class CC_Salud_America_Big_Bets extends BP_Group_Extension {
    // Documentation: https://codex.buddypress.org/developer/group-extension-api/
    function __construct() {
        $args = array(
            'slug' => 'big-bets', //sa_get_tab_slug( 'big_bets' ),
            'name' => 'Topics', //sa_get_tab_label( 'big_bets' ),
            'nav_item_position' => 62,
            // Make this a publicly accessible tab in the SA group
            'access' => $this->enable_cc_sa_big_bets_tab() ? 'anyone' : 'noone',
            'show_tab' => $this->enable_cc_sa_big_bets_tab() ? 'anyone' : 'noone',
        );
        parent::init( $args );
    }

    function display( $group_id = null ) {
        bp_get_template_part( 'groups/single/sabigbets/index' );
    }

    function enable_cc_sa_big_bets_tab() {
        $setting = sa_is_sa_group();
        return apply_filters( 'enable_cc_sa_big_bets_tab', $setting );
    }

}
bp_register_group_extension( 'CC_Salud_America_Big_Bets' );

endif;  // if ( class_exists( 'BP_Group_Extension' ) )