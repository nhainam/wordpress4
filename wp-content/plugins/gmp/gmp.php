<?php
/*
Plugin Name: My Awesome Plugin
Plugin URI: http://example.com/wordpress-plugins/my-plugin
Description: This is a brief description of my plugin
Version: 1.0
Author: Brad Williams
Author URI: http://example.com
*/
register_activation_hook(__FILE__,'gmp_install');
function gmp_install() {
    global $wp_version;
    if(version_compare($wp_version, "2.9", "<")) {
        deactivate_plugins(basename(__FILE__)); // Deactivate our plugin
        wp_die("This plugin requires WordPress version 2.9 or higher.");
    }
    add_option('gmp_display_mode', 'Christmas Tree');
}

register_deactivation_hook(__FILE__,'gmp_uninstall');
function gmp_uninstall() {
    delete_option('gmp_display_mode', 'Christmas Tree');
}

add_action('init', 'gmp_init');
function gmp_init() {
    load_plugin_textdomain('gmp-plugin', false,
    plugin_basename(dirname(__FILE__).'/localization'));
}

function email_new_comment() {
    wp_mail('me@example.com', __('New blog comment', 'gmp-plugin') ,
    __('There is a new comment on your website: http://example.com','gmp-plugin'));
}
add_action('comment_ post', 'email_new_comment');

function SubscribeFooter($content) {
    if(is_single()) {
        $content.= '<h3>' .__('Enjoyed this article?', 'gmp-plugin') . '</h3>';
        $content.= '<p>' .__('Subscribe to our
            <a href="http://example.com/feed">RSS feed</a>!', 'gmp-plugin'). '</p>';
    }
    return $content;
}
add_filter ('the_content', 'SubscribeFooter');

// create custom plugin settings menu
add_action('admin_menu', 'gmp_create_menu');
function gmp_create_menu() {
    //create new top-level menu
    add_menu_page('GMP Plugin Settings', 'GMP Settings',
        'administrator', __FILE__, 'gmp_settings_page',
        plugins_url('/gmp.png', __FILE__));
    //create three sub-menus: email, template, and general
    add_submenu_page( __FILE__, 'General Settings Page', 'General',
        'administrator', __FILE__.'_general_settings', 'gmp_settings_general');

    add_submenu_page( __FILE__, 'Email Settings Page', 'Email',
        'administrator', __FILE__.'_email_settings', 'gmp_settings_email');

    add_submenu_page( __FILE__, 'Template Settings Page', 'Template',
        'administrator', __FILE__.'_template_settings', 'gmp_settings_template');

    remove_menu_page('Posts');
}

function gmp_settings_general() {
    echo "Hello General!";
}

function gmp_settings_email() {
    echo "Hello Email!";
}

function gmp_settings_page() {
    echo "Hello Setting!";
}

function gmp_settings_template() {
    echo "Hello Template!";
}