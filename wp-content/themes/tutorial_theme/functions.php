<?php
/**
 * Register widget area.
 *
 * @since Twenty Fifteen 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
define('THEME_NAME', 'tutorial_theme');

function remove_menus () {
    global $menu;
    $restricted = array(
        __('Posts'),
        __('Pages'),
        __('WPML'),
        __('Tools'),
        __('Plugins')
    );
    end ($menu);
    while (prev($menu)){
        $value = explode(' ',$menu[key($menu)][0]);
        if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
            unset($menu[key($menu)]);
        }
    }
}
if(false == WP_DEBUG) {
    add_action('admin_menu', 'remove_menus');
}

function tutorial_theme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Widget Area 1', THEME_NAME ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Add widgets here to appear in your sidebar.', THEME_NAME ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => __( 'Widget Area 2', THEME_NAME ),
        'id'            => 'sidebar-2',
        'description'   => __( 'Add widgets here to appear in your sidebar.', THEME_NAME ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'tutorial_theme_widgets_init' );