<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class EventTypeTaxonomy {

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
		add_action( 'init', array( $this, 'register_event_type_taxonomy' ) );
		add_action( 'pre_insert_term', array( $this, 'restrict_new_terms' ), 0, 2 );
	}

	/**
	* Register custom taxonomy
	*
	* @since 0.1.0
	**/
	public function register_event_type_taxonomy() {
		$labels = array(
			// translators: This is the name of the Event Type taxonomy.
			'name'          => __( 'Event Types', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Event Type taxonomy.
			'singular_name' => __( 'Event Type', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Event Type name.
			'new_item_name' => __( 'New Event Type Name', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Event Type.
			'add_new_item'  => __( 'Add New Event Type', 'itsa-core-plugin' ),
			// translators: This is a label to edit an Event Type.
			'edit_item'     => __( 'Edit Event Type', 'itsa-core-plugin' ),
			// translators: This is a label to update an Event Type.
			'update_item'   => __( 'Update Event Type', 'itsa-core-plugin' ),
			// translators: This is a label to view all Event Types.
			'all_items'     => __( 'All Event Types', 'itsa-core-plugin' ),
			// translators: This is a label to view an Event Type.
			'view_item'     => __( 'View Event Type', 'itsa-core-plugin' ),
			// translators: This is a label to search Event Types.
			'search_items'  => __( 'Search Event Types', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Event Types are found.
			'not_found'     => __( 'No Event Types Found', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Event Types.
			'menu_name'     => __( 'Event Types', 'itsa-core-plugin' ),
			// translators: This is a label to view popular Event Types.
			'popular_items' => __( 'Popular Event Types', 'itsa-core-plugin' ),
			// translators: This is the link displayed after an Event Type has been updated.
			'back_to_items' => __( 'Back to Event Types', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for News Types.
			'label'              => __( 'Event Types', 'itsa-core-plugin' ),
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

		register_taxonomy( 'event-type', array( 'event' ), $args );
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
