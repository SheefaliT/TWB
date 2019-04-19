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