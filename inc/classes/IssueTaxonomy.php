<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class IssueTaxonomy {

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
		add_action( 'init', array( $this, 'register_issue_taxonomy' ) );
		add_action( 'pre_insert_term', array( $this, 'restrict_new_terms' ), 0, 2 );
	}

	/**
	* Register custom taxonomy
	*
	* @since 0.1.0
	**/
	public function register_issue_taxonomy() {
		$labels = array(
			// translators: This is the name of the Issue taxonomy.
			'name'          => __( 'Issues', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Issue taxonomy.
			'singular_name' => __( 'Issue', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Issue name.
			'new_item_name' => __( 'New Issue Name', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Issue.
			'add_new_item'  => __( 'Add New Issue', 'itsa-core-plugin' ),
			// translators: This is a label to edit an Issue.
			'edit_item'     => __( 'Edit Issue', 'itsa-core-plugin' ),
			// translators: This is a label to update an Issue.
			'update_item'   => __( 'Update Issue', 'itsa-core-plugin' ),
			// translators: This is a label to view all Issues.
			'all_items'     => __( 'All Issues', 'itsa-core-plugin' ),
			// translators: This is a label to view an Issue.
			'view_item'     => __( 'View Issue', 'itsa-core-plugin' ),
			// translators: This is a label to search Issues.
			'search_items'  => __( 'Search Issues', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Issues are found.
			'not_found'     => __( 'No Issues Found', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Issues.
			'menu_name'     => __( 'Issues', 'itsa-core-plugin' ),
			// translators: This is a label to view popular Issues.
			'popular_items' => __( 'Popular Issues', 'itsa-core-plugin' ),
			// translators: This is the link displayed after an Issue has been updated.
			'back_to_items' => __( 'Back to Issues', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for Issues.
			'label'              => __( 'Issues', 'itsa-core-plugin' ),
			'labels'             => $labels,
			'description'        => '',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'has_archive'        => false,
			'rewrite'            => array(
				'slug' => 's',
			),
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

		register_taxonomy( 'issue', array( 'silo', 'news', 'advocacy-material', 'event', 'priority' ), $args );
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
