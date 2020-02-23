<?php
/**
 * Plugin Name:         ITSA Core Functionality
 * Description:         This contains core functionality for the ITSA site. <strong>It should always be activated</strong>.
 * Version:             0.1.0
 * Requires at least:   5.2
 * Requires PHP:        7.0
 * Author:              Emily Leffler Schulman, Mobtown Studios
 * Author URI:          https://emilylefflerschulman.com
 * License:             GPLv2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         itsa-core-plugin
 * Domain Path:         /languages
 *
 * @package             ITSACoreFunctionality
 */

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

// Global constants
define( 'ICF_VERSION', '0.1.0' );
define( 'ICF_URL', plugin_dir_url( __FILE__ ) );
define( 'ICF_PATH', plugin_dir_path( __FILE__ ) );
define( 'ICF_INC', ICF_PATH . 'inc/' );

// Include files
require_once ICF_INC . 'classes/SocialIconWidget.php';
require_once ICF_INC . 'core.php';
require_once ICF_INC . 'duplicate-post.php';
require_once ICF_INC . 'classes/AcfFieldTimezonePicker.php';
require_once ICF_INC . 'classes/AcfConditionalLogicAdvanced.php';

register_activation_hook( __FILE__, '\ITSACoreFunctionality\Core\activate' );
register_deactivation_hook( __FILE__, '\ITSACoreFunctionality\Core\deactivate' );

// Set up
Core\setup();

// Load classes
spl_autoload_register(
	function( $class ) {
		// project-specific namespace prefix.
		$prefix = 'ITSACoreFunctionality\\';
		// base directory for the namespace prefix.
		$base_dir = ICF_INC . 'classes/';
		// does the class use the namespace prefix?
		$len = strlen( $prefix );

		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

			// if the file exists, require it.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

// Set up ACF Field Timezone Picker
// AcfFieldTimezonePicker::factory();

// Set up ACF Conditional Logic Advanced
// AcfConditionalLogicAdvanced::factory();

// Set Up Advocacy Material Post Type
AdvocacyMaterialPostType::factory();

// Set Up Event Post Type
EventPostType::factory();

// Set Up News Post Type
NewsPostType::factory();

// Set Up Priority Post Type
PriorityPostType::factory();

// Set Up Quote Post Type
QuotePostType::factory();

// Set Up Silo Post Type
SiloPostType::factory();

// Set Up Block Area Post Type
BlockAreaPostType::factory();

// Set Up News Type Taxonomy
NewsTypeTaxonomy::factory();

// Set Up Event Type Taxonomy
EventTypeTaxonomy::factory();

// Set Up Advocacy Material Type Taxonomy
AdvocacyMaterialTypeTaxonomy::factory();

// Set Up Issue Taxonomy
IssueTaxonomy::factory();

// Set Up DisplayTweets
DisplayTweets::factory();
