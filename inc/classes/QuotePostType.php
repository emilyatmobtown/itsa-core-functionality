<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class QuotePostType {

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
		add_action( 'init', array( $this, 'register_quote_post_type' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'set_title' ), 99, 2 );
	}

	/**
	* Register custom post type
	*
	* @since 0.1.0
	**/
	public function register_quote_post_type() {
		$labels = array(
			// translators: This is the name of the Quote post type.
			'name'               => __( 'Quotes', 'itsa-core-plugin' ),
			// translators: This is the singular name of the Quote post type.
			'singular_name'      => __( 'Quote', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Quote post.
			'add_new'            => __( 'Add New Quote', 'itsa-core-plugin' ),
			// translators: This is a label to add a new Quote post.
			'add_new_item'       => __( 'Add New Quote', 'itsa-core-plugin' ),
			// translators: This is a label to edit a  Quote post.
			'edit_item'          => __( 'Edit Quote', 'itsa-core-plugin' ),
			// translators: This is a label for a new Quote post.
			'new_item'           => __( 'New Quote', 'itsa-core-plugin' ),
			// translators: This is a label to show all Quote posts.
			'all_items'          => __( 'All Quotes', 'itsa-core-plugin' ),
			// translators: This is a label to view a Quote post.
			'view_item'          => __( 'View Quote', 'itsa-core-plugin' ),
			// translators: This is a label to search the Quote posts.
			'search_items'       => __( 'Search Quotes', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Quote posts are found.
			'not_found'          => __( 'No Quotes Found', 'itsa-core-plugin' ),
			// translators: This is a message shown when no matching Quote posts are found in Trash.
			'not_found_in_trash' => __( 'No Quotes Found in Trash', 'itsa-core-plugin' ),
			// translators: This is the name of the menu item for Quote posts.
			'menu_name'          => __( 'Quotes', 'itsa-core-plugin' ),
		);

		$args = array(
			// translators: This is the label for Quote posts.
			'label'               => __( 'Quotes', 'itsa-core-plugin' ),
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
			'menu_position'       => 8,
			'menu_icon'           => 'dashicons-format-quote',
			'supports'            => array( 'editor' ),
			'template'            => array(
				array( 'core/quote' ),
			),
			'template_lock'       => 'all',
		);

		register_post_type( 'quote', $args );
	}

	/**
	* Set title
	*
	* @since 0.1.0
	**/
	public function set_title( $data, $postarr ) {
		if ( 'quote' === $data['post_type'] ) {
			$title = $this->get_citation( $data['post_content'] );

			if ( empty( $title ) ) {
				$title = 'Quote ' . $postarr['ID'];
			}

			$data['post_title'] = sanitize_text_field( $title );
		}

		return $data;
	}

	/**
	* Get citation
	*
	* @since 0.1.0
	**/
	public function get_citation( $content ) {
		$matches = array();
		$regex   = '#<cite>(.*?)</cite>#';
		preg_match_all( $regex, $content, $matches );
		if ( ! empty( $matches ) && ! empty( $matches[0] ) && ! empty( $matches[0][0] ) ) {
			return wp_strip_all_tags( $matches[0][0] );
		}
	}
}
