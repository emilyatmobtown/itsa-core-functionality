<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class SiloTypeTaxonomy {

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
		add_action( 'init', array( $this, 'register_silo_type_taxonomy' ) );
		add_action( 'pre_insert_term', array( $this, 'restrict_new_terms' ), 0, 2 );
	}

	/**
	* Register custom taxonomy
	*
	* @since 0.1.0
	**/
	public function register_silo_type_taxonomy() {
		$labels = array(
			// translators: This is the name of the Silo Type taxonomy.
			'name'          => __( 'Silo Types', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Silo Type taxonomy.
			'singular_name' => __( 'Silo Type', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Silo Type name.
			'new_item_name' => __( 'New Silo Type Name', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Silo Type.
			'add_new_item'  => __( 'Add New Silo Type', 'itsa-core-plugin' ),
			// translators: This is a label to edit an Silo Type.
			'edit_item'     => __( 'Edit Silo Type', 'itsa-core-plugin' ),
			// translators: This is a label to update an Silo Type.
			'update_item'   => __( 'Update Silo Type', 'itsa-core-plugin' ),
			// translators: This is a label to view all Silo Types.
			'all_items'     => __( 'All Silo Types', 'itsa-core-plugin' ),
			// translators: This is a label to view an Silo Type.
			'view_item'     => __( 'View Silo Type', 'itsa-core-plugin' ),
			// translators: This is a label to search Silo Types.
			'search_items'  => __( 'Search Silo Types', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Silo Types are found.
			'not_found'     => __( 'No Silo Types Found', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Silo Types.
			'menu_name'     => __( 'Silo Types', 'itsa-core-plugin' ),
			// translators: This is a label to view popular Silo Types.
			'popular_items' => __( 'Popular Silo Types', 'itsa-core-plugin' ),
			// translators: This is the link displayed after an Silo Type has been updated.
			'back_to_items' => __( 'Back to Silo Types', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for News Types.
			'label'              => __( 'Silo Types', 'itsa-core-plugin' ),
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

		register_taxonomy( 'silo-type', array( 'silo' ), $args );
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
