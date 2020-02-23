<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class NewsPostType {

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
		add_action( 'init', array( $this, 'register_news_post_type' ) );
		add_action( 'save_post_news', array( $this, 'set_title_and_slug' ), 15, 3 );
	}

	/**
	* Register custom post type
	*
	* @since 0.1.0
	**/
	public function register_news_post_type() {
		$labels = array(
			// translators: This is the name of the News post type.
			'name'               => __( 'News', 'itsa-core-plugin' ),
			// translators: This is the singular name of the News post type.
			'singular_name'      => __( 'News', 'itsa-core-plugin' ),
			// translators: This is a label to add a new News post.
			'add_new'            => __( 'Add News Item', 'itsa-core-plugin' ),
			// translators: This is a label to add a new News post.
			'add_new_item'       => __( 'Add News Item', 'itsa-core-plugin' ),
			// translators: This is a label to edit a News post.
			'edit_item'          => __( 'Edit News Item', 'itsa-core-plugin' ),
			// translators: This is a label for a new News post.
			'new_item'           => __( 'New News', 'itsa-core-plugin' ),
			// translators: This is a label to show all News posts.
			'all_items'          => __( 'All News', 'itsa-core-plugin' ),
			// translators: This is a label to view a News post.
			'view_item'          => __( 'View News Item', 'itsa-core-plugin' ),
			// translators: This is a label to search the News posts.
			'search_items'       => __( 'Search News', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching News posts are found.
			'not_found'          => __( 'No News Found', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching News posts are found in Trash.
			'not_found_in_trash' => __( 'No News Found in Trash', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for News posts.
			'menu_name'          => __( 'News', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for News posts.
			'label'               => __( 'News', 'itsa-core-plugin' ),
			'labels'              => $labels,
			'description'         => '',
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-megaphone',
			'supports'            => array( 'editor', 'excerpt' ),
			'template'            => array(
				array( 'acf/header' ),
			),
		);

		register_post_type( 'news', $args );
	}

	/**
	* Set title
	*
	* @since 0.1.0
	**/
	public function set_title_and_slug( $post_ID, $post, $update ) {
		if ( 'news' === get_post_type( $post_ID ) ) {
			$blocks = parse_blocks( $post->post_content );

			if ( isset( $blocks[0] ) && isset( $blocks[0]['blockName'] ) && isset( $blocks[0]['attrs']['data'] ) ) {
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
}
