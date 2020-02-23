<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class AdvocacyMaterialTypeTaxonomy {

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
		add_action( 'init', array( $this, 'register_advocacy_material_type_taxonomy' ) );
		add_action( 'pre_insert_term', array( $this, 'restrict_new_terms' ), 0, 2 );
	}

	/**
	* Register custom taxonomy
	*
	* @since 0.1.0
	**/
	public function register_advocacy_material_type_taxonomy() {
		$labels = array(
			// translators: This is the name of the Advocacy Material Type taxonomy.
			'name'          => __( 'Advocacy Material Types', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Advocacy Material Type taxonomy.
			'singular_name' => __( 'Advocacy Material Type', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Advocacy Material Type name.
			'new_item_name' => __( 'New Advocacy Material Type Name', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Advocacy Material Type.
			'add_new_item'  => __( 'Add New Advocacy Material Type', 'itsa-core-plugin' ),
			// translators: This is a label to edit an Advocacy Material Type.
			'edit_item'     => __( 'Edit Advocacy Material Type', 'itsa-core-plugin' ),
			// translators: This is a label to update an Advocacy Material Type.
			'update_item'   => __( 'Update Advocacy Material Type', 'itsa-core-plugin' ),
			// translators: This is a label to view all Advocacy Material Types.
			'all_items'     => __( 'All Advocacy Material Types', 'itsa-core-plugin' ),
			// translators: This is a label to view an Advocacy Material Type.
			'view_item'     => __( 'View Advocacy Material Type', 'itsa-core-plugin' ),
			// translators: This is a label to search Advocacy Material Types.
			'search_items'  => __( 'Search Advocacy Material Types', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Advocacy Material Types are found.
			'not_found'     => __( 'No Advocacy Material Types Found', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Advocacy Material Types.
			'menu_name'     => __( 'Advocacy Material Types', 'itsa-core-plugin' ),
			// translators: This is a label to view popular Advocacy Material Types.
			'popular_items' => __( 'Popular Advocacy Material Types', 'itsa-core-plugin' ),
			// translators: This is the link displayed after an Advocacy Material Type has been updated.
			'back_to_items' => __( 'Back to Advocacy Material Types', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for News Types.
			'label'              => __( 'Advocacy Material Types', 'itsa-core-plugin' ),
			'labels'             => $labels,
			'description'        => '',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'has_archive'        => false,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => false,
			'show_admin_column'  => true,
			'map_meta_cap'       => true,
			'hierarchical'       => true,
			'capabilities'       => array(
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'edit_posts',
			),
		);

		register_taxonomy( 'advocacy-material-type', array( 'advocacy-material' ), $args );
	}

	/**
	* Restrict the creation of new terms
	*
	* @param string|WP_Error $term
	* @param string $taxonomy
	* @since 0.1.0
	**/
	public function restrict_new_terms( $term, $taxonomy ) {
		if ( 'issue' === $taxonomy && ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'term_addition_blocked', __( 'You cannot add terms to this taxonomy,', 'itsa-core-plugin' ) );
		}

		return $term;
	}
}
