<?php
/**
 * Core plugin functionality.
 *
 * @package ITSACoreFunctionality
 */

namespace ITSACoreFunctionality\Core;

use ITSACoreFunctionality\Settings as Settings;
use \WP_Error as WP_Error;

/**
 * Set-up routine
 *
 * @since 0.1.0
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . '\\' . $function;
	};

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );
	add_action( 'init', $n( 'add_image_sizes' ) );
	add_action( 'init', $n( 'remove_comment_support' ), 100 );
	add_action( 'init', $n( 'add_rewrite_rules' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );
	add_action( 'admin_menu', $n( 'remove_default_post_types' ) );
	add_action( 'wp_before_admin_bar_render', $n( 'remove_default_post_type_menu_bar' ), 999 );
	add_action( 'wp_dashboard_setup', $n( 'update_dashboard' ), 999 );
	add_action( 'widgets_init', $n( 'add_widget' ) );

	// Allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	do_action( 'icf_loaded' );
}

/**
 * Registers the textdomain.
 *
 * @since 0.1.0
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'simple-before-and-after' );
	load_textdomain( 'itsa-core-plugin', WP_LANG_DIR . '/itsa-core-plugin/itsa-core-plugin-' . $locale . '.mo' );
	load_plugin_textdomain( 'itsa-core-plugin', false, plugin_basename( ICF_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @since 0.1.0
 */
function init() {
	do_action( 'icf_init' );
}

/**
 * Activate the plugin
 *
 * @since 0.1.0
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * @since 0.1.0
 */
function deactivate() {
	flush_rewrite_rules();
}


/**
 * The list of known contexts for enqueuing scripts/styles.
 *
 * @since 0.1.0
 */
function get_enqueue_contexts() {
	return [ 'admin', 'frontend' ];
}

/**
 * Generate a URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param  string          $script Script file name (no .js extension)
 * @param  string          $context Context for the script ('admin', 'frontend')
 * @return string|WP_Error URL
 * @since  0.1.0
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in the ICF script loader.' );
	}

	// Use minified file if possible
	if ( file_exists( ICF_URL . "dist/js/${context}/${script}.min.js" ) ) {
		return ICF_URL . "dist/js/${context}/${script}.min.js";
	} else {
		return ICF_URL . "dist/js/${context}/${script}.js";
	}
}

/**
 * Generate a URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param  string          $stylesheet Stylesheet file name (no .css extension)
 * @param  string          $context Context for the script ('admin', 'frontend')
 * @return string|WP_Error URL
 * @since  0.1.0
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in the ICF stylesheet loader.' );
	}

	// Use minified file if possible
	if ( file_exists( ICF_URL . "dist/css/${context}/${stylesheet}.min.css" ) ) {
		return ICF_URL . "dist/css/${context}/${stylesheet}.min.css";
	} else {
		return ICF_URL . "dist/css/${context}/${stylesheet}.css";
	}

}

/**
 * Enqueue scripts for front-end.
 *
 * @since 0.1.0
 */
function scripts() {

	// wp_enqueue_script(
	// 	'sba-script',
	// 	script_url( 'simple-before-and-after', 'frontend' ),
	// 	[],
	// 	ICF_VERSION,
	// 	true
	// );

}

/**
 * Enqueue scripts for admin.
 *
 * @since 0.1.0
 */
function admin_scripts() {
	global $typenow;

	if ( 'block_area' === $typenow ) {
		wp_enqueue_media();
		// wp_enqueue_script(
		// 	'sba-meta-box-image-loader',
		// 	script_url( 'sba-media', 'admin' ),
		// 	array( 'jquery' ),
		// 	ICF_VERSION,
		// 	true
		// );
		// wp_localize_script(
		// 	'sba-meta-box-image-loader',
		// 	'metaImage',
		// 	array(
		// 		// translators: This is the title of the image uploader.
		// 		'title'  => __( 'Choose or Upload Image', 'simple-before-and-after' ),
		// 		// translators: This is the button label for the image uploader.
		// 		'button' => __( 'Use This Image', 'simple-before-and-after' ),
		// 	)
		// );
	}

}

/**
 * Enqueue styles for front-end.
 *
 * @since 0.1.0
 */
function styles() {

	// wp_enqueue_style(
	// 	'sba-google-fonts',
	// 	'https://fonts.googleapis.com/css?family=Oswald&display=swap',
	// 	[],
	// 	SBA_VERSION
	// );
	//
	// wp_enqueue_style(
	// 	'sba-styles',
	// 	style_url( 'simple-before-and-after', 'frontend' ),
	// 	[],
	// 	SBA_VERSION
	// );

}

/**
 * Enqueue styles for admin.
 *
 * @since 0.1.0
 */
function admin_styles() {
	global $typenow;

	// if ( 'before_and_after' === $typenow ) {
	// 	wp_enqueue_style(
	// 		'sba-admin-styles',
	// 		style_url( 'sba-admin', 'admin' ),
	// 		[],
	// 		SBA_VERSION
	// 	);
	// }

}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link   https://core.trac.wordpress.org/ticket/12009
 * @param  string $tag    The script tag.
 * @param  string $handle The script handle.
 * @return string
 * @since  0.1.0
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag; // _doing_it_wrong()?
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}

/**
 * Add custom image size to image upload process
 *
 * @since 0.1.0
 */
function add_image_sizes() {
	// $settings = Settings\get_global_settings( true );
	//
	// if ( ! empty( $settings['image_width'] && ! empty( $settings['image_height'] ) ) ) {
	// 	add_image_size( 'sba-grid-image', $settings['image_width'], $settings['image_height'], true );
	// }
}

/**
 * Remove default post type(s) from admin menu
 *
 * @since 0.1.0
 */
function remove_default_post_types() {
	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );
}

/**
 * Remove default post type(s) from admin menu bar
 *
 * @param WP_Admin_Bar $wp_admin_bar
 *
 * @since 0.1.0
 */
function remove_default_post_type_menu_bar() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_node( 'new-post' );
	$wp_admin_bar->remove_menu( 'comments' );
}

/**
 * Update dashboard without unused clutter
 *
 * @since 0.1.0
 */
function update_dashboard() {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
}

/**
 * Remove support for comments on all post types
 *
 * @since 0.1.0
 */
function remove_comment_support() {
	$post_types = get_post_types();

	foreach ( $post_types as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
	}
}

/**
 * Registers social media icon widget
 *
 * @since  0.1.0
 */
function add_widget() {
	\register_widget( 'ITSACoreFunctionality\SocialIconWidget' );
}

/**
 * Rewrites permalinks
 *
 * @since  0.1.0
 */
function add_rewrite_rules() {
	add_rewrite_rule( '^issue/(.*)/?', 's/$matches[1]/', 'bottom' );
	flush_rewrite_rules();
}
