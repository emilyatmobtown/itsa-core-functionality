<?php
/**
 * Duplicates posts and page. Clones appear as drafts. User is redirected to
 * edit screen.
 *
 * @link https://kinsta.com/knowledgebase/duplicate-page-post-wordpress/
 *
 * @package ITSACoreFunctionality
 */

namespace ITSACoreFunctionality\DuplicatePost;

$n = function( $function ) {
	return __NAMESPACE__ . '\\' . $function;
};

add_action( 'admin_action_itsa_duplicate_post', $n( 'duplicate_post' ) );
add_filter( 'post_row_actions', $n( 'duplicate_post_link' ), 10, 2 );
add_filter( 'page_row_actions', $n( 'duplicate_post_link' ), 10, 2 );

/**
 * Duplicate post as draft
 *
 * @since 0.1.0
 */
function duplicate_post() {
	global $wpdb;

	if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'itsa_duplicate_post' === $_REQUEST['action'] ) ) ) {
		wp_die( 'No post to duplicate has been supplied!' );
	}

	// Nonce verification
	if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	// Get original post ID and data
	$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
	$post    = get_post( $post_id );

	// Assign author
	$current_user    = wp_get_current_user();
	$new_post_author = $current_user->ID;

	// If post exists, create new post
	if ( isset( $post ) && null !== $post ) {

		// Assign data
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		);

		$new_post_id = wp_insert_post( $args );

		// Assign terms
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
			wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
		}

		// Assign metadata
		$post_metas = $wpdb->get_results(
			$wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id )
		);
		if ( 0 !== count( $post_metas ) ) {
			foreach ( $post_metas as $post_meta ) {
				if ( '_wp_old_slug' === $post_meta->meta_key ) {
					continue;
				}

				$meta_key        = $post_meta->meta_key;
				$meta_value      = $post_meta->meta_value;
				$sql_query_sel[] = $wpdb->prepare( 'SELECT %d, %s, %s', $new_post_id, $meta_key, $meta_value );
			}

			$sql_query  = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value )";
			$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
			$wpdb->query( $sql_query ); //phpcs:ignore
		}

		// Redirect to the edit post screen for the new draft
		wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . esc_attr( $new_post_id ) ) );
		exit;

	} else {
		wp_die( 'Post creation failed, could not find original post: ' . esc_attr( $post_id ) );
	}
}

/**
 * Add the duplicate link to action list for post_row_actions
 *
 * @param  array   $actions
 * @param  WP_Post $post
 * @return array   $actions
 *
 * @since 0.1.0
 */
function duplicate_post_link( $actions, $post ) {
	if ( current_user_can( 'edit_posts' ) && in_array( $post->post_type, array( 'post', 'page', 'advocacy-material', 'event', 'news', 'block-area', 'priority', 'quote', 'silo' ), true ) ) {
		$actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=itsa_duplicate_post&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
	}
	return $actions;
}
