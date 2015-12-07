<?php
/*
	Plugin Name: Post Products
	Plugin URI: http://webdevstudios.com/support/wordpress-plugins/
	Description: Easily add product data to posts.
	Version: 1.0
	Author: Brad Williams
	Author URI: http://webdevstudios.com
*/
/*
	Copyright 2010
	Brad Williams
	(email : brad@webdevstudios.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301
	USA
*/
// Call function when plugin is activated
register_activation_hook( __FILE__, 'pp_install' );
// Action hook to initialize the plugin
add_action( 'admin_init', 'pp_init' );
// Action hook to register our option settings
add_action( 'admin_init', 'pp_register_settings' );
// Action hook to add the post products menu item
add_action( 'admin_menu', 'pp_menu' );
// Action hook to save the meta box data when the post is saved
add_action( 'save_post', 'pp_save_meta_box' );
// Action hook to create the post products shortcode
add_shortcode( 'pp', 'pp_shortcode' );
// Action hook to create plugin widget
add_action( 'widgets_init', 'pp_register_widgets' );

function pp_install() {
	//setup our default option values
	$pp_options_arr = array(
		"currency_sign" => '$'
	);
	//save our default option values
	update_option( 'pp_options', $pp_options_arr );
}

//create the post products sub-menu
function pp_menu() {
	add_options_page(__( 'Post Products Settings Page', 'pp-plugin' ),
		__( 'Post Products Settings', 'pp-plugin' ), 'administrator',
		__FILE__, 'pp_settings_page' );
}

//create post meta box
function pp_init() {
	// create our custom meta box
	add_meta_box( 'pp-meta', __( 'Post Product Information', 'pp-plugin' ),
		'pp_meta_box', 'post', 'side', 'default' );
}

//create shortcode
function pp_shortcode( $atts, $content = null ) {
	global $post;
	extract( shortcode_atts( array(
		"show" => ''
	), $atts ) );
	//load options array
	$pp_options = get_option( 'pp_options' );
	if ( $show == 'sku' ) {
		$pp_show = get_post_meta( $post->ID, 'pp_sku', true );
	} elseif ( $show == 'price' ) {
		$pp_show = $pp_options['currency_sign'] .
		           get_post_meta( $post->ID, 'pp_price', true );
	} elseif ( $show == 'weight' ) {
		$pp_show = get_post_meta( $post->ID, 'pp_weight', true );
	} elseif ( $show == 'color' ) {
		$pp_show = get_post_meta( $post->ID, 'pp_color', true );
	} elseif ( $show == 'inventory' ) {
		$pp_show = get_post_meta( $post->ID, 'pp_inventory', true );
	}

	return $pp_show;
}

//build post product meta box
function pp_meta_box( $post, $box ) {
	// retrieve our custom meta box values
	$pp_sku       = get_post_meta( $post->ID, 'pp_sku', true );
	$pp_price     = get_post_meta( $post->ID, 'pp_price', true );
	$pp_weight    = get_post_meta( $post->ID, 'pp_weight', true );
	$pp_color     = get_post_meta( $post->ID, 'pp_color', true );
	$pp_inventory = get_post_meta( $post->ID, 'pp_inventory', true );
	// display meta box form
	echo '<table>';
	echo '<tr>';
	echo '<td>' . __( 'Sku', 'pp-plugin' ) .
	     ':</td><td><input type="text" name="pp_sku" value="' . esc_attr( $pp_sku ) .
	     '" size="10"></td>';
	echo '</tr><tr>';
	echo '<td>' . __( 'Price', 'pp-plugin' ) .
	     ':</td><td><input type="text" name="pp_price" value="' . esc_attr( $pp_price ) .
	     '" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' . __( 'Weight', 'pp-plugin' ) .
	     ':</td><td><input type="text" name="pp_weight" value="' . esc_attr( $pp_weight ) . '"
	size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' . __( 'Color', 'pp-plugin' ) .
	     ':</td><td><input type="text" name="pp_color" value="' . esc_attr( $pp_color ) .
	     '" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>Inventory:</td><td><select name="pp_inventory" id="pp_inventory">
	<option value="' . __( 'In Stock', 'pp-plugin' ) .
	     '" ' . ( is_null( $pp_inventory ) || $pp_inventory == __( 'In Stock', 'pp-plugin' ) ?
			'selected="selected" ' : '' ) . '>' . __( 'In Stock', 'pp-plugin' ) . '</option>
	<option value="' . __( 'Backordered', 'pp-plugin' ) . '"
	' . ( $pp_inventory == __( 'Backordered', 'pp-plugin' ) ?
			'selected="selected" ' : '' ) . '>' . __( 'Backordered', 'pp-plugin' ) . '</option>
	<option value="' . __( 'Out of Stock', 'pp-plugin' ) .
	     '" ' . ( $pp_inventory == __( 'Out of Stock', 'pp-plugin' ) ?
			'selected="selected" ' : '' ) . '>' . __( 'Out of Stock', 'pp-plugin' ) . '</option>
	<option value="' . __( 'Discontinued', 'pp-plug
	in' ) .
	     '" ' . ( $pp_inventory == __( 'Discontinued', 'pp-plugin' ) ?
			'selected="selected" ' : '' ) . '>' . __( 'Discontinued', 'pp-plugin' ) . '</option>
	</select></td>';
	echo '</tr>';
	//display the meta box shortcode legend section
	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td colspan="2"><strong>' . __( 'Shortcode Legend', 'pp-plugin' )
	     . '</strong></td></tr>';
	echo '<tr><td>' . __( 'Sku', 'pp-plugin' ) . ':</td><td>[pp show=sku]</td></tr>';
	echo '<tr><td>' . __( 'Price', 'pp-plugin' )
	     . ':</td><td>[pp show=price]</td></tr>';
	echo '<tr><td>' . __( 'Weight', 'pp-plugin' )
	     . ':</td><td>[pp show=weight]</td></tr>';
	echo '<tr><td>' . __( 'Color', 'pp-plugin' )
	     . ':</td><td>[pp show=color]</td></tr>';
	echo '<tr><td>' . __( 'Inventory', 'pp-plugin' )
	     . ':</td><td>[pp show=inventory]</td></tr>';
	echo '</table>';
}

//save meta box data
function pp_save_meta_box( $post_id=0, $post="" ) {
	// if post is a revision skip saving our meta box data
	if ( !empty($post) && $post->post_type == 'revision' ) {
		return;
	}
	// process form data if $_POST is set
	if ( isset( $_POST['pp_sku'] ) && $_POST['pp_sku'] != '' ) {
		// save the meta box data as post meta using the post ID as a unique prefix
		update_post_meta( $post_id, 'pp_sku', esc_attr( $_POST['pp_sku'] ) );
		update_post_meta( $post_id, 'pp_price', esc_attr( $_POST['pp_price'] ) );
		update_post_meta( $post_id, 'pp_weight', esc_attr( $_POST['pp_weight'] ) );
		update_post_meta( $post_id, 'pp_color', esc_attr( $_POST['pp_color'] ) );
		update_post_meta( $post_id, 'pp_inventory', esc_attr( $_POST['pp_inventory'] ) );
	}
}

//register our widget
function pp_register_widgets() {
	register_widget( 'pp_widget' );
}

//pp_widget class
class pp_widget extends WP_Widget {
//process our new widget
	function pp_widget() {
		$widget_ops = array(
			'classname'   => 'pp_widget',
			'description' =>
				__( 'Display Post Products', 'pp-plugin' )
		);
		$this->WP_Widget( 'pp_widget', __( 'Post Products Widget', 'pp-plugin' ),
			$widget_ops );
	}

//build our widget settings form
	function form( $instance ) {
		$defaults        = array(
			'title' => __( 'Products', 'pp-plugin' ),
			'number_products'
			        => ''
		);
		$instance        = wp_parse_args( (array) $instance, $defaults );
		$title           = strip_tags( $instance['title'] );
		$number_products = strip_tags( $instance['number_products'] );
		?>
		<p><?php _e( 'Title', 'pp-plugin' ) ?>: <input class="widefat"
		                                               name="<?php echo $this->get_field_name( 'title' ); ?>"
		                                               type="text" value="<?php echo
			esc_attr( $title ); ?>"/></p>
		<p><?php _e( 'Number of Products', 'pp-plugin' ) ?>: <input
				name="<?php echo $this->get_field_name( 'number_products' ); ?>"
				type="text" value="<?php echo esc_attr( $number_products ); ?>"
				size="2" maxlength="2"/></p>
		<?php
	}

//save our widget settings
	function update( $new_instance, $old_instance ) {
		$instance                    = $old_instance;
		$instance['title']           = strip_tags( esc_attr( $new_instance['title'] ) );
		$instance['number_products'] = intval( $new_instance['number_products'] );

		return $instance;
	}

//display our widget
	function widget( $args, $instance ) {
		global $post;
		extract( $args );
		echo $before_widget;
		$title           = apply_filters( 'widget_title', $instance['title'] );
		$number_products = empty( $instance['number_products'] ) ?
			'&nbsp;' : apply_filters( 'widget_number_products', $instance['number_products'] );
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		};
		$dispProducts = new WP_Query();
		$dispProducts->query( 'meta_key=pp_sku&showposts=' . $number_products );
		while ( $dispProducts->have_posts() ) : $dispProducts->the_post();
//load options array
			$pp_options = get_option( 'pp_options' );
//load custom meta values
			$pp_price     = get_post_meta( $post->ID, 'pp_price', true );
			$pp_inventory = get_post_meta( $post->ID, 'pp_inventory', true );
			?><p><a href="<?php the_permalink() ?>" rel="bookmark"
			        title="<?php the_title_attribute(); ?> Product Information">
				<?php the_title(); ?></a></p><?php
			echo '<p>' . __( 'Price', 'pp-plugin' ) . ': '
			     . $pp_options['currency_sign'] . $pp_price . '</p>';
//check if Show Inventory option is enabled
			If ( !empty($pp_options['show_inventory']) ) {
				echo '<p>' . __( 'Stock', 'pp-plugin' ) . ': ' . $pp_inventory . '</p>';
			}
			echo '<hr>';
		endwhile;
		echo $after_widget;
	}
}

function pp_register_settings() {
//register our array of settings
	register_setting( 'pp-settings-group', 'pp_options' );
}

function pp_settings_page() {
//load our options array
	$pp_options = get_option( 'pp_options' );
// if the show inventory option exists the checkbox needs to be checked
	If ( !empty($pp_options['show_inventory']) ) {
		$checked = ' checked="checked" ';
	}
	$pp_currency = $pp_options['currency_sign'];
	?>
	<div class="wrap">
		<h2><?php _e( 'Post Products Options', 'pp-plugin' ) ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'pp-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Show Product Inventory', 'pp-plugin' ) ?></th>
					<td><input type="checkbox" name="pp_options[show_inventory]"
							<?php echo !empty($checked)?$checked:""; ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Currency Sign', 'pp-plugin' ) ?></th>
					<td><input type="text" name="pp_options[currency_sign]"
					           value="<?php echo $pp_currency; ?>" size="1" maxlength="1"/></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary"
				       value="<?php _e( 'Save Changes', 'pp-plugin' ) ?>"/>
			</p>
		</form>
	</div>
	<?php
}