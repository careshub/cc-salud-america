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
            'nav_item_position' => 11,
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
            'nav_item_position' => 13,
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

endif;  // if ( class_exists( 'BP_Group_Extension' ) )