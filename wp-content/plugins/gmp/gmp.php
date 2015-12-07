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
    global $wp_version, $wpdb;
    if(version_compare($wp_version, "2.9", "<")) {
        deactivate_plugins(basename(__FILE__)); // Deactivate our plugin
        wp_die("This plugin requires WordPress version 2.9 or higher.");
    }

    //set the table structure version
    $gmp_db_version = "1.0";

    //define the custom table name
    $table_name = $wpdb->prefix . "gmp_ data";

    //verify the table doesn’t already exist
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

        //build our query to create our new table
        $sql = "CREATE TABLE " . $table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time bigint(11) DEFAULT ‘0’ NOT NULL,
            name tinytext NOT NULL,
            text text NOT NULL,
            url VARCHAR(55) NOT NULL,
            UNIQUE KEY id (id)
        );";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        //execute the query creating our table
        dbDelta( $sql );

        //save the table structure version number
        add_option("gmp_db_version", $gmp_db_version);
    }

    $installed_ver = get_option( "gmp_ db_version" );
    if( $installed_ver != $gmp_db_version ) {
        //update database table here

        //update table version
        update_option( "gmp_db_version", $gmp_db_version );
    }
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

add_action( "widgets_init", "gmp_register_widgets" );
function gmp_register_widgets() {
    register_widget( "gmp_widget" );
}

class gmp_widget extends WP_Widget {

    function gmp_widget() {
            $widget_ops = array('classname' => 'gmp_widget',
                'description' => __('Example widget that displays a user\'s bio.','gmp-plugin') );
            $this->WP_Widget('gmp_widget_bio',
                __('Bio Widget','gmp-plugin'), $widget_ops);
    }
    function form($instance) {
        $defaults = array( 'title' => __('My Bio','gmp-plugin'), 'name' => '', 'bio' => '' );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags($instance['title']);
        $name = strip_tags($instance['name']);
        $bio = strip_tags($instance['bio']);
        ?>
                <p><?php _e('Title', 'gmp-plugin') ?>: <input class="widefat"
                                                             name="<?php echo $this->get_field_name('title'); ?>"
                                                             type="text"
                                                             value="<?php echo esc_attr($title); ?>" /></p>
                <p><?php _e('Name', 'gmp-plugin') ?>: <input class="widefat"
                                                            name="<?php echo $this->get_field_name('name');?>"
                                                            type="text"
                                                            value="<?php echo esc_attr($name); ?>" /></p>
                <p><?php _e('Bio', 'gmp-plugin') ?>: <textarea class="widefat"
                                                      name="<?php echo $this->get_field_name('bio'); ?>"
            ><?php echo esc_attr($bio); ?></textarea></p>
        <?php
    }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['name'] = strip_tags($new_instance['name']);
        $instance['bio'] = strip_tags($new_instance['bio']);
        return $instance;
    }
    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
        $title = apply_filters('widget_title', $instance['title'] );
        $name = empty($instance['name']) ? '&nbsp;' : apply_filters('widget_name', $instance['name']);
        $bio = empty($instance['bio']) ? '&nbsp;' :
        apply_filters('widget_bio', $instance['bio']);
        if (!empty( $title ) ) { echo $before_title . $title . $after_title; };
        echo '<p>' .__('Name', 'gmp-plugin') .':' . $name . '</p>';
        echo '<p>' .__('Bio', 'gmp-plugin') .':' . $bio . '</p>';
        echo $after_widget;
    }
}

add_action('wp_dashboard_setup', 'gmp_add_dashboard_widget' );
// call function to create our dashboard widget
function gmp_add_dashboard_widget() {
    wp_add_dashboard_widget('gmp_dashboard_widget',
        __('GMP Dashboard Widget','gmp-plugin'), 'gmp_create_dashboard_widget');
}

// function to display our dashboard widget content
function gmp_create_dashboard_widget() {
    _e('Hello World! This is my Dashboard Widget', 'gmp-plugin');
}