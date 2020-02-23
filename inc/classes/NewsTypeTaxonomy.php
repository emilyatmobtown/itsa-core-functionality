<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class NewsTypeTaxonomy {

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
		add_action( 'init', array( $this, 'register_news_type_taxonomy' ) );
		add_action( 'pre_insert_term', array( $this, 'restrict_new_terms' ), 0, 2 );
	}

	/**
	* Register custom taxonomy
	*
	* @since 0.1.0
	**/
	public function register_news_type_taxonomy() {
		$labels = array(
			// translators: This is the name of the News Type taxonomy.
			'name'          => __( 'News Types', 'itsa-core-plugin' ),
			// translators: This is the singular name of the News Type taxonomy.
			'singular_name' => __( 'News Type', 'itsa-core-plugin' ),
			// translators: This is a label to add a new News Type name.
			'new_item_name' => __( 'New Type Name', 'itsa-core-plugin' ),
			// translators: This is a label to add a new News Type.
			'add_new_item'  => __( 'Add New Type', 'itsa-core-plugin' ),
			// translators: This is a label to edit a News Type.
			'edit_item'     => __( 'Edit News Type', 'itsa-core-plugin' ),
			// translators: This is a label to update a News Type.
			'update_item'   => __( 'Update News Type', 'itsa-core-plugin' ),
			// translators: This is a label to view all News Types.
			'all_items'     => __( 'All News Types', 'itsa-core-plugin' ),
			// translators: This is a label to view a News Type.
			'view_item'     => __( 'View News Type', 'itsa-core-plugin' ),
			// translators: This is a label to search News Types.
			'search_items'  => __( 'Search News Types', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching News Types are found.
			'not_found'     => __( 'No News Types Found', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for News Types.
			'menu_name'     => __( 'News Types', 'itsa-core-plugin' ),
			// translators: This is a label to view popular News Types.
			'popular_items' => __( 'Popular News Types', 'itsa-core-plugin' ),
			// translators: This is the link displayed after a News Type has been updated.
			'back_to_items' => __( 'Back to News Types', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for News Types.
			'label'              => __( 'News Types', 'itsa-core-plugin' ),
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

		register_taxonomy( 'news-type', array( 'news' ), $args );
	}

	/**
	* Set title
	*
	* @since 0.1.0
	**/
	public function set_title_and_slug( $post_ID, $post, $update ) {
		if ( 'news' === get_post_type( $post_ID ) ) {
			$blocks = parse_blocks( $post->post_content );

			if ( isset( $blocks[0] ) && isset( $blocks[0]['blockName'] ) && 'acf/header' === $blocks[0]['blockName'] && isset( $blocks[0]['attrs']['data'] ) ) {
				$the_title = $blocks[0]['attrs']['data']['title'];

				if ( ! empty( $the_title ) ) {
					$updated_post = array(
						'ID'         => $post_ID,
						'post_title' => sanitize_text_field( $the_title ),
						'post_name'  => sanitize_title( $the_title ),
					);

					// Unhook custom action to avoid infinite loop
					remove_action( 'save_post_news', array( $this, 'set_title_and_slug' ), 15 );

					// Update post with new data
					wp_update_post( $updated_post );

					// Re-hook action for future use
					add_action( 'save_post_news', array( $this, 'set_title_and_slug' ), 15, 3 );
				}
			}
		}
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
