<?php
/*
	Plugin Name: Personal Resume CV Plugin
	Plugin URI: http://donbwhite.com/cvplugin
	Description: Show off your resume with style displaying your Education, Work Experience, and Skills with the power of WordPress, Custom Post Types and shortcodes to easily add your experience to post and pages.
	Version: 1.0
	Author: Donald White
	Author URI: http://donbwhite.com
*/
?>
<?php
// create custom plugin settings menu
add_action('admin_menu', 'cvr_create_menu');

function cvr_create_menu() {

	//create new top-level menu
	add_menu_page('CV Profile', 'CV Profile', 5, 'cv_settings.php', 'cvresume_settings_page', plugins_url('/img/cvicon.png'), 25);

	//call register settings function
	add_action( 'admin_init', 'register_cvrsettings' );
}


function register_cvrsettings() {
	//register our settings
	register_setting( 'cvr-settings-group', 'cvr_name' );
	register_setting( 'cvr-settings-group', 'cvr_phone_number' );
	register_setting( 'cvr-settings-group', 'cvr_location' );
	register_setting( 'cvr-settings-group', 'cvr_email' );
	register_setting( 'cvr-settings-group', 'cvr_link' );
	register_setting( 'cvr-settings-group', 'cvr_twitter' );
	register_setting( 'cvr-settings-group', 'cvr_facebook' );
	register_setting( 'cvr-settings-group', 'cvr_linkedin' );
	register_setting( 'cvr-settings-group', 'cvr_gplus' );		
}

function cvresume_settings_page() {
?>
<div class="wrap">
<?php if ( false !== $_REQUEST['updated'] ) : ?>
<div id="message" class="updated">
<p>Settings has been saved.</p>
</div>
<?php else :?>
    <?php endif; // If the form has just been submitted, this shows the notification ?>
 
<div id="icon-plugins" class="icon32"></div><h2>CV / Resume Profile</h2>

<form method="post" action="options.php">
        
    <?php settings_fields('cvr-settings-group'); ?>
    <table class="form-table">
		<h3><?php _e('Personal Information'); ?></h3>
        <tr valign="top">
        <th scope="row">Name</th>
        <td><input type="text" name="cvr_name" value="<?php echo get_option('cvr_name'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">eMail Address</th>
        <td><input type="text" name="cvr_email" value="<?php echo get_option('cvr_email'); ?>" /></td>
        </tr> 
        
        <tr valign="top">
        <th scope="row">Phone Number</th>
        <td><input type="text" name="cvr_phone_number" value="<?php echo get_option('cvr_phone_number'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Address</th>
        <td><input type="text" name="cvr_location" value="<?php echo get_option('cvr_location'); ?>" /></td>
        </tr>
    </table>
    <br />
    <table class="form-table">
		<h3><?php _e('Social Media'); ?></h3>
        <tr valign="top">
        <th scope="row">Facebook</th>
        <td><input type="text" name="cvr_facebook" value="<?php echo get_option('cvr_facebook'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Twitter</th>
        <td><input type="text" name="cvr_twitter" value="<?php echo get_option('cvr_twitter'); ?>" /></td>
        </tr> 
        
        <tr valign="top">
        <th scope="row">LinkedIn</th>
        <td><input type="text" name="cvr_linkedin" value="<?php echo get_option('cvr_linkedin'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Google+</th>
        <td><input type="text" name="cvr_gplus" value="<?php echo get_option('cvr_gplus'); ?>" /></td>
        </tr>
    </table>
</div>
<input class='button-primary' type='submit' name='Save' value='<?php _e('Save Options'); ?>' id='submitbutton' />
 
<?php } ?>

<?php
/**
 * Enable Sort menu
 *
 * @return void
 * @author Soul
 **/
function enable_cvresume_sort() {
    add_submenu_page('cv_settings.php', 'Organize Resume', 'Organize', 'edit_posts', 'cv_organize.php', 'sort_cvresume');
}
add_action('admin_menu' , 'enable_cvresume_sort'); 
 
 
/**
 * Display Sort admin
 *
 * @return void
 * @author Soul
 **/
function sort_cvresume() {
	$education = new WP_Query('post_type=education&posts_per_page=-1&orderby=menu_order');
	$employment = new WP_Query('post_type=employment&posts_per_page=-1&orderby=menu_order');
?>
	<div class="wrap">
	<h3>Sort Employement <img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h3>
	<ul id="cvresume-list">
	<?php while ( $employment->have_posts() ) : $employment->the_post(); ?>
		<li id="<?php the_id(); ?>"><?php the_title(); ?></li>			
	<?php endwhile; ?>
	<h3>Sort Education <img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h3>
	<ul id="cvresume-list">
	<?php while ( $education->have_posts() ) : $education->the_post(); ?>
		<li id="<?php the_id(); ?>"><?php the_title(); ?></li>			
	<?php endwhile; ?>


	</div><!-- End div#wrap //-->
 
<?php
}


?>
<?php


function cvresume_print_scripts( $hook ) {

    if ( $hook != 'cv-profile_page_cv_organize' ) return;

    wp_enqueue_style('cvresumsStyleSheet', plugins_url( '/css/cvr-sort.css', __FILE__ ) );
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('cvresumeScript', plugins_url( '/js/cvresume.js', __FILE__ ) );

}
add_action( 'admin_enqueue_scripts', 'cvresume_print_scripts' );

function save_cvresume_order() {
	global $wpdb; // WordPress database class
 
	$order = explode(',', $_POST['order']);
	$counter = 0;
 
	foreach ($order as $cvresume_id) {
		$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $cvresume_id) );
		$counter++;
	}
	die(1);
}
add_action('wp_ajax_cvresume_sort', 'save_cvresume_order');


 ?>
<?php 

function bio_shortcode()
{
	echo '<div class="profile">';
	echo get_avatar( get_option('cvr_email'), $size = '150', $default = '<path_to_url>' ); 
	echo '<h2>'; echo get_option('cvr_name'); echo '</h2>';
	echo '<h2>eMail:  '; echo '<span style="color:rgba(0,204,51,.5)">';echo get_option('cvr_email'); echo '</span></h2>';
	echo '<h2>Phone:  '; echo '<span style="color:rgba(0,204,51,.5)">';echo get_option('cvr_phone_number'); echo '</span></h2>';
	echo '</div>';
	echo '<div class="clearfix"></div>';
		 echo '<style type="text/css">
		 .profile h2 {font-family: Droid Sans, arial, san-serif; font-size:22px !important; font-weight: normal !important; margin-left:25px; margin-bottom:30px; background:none;}
.profile {margin-bottom:20px; min-height: 150px;}
.profile img {float:left; margin-right:25px;box-shadow: 3px 3px 1px, 3px 0 1px, 0 3px 1px;}
.clear {
  clear: both;
  display: block;
  overflow: hidden;
  visibility: hidden;
  width: 0;
  height: 0;
}
.clearfix:before,
.clearfix:after {
  display: block;
  overflow: hidden;
  visibility: hidden;
  width: 0;
  height: 0;
}
.clearfix:after {
  clear: both;
}
.clearfix {
  zoom: 1;
}
</style>';
 echo wp_enqueue_style('cvoutput', WP_PLUGIN_URL . '/css/style.css');
	}

add_shortcode('bioinfo', 'bio_shortcode');
?>

<?php
	require_once ( $WP_PLUGIN_DIR . '/employment.php');
	require_once ( $WP_PLUGIN_DIR . '/education.php');
	require_once ( $WP_PLUGIN_DIR . '/skills.php');