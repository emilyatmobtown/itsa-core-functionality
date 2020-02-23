<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class PriorityPostType {

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
		add_action( 'init', array( $this, 'register_priority_post_type' ) );
		add_action( 'save_post_priority', array( $this, 'set_title_and_slug' ), 15, 3 );
	}

	/**
	* Register custom post type
	*
	* @since 0.1.0
	**/
	public function register_priority_post_type() {
		$labels = array(
			// translators: This is the name of the Priority post type.
			'name'               => __( 'Priorities', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Priority post type.
			'singular_name'      => __( 'Priority', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Priority.
			'add_new'            => __( 'Add New Priority', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Priority.
			'add_new_item'       => __( 'Add New Priority', 'itsa-core-plugin' ),
			// translators: This is a label to edit a Priority.
			'edit_item'          => __( 'Edit Priority', 'itsa-core-plugin' ),
			// translators: This is a label for a new Priority.
			'new_item'           => __( 'New Priority', 'itsa-core-plugin' ),
			// translators: This is a label to show all Priorities.
			'all_items'          => __( 'All Priorities', 'itsa-core-plugin' ),
			// translators: This is a label to view a Priority.
			'view_item'          => __( 'View Priority', 'itsa-core-plugin' ),
			// translators: This is a label to search the Priorities.
			'search_items'       => __( 'Search Priorities', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Priorities are found.
			'not_found'          => __( 'No Priorities Found', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Priorities are found in Trash.
			'not_found_in_trash' => __( 'No Priorities Found in Trash', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Priorities.
			'menu_name'          => __( 'Priorities', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for Priorities.
			'label'               => __( 'Priorities', 'itsa-core-plugin' ),
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
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-star-filled',
			'supports'            => array( 'editor' ),
			'template'            => array(
				array( 'acf/priority' ),
			),
			'template_lock'       => 'all',
		);

		register_post_type( 'priority', $args );
	}

	/**
	* Set title
	*
	* @since 0.1.0
	**/
	public function set_title_and_slug( $post_ID, $post, $update ) {
		if ( 'priority' === get_post_type( $post_ID ) ) {
			$blocks = parse_blocks( $post->post_content );

			if ( isset( $blocks[0] ) && isset( $blocks[0]['blockName'] ) && 'acf/priority' === $blocks[0]['blockName'] && isset( $blocks[0]['attrs']['data'] ) ) {
				$the_title = $blocks[0]['attrs']['data']['title'];

				if ( ! empty( $the_title ) ) {
					$updated_post = array(
						'ID'         => $post_ID,
						'post_title' => sanitize_text_field( $the_title ),
						'post_name'  => sanitize_title( $the_title ),
					);

					// Unhook custom action to avoid infinite loop
					remove_action( 'save_post_priority', array( $this, 'set_title_and_slug' ), 15 );

					// Update post with new data
					wp_update_post( $updated_post );

					// Re-hook action for future use
					add_action( 'save_post_priority', array( $this, 'set_title_and_slug' ), 15, 3 );
				}
			}
		}
	}
}
