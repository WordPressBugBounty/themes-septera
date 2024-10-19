<?php
/**
 * Admin theme page
 *
 * @package Septera
 */

// Theme particulars
require_once( get_template_directory() . "/admin/defaults.php" );
require_once( get_template_directory() . "/admin/options.php" );
require_once( get_template_directory() . "/includes/tgmpa.php" );

// Custom CSS Styles for customizer
require_once( get_template_directory() . "/includes/custom-styles.php" );

// load up theme options
$cryout_theme_settings = apply_filters( 'septera_theme_structure_array', $septera_big );
$cryout_theme_options = septera_get_theme_options();
$cryout_theme_defaults = septera_get_option_defaults();

// Get the theme options and make sure defaults are used if no values are set
function septera_get_theme_options() {
	$options = wp_parse_args(
		get_option( 'septera_settings', array() ),
		septera_get_option_defaults()
	);
	$options = cryout_maybe_migrate_options( $options );
	return apply_filters( 'septera_theme_options_array', $options );
} // septera_get_theme_options()

function septera_get_theme_structure() {
	global $septera_big;
	return apply_filters( 'septera_theme_structure_array', $septera_big );
} // septera_get_theme_structure()

// backwards compatibility filter for some values that changed format
// this needs to be applied to the options array using WordPress' 'option_{$option}' filter
function septera_options_back_compat( $options ){
	if (!empty($options[_CRYOUT_THEME_PREFIX . '_lineheight'])) 		$options[_CRYOUT_THEME_PREFIX . '_lineheight']			= floatval( $options[_CRYOUT_THEME_PREFIX . '_lineheight'] );
	if (!empty($options[_CRYOUT_THEME_PREFIX . '_paragraphspace'])) 	$options[_CRYOUT_THEME_PREFIX . '_paragraphspace'] 		= floatval( $options[_CRYOUT_THEME_PREFIX . '_paragraphspace'] );
	if (!empty($options[_CRYOUT_THEME_PREFIX . '_parindent'])) 			$options[_CRYOUT_THEME_PREFIX . '_parindent'] 			= floatval( $options[_CRYOUT_THEME_PREFIX . '_parindent'] );
	if (!empty($options[_CRYOUT_THEME_PREFIX . '_responsivelimit']))	$options[_CRYOUT_THEME_PREFIX . '_responsivelimit'] 	= intval( $options[_CRYOUT_THEME_PREFIX . '_responsivelimit'] );
	return $options;
} // 
add_filter( 'option_septera_settings', 'septera_options_back_compat' );

// Hooks/Filters
add_action( 'admin_menu', 'septera_add_page_fn' );

// Add admin scripts
function septera_admin_scripts( $hook ) {
	global $septera_page;
	if( $septera_page != $hook ) {
        return;
    }

	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	wp_enqueue_style( 'septera-admin-style', esc_url( get_template_directory_uri() ) . '/admin/css/admin.css', NULL, _CRYOUT_THEME_VERSION );
	wp_enqueue_script( 'septera-admin-js', esc_url( get_template_directory_uri() ) . '/admin/js/admin.js', array('jquery-ui-dialog'), _CRYOUT_THEME_VERSION );
	$js_admin_options = array(
		'reset_confirmation' => esc_html( __( 'Reset Septera Settings to Defaults?', 'septera' ) ),
	);
	wp_localize_script( 'septera-admin-js', 'cryout_admin_settings', $js_admin_options );
}

// Create admin subpages
function septera_add_page_fn() {
	global $septera_page;
	$septera_page = add_theme_page( __( 'Septera Theme', 'septera' ), __( 'Septera Theme', 'septera' ), 'edit_theme_options', 'about-septera-theme', 'septera_page_fn' );
	add_action( 'admin_enqueue_scripts', 'septera_admin_scripts' );
} // septera_add_page_fn()

// Display the admin options page

function septera_page_fn() {

	if (!current_user_can('edit_theme_options'))  {
		wp_die( __( 'Sorry, but you do not have sufficient permissions to access this page.', 'septera') );
	}

?>

<div class="wrap" id="main-page"><!-- Admin wrap page -->
	<div id="lefty">
	<?php if( isset($_GET['settings-loaded']) ) { ?>
		<div class="updated fade">
			<p><?php _e('Septera settings loaded successfully.', 'septera') ?></p>
		</div> <?php
	} ?>
	<?php
	// Reset settings to defaults if the reset button has been pressed
	if ( isset( $_POST['cryout_reset_defaults'] ) ) {
		delete_option( 'septera_settings' ); ?>
		<div class="updated fade">
			<p><?php _e('Septera settings have been reset successfully.', 'septera') ?></p>
		</div> <?php
	} ?>

		<div id="admin_header">
			<img src="<?php echo esc_url( get_template_directory_uri() ) . '/admin/images/logo-about-top.png' ?>" />
			<span class="version">
				<?php _e( 'Septera Theme', 'septera' ) ?> v<?php echo _CRYOUT_THEME_VERSION; ?> by
				<a href="https://www.cryoutcreations.eu" target="_blank">Cryout Creations</a><br>
				<?php do_action( 'cryout_admin_version' ); ?>
			</span>
		</div>

		<div id="admin_links">
			<a href="https://www.cryoutcreations.eu/wordpress-themes/septera" target="_blank"><?php _e( 'Septera Homepage', 'septera' ) ?></a>
			<a href="https://www.cryoutcreations.eu/forums/f/wordpress/septera" target="_blank"><?php _e( 'Theme Support', 'septera' ) ?></a>
			<a class="blue-button" href="https://www.cryoutcreations.eu/wordpress-themes/septera#cryout-comparison-section" target="_blank"><?php _e( 'Upgrade to PLUS', 'septera' ) ?></a>
		</div>


		<br>
		<div id="description">
			<?php
				$theme = wp_get_theme();
			 	echo esc_html( $theme->get( 'Description' ) );
			?>
		</div>
		<br><br>

		<a class="button" href="customize.php" id="customizer"> <?php printf( __( 'Customize %s', 'septera' ), ucwords(_CRYOUT_THEME_NAME) ); ?> </a>

		<br>

				<form action="" method="post" class="third">
					<input type="hidden" name="cryout_reset_defaults" value="true" />
					<input type="submit" class="button" id="cryout_reset_defaults" value="<?php _e( 'Reset to Defaults', 'septera' ); ?>" />
				</form>

	</div><!--lefty -->


	<div id="righty">
		<div id="cryout-donate" class="postbox donate">

			<h3 class="hndle"><?php _e( 'Upgrade to Plus', 'septera' ); ?></h3>
			<div class="inside">
				<p><?php _e('Find out what features you\'re missing out on and how the Plus version of Septera can improve your site.', 'septera'); ?></p>
				<img src="<?php echo esc_url( get_template_directory_uri() ) . '/admin/images/features.png' ?>" />
				<a class="button" href="https://www.cryoutcreations.eu/wordpress-themes/septera" target="_blank" style="display: block;"><?php _e( 'Upgrade to Plus', 'septera' ); ?></a>

			</div><!-- inside -->

		</div><!-- donate -->

	</div><!--  righty -->
</div><!--  wrap -->

<?php
} // septera_page_fn()
