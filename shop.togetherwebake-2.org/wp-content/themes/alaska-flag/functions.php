<?php // When WordPress version which is used in your blog is known, hacker can find proper exploit for exact version of WordPRess.
function no_generator() { return ''; }  
add_filter( 'the_generator', 'no_generator' );
?>
<?php //remove unncessary login error information
function explain_less_login_issues(){ return '<strong>ERROR</strong>: Entered credentials are incorrect.';}
add_filter( 'login_errors', 'explain_less_login_issues' );
// REMOVE EMOJI ICONS
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
?>
<?php //set alaska_custom_styles as stylesheet to default to for user edits
add_editor_style('alaska_custom_styles.css');
?>
<?php //set $content-width to widest image users can post
    if ( ! isset( $content_width ) ) $content_width = 2048;
?>
<?php // adds featured image support
    add_theme_support( 'post-thumbnails' ); 
?>
<?php 
$args = array(
	'width'         => '',
	'height'        => 240,
	'default-image' => get_template_directory_uri() . '/alaska_images/alaska_logo.jpg',
	'uploads'       => true,
);
add_theme_support( 'custom-background' );
add_theme_support( 'custom-header', $args )
?>
<?php //add custom background support
    add_theme_support( 'custom-background' )?>
<?php function my_custom_background_callback() {
	/* Get the background image. */
	$image = get_background_image();
	/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
	if ( !empty( $image ) ) {
		_custom_background_cb();
		return;
	}
	/* Get the background color. */
	$color = get_background_color();
	/* If no background color, return. */
	if ( empty( $color ) )
		return;
	/* Use 'background' instead of 'background-color'. */
	$style = "background: #{$color};";
 } ?>
<?php // add theme support for thumbnails
add_theme_support( 'post-thumbnails' ); 
?>
<?php // add theme support for automatic-feed-links
add_theme_support( 'automatic-feed-links' )
?>
<?php  // add custom Menus to Appearance - Menu section
function register_my_menus() {
  register_nav_menus(
    array(
      'top-menu' => 'Top Menu',
      'bottom-menu' => 'Bottom Menu',
      'bottom2-menu' => 'Bottom2 Menu',
      'bottom3-menu' => 'Bottom3 Menu',
      'social-menu' => 'Social Menu',
      )
  );
}
add_action( 'init', 'register_my_menus' );
?>
<?php // add custom Widgets to Appearance - Widget section
// Register widgetized areas
function theme_widgets_init() {
    register_sidebar( array (
    'name' => 'Left Sidebar',
    'id' => 'left_sidebar',
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<strong>',
    'after_title' => '</strong>',
  ) );
 
    register_sidebar( array (
    'name' => 'Right Sidebar',
    'id' => 'right_sidebar', 
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<strong>',
    'after_title' => '</strong>',
  ) );
} // end theme_widgets_init
 
add_action( 'init', 'theme_widgets_init' );

$preset_widgets = array (
    'left_sidebar'  => array( ),
    'right_sidebar'  => array( )
);

// update_option( 'sidebars_widgets', NULL );
// Check for static widgets in widget-ready areas
function is_sidebar_active( $index ){
  global $wp_registered_sidebars;
 
  $widgetcolums = wp_get_sidebars_widgets();
          
  if ($widgetcolums[$index]) return true;
   
    return false;
} // end is_sidebar_active
?>
<? // add categories for attachments
function add_categories_for_attachments() {register_taxonomy_for_object_type( 'category', 'attachment' ); } add_action( 'init' , 'add_categories_for_attachments' ); 
// add tags for attachments
function add_tags_for_attachments() {register_taxonomy_for_object_type( 'post_tag', 'attachment' ); } add_action( 'init' , 'add_tags_for_attachments' );
?>
<? function new_excerpt_more($more) {global $post;return '<a href="'. get_permalink($post->ID) . '">...Read More...</a>';}
add_filter('excerpt_more', 'new_excerpt_more');
?>
<? function my_deregister_scripts(){
  wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'my_deregister_scripts' );
?>
<? function your_theme_woocommerce_scripts() {
  wp_enqueue_style( 'custom-woocommerce-style', get_template_directory_uri() . '/alaska_custom_styles.css' );
}
?>
<? /**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 26;
  return $cols;
}
?>
<? function add_specific_menu_location_atts( $atts, $item, $args ) {
    // check if the item is in the primary menu
    if( $args->theme_location == 'top-menu' ) {
      // add the desired attributes:
      $atts['class'] = 'top-menu';
    }
   if( $args->theme_location == 'bottom-menu' ) {
      // add the desired attributes:
      $atts['class'] = 'bottom-menu';
    }
   if( $args->theme_location == 'bottom-menu' ) {
      // add the desired attributes:
      $atts['class'] = 'bottom2-menu';
    }
  return $atts;
}
add_filter( 'nav_menu_link_attributes', 'add_specific_menu_location_atts', 10, 3 );
?>
<? //add custom checkout fields
/**
 * Add the field to the checkout
 */
add_action( 'woocommerce_after_order_notes', 'time_checkout_field' );
function time_checkout_field( $checkout ) {
    echo '<div id="time_checkout_field"><strong>' . __('') . '</strong>';
    woocommerce_form_field( 'pickup_time', array(
        'type'          => 'text',
		'required'   	=> true,
        'class'         => array('pickup-time-class form-row-wide'),
        'label'         => __('<h3>Note your Pickup/Shipping - Date/Time Preference:</h3>'),
        'placeholder'   => __('1/1 10AM'),
        ), $checkout->get_value( 'pickup_time' ));
    echo '</div>';
}
/**
* Update the user meta with field value
**/
add_action('woocommerce_checkout_update_user_meta', 'time_checkout_field_update_user_meta');
function time_checkout_field_update_user_meta( $user_id ) {
if ($user_id && $_POST['pickup_time']) update_user_meta( $user_id, 'pickup_time', esc_attr($_POST['pickup_time']) );
}
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'time_checkout_field_update_order_meta' );
function time_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['pickup_time'] ) ) {
        update_post_meta( $order_id, 'Pickup/Ship Date/Time:', sanitize_text_field( $_POST['pickup_time'] ) );
    }}
/**
 Add the field to order emails
**/
add_filter('woocommerce_email_order_meta_keys', 'time_checkout_field_order_meta_keys');
function time_checkout_field_order_meta_keys( $keys ) {
$keys[] = 'Pickup/Ship Date/Time:';
return $keys;
}
?>