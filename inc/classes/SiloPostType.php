<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class SiloPostType {

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
		add_action( 'init', array( $this, 'register_silo_post_type' ) );
	}

	/**
	* Register custom post type
	*
	* @since 0.1.0
	**/
	public function register_silo_post_type() {
		$labels = array(
			// translators: This is the name of the Silo post type.
			'name'               => __( 'Silos', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Silo post type.
			'singular_name'      => __( 'Silo', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Silo post.
			'add_new'            => __( 'Add New Silo', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Silo post.
			'add_new_item'       => __( 'Add New Silo', 'itsa-core-plugin' ),
			// translators: This is a label to edit a  Silo post.
			'edit_item'          => __( 'Edit Silo', 'itsa-core-plugin' ),
			// translators: This is a label for a new Silo post.
			'new_item'           => __( 'New Silo', 'itsa-core-plugin' ),
			// translators: This is a label to show all Silo posts.
			'all_items'          => __( 'All Silos', 'itsa-core-plugin' ),
			// translators: This is a label to view a Silo post.
			'view_item'          => __( 'View Silo', 'itsa-core-plugin' ),
			// translators: This is a label to search the Silo posts.
			'search_items'       => __( 'Search Silos', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Silo posts are found.
			'not_found'          => __( 'No Silos Found', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Silo posts are found in Trash.
			'not_found_in_trash' => __( 'No Silos Found in Trash', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Silo posts.
			'menu_name'          => __( 'Silos', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for Silo posts.
			'label'               => __( 'Silos', 'itsa-core-plugin' ),
			'labels'              => $labels,
			// translators: This is the description for Silo posts.
			'description'         => __( 'Silos can be based on a theme or an issue. They enable landing pages for contextualized content.', 'itsa-core-plugin' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'rewrite'             => array(
				'slug' => 's',
			),
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => false,
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_position'       => 10,
			'menu_icon'           => 'dashicons-filter',
			'supports'            => array( 'title', 'editor', 'excerpt' ),
			'template'            => array(
				array( 'acf/header' ),
				array( 'acf/featured-post-grid' ),
				array( 'acf/content-grid' ),
				array( 'acf/priority-slider' ),
				array( 'acf/post-grid' ),
				array( 'acf/post-slider' ),
				array( 'acf/post-grid' ),
			),
			'template_lock'       => 'all',
		);

		register_post_type( 'silo', $args );
	}
}
