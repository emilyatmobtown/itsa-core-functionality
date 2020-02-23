<?php

namespace ITSACoreFunctionality;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class BlockAreaPostType {

	/**
	 * Return singleton instance of class
	 *
	 * @return self
	 * @since  0.1.0
	 */
	public static function factory() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
			$instance->setup();
		}
		return $instance;
	}

	/**
	 * Initialize class
	 *
	 * @since 0.1.0
	 */
	public function setup() {
		add_action( 'init', array( $this, 'register_block_area_post_type' ) );
	}

	/**
	* Register custom post type
	*
	* @since 0.1.0
	**/
	public function register_block_area_post_type() {
		$labels = array(
			// translators: This is the name of the Block Area post type.
			'name'               => __( 'Block Areas', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Block Area post type.
			'singular_name'      => __( 'Block Area', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Block Area post.
			'add_new'            => __( 'Add New Block Area', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Block Area post.
			'add_new_item'       => __( 'Add New Block Area', 'itsa-core-plugin' ),
			// translators: This is a label to edit a  Block Area post.
			'edit_item'          => __( 'Edit Block Area', 'itsa-core-plugin' ),
			// translators: This is a label for a new Block Area post.
			'new_item'           => __( 'New Block Area', 'itsa-core-plugin' ),
			// translators: This is a label to show all Block Area posts.
			'all_items'          => __( 'All Block Areas', 'itsa-core-plugin' ),
			// translators: This is a label to view a Block Area post.
			'view_item'          => __( 'View Block Area', 'itsa-core-plugin' ),
			// translators: This is a label to search the Block Area posts.
			'search_items'       => __( 'Search Block Areas', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Block Area posts are found.
			'not_found'          => __( 'No Block Areas Found', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Block Area posts are found in Trash.
			'not_found_in_trash' => __( 'No Block Areas Found in Trash', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Block Area posts.
			'menu_name'          => __( 'Block Areas', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for Block Area posts.
			'label'               => __( 'Block Areas', 'itsa-core-plugin' ),
			'labels'              => $labels,
			'description'         => '',
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_position'       => 15,
			'menu_icon'           => 'dashicons-align-center',
			'supports'            => array( 'title', 'editor' ),
		);

		register_post_type( 'block_area', $args );
	}

	/**
	 * Redirect single block areas
	 *
	 */
	public function redirect_single() {
		if ( is_singular( 'block_area' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Show block area
	 *
	 */
	public function show( $location = '' ) {
		if ( ! $location ) {
			return;
		}

		$location = sanitize_key( $location );

		$loop = new WP_Query(
			array(
				'post_type'              => 'block_area',
				'name'                   => $location,
				'posts_per_page'         => 1,
				'post_status'            => 'publish',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
			)
		);

		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) {
				$loop->the_post();
				the_content();
			}
		}

		wp_reset_postdata();
	}

}

/**
 * The function provides access to the class methods.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @return object
 */
function block_area() {
	return BlockAreaPostType::factory();
}
block_area();
