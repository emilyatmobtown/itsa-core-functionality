<?php

namespace ITSACoreFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class SocialIconWidget extends \WP_Widget {

	/**
	 * Constructs widget
	 *
	 * @since  0.1.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => __( 'SocialIconWidget', 'itsa-core-plugin' ),
			'description' => __( 'Displays social icons', 'itsa-core-plugin' ),
		);

		parent::__construct(
			__( 'SocialIconWidget', 'itsa-core-plugin' ),
			__( 'ITSA Social Icons', 'itsa-core-plugin' ),
			$widget_ops
		);
	}

	/**
	 * Creates widget form
	 *
	 * @since 0.1.0
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'itsa-core-plugin' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	* Updates widget instance
	*
	* @param array $new_instance
	* @param array $old_instance
	* @return array
	* @since 0.1.0
	**/
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	/**
	* Updates widget instance
	*
	* @param array $new_instance
	* @param array $old_instance
	* @return array
	* @since 0.1.0
	**/
	public function widget( $args, $instance ) {
		echo $args['before_widget'];  //phpcs:ignore
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; //phpcs:ignore
		}

		$tw_handle = get_theme_mod( 'twitter_handle' );
		$tw_url    = ( ! empty( $tw_handle ) ) ? 'https://twitter.com/' . $tw_handle : '';
		$ig_handle = get_theme_mod( 'instagram_handle' );
		$ig_url    = ( ! empty( $ig_handle ) ) ? 'https://instagram.com/' . $ig_handle : '';
		$fb_url    = get_theme_mod( 'facebook_url' );

		if ( ! empty( $tw_url ) || ! empty( $ig_url ) || ! empty( $fb_url ) ) {
			?>
			<span class="itsa-social-icons display-block">
				<?php if ( ! empty( $tw_url ) ) { ?>
					<a
					class="icon twitter-icon"
					href="<?php echo esc_url( $tw_url ); ?>"
					aria-label="<?php esc_html_e( 'Check out ITSA on Twitter', 'itsa-core-plugin' ); ?>"
					target="_blank"></a>
				<?php } ?>
				<?php if ( ! empty( $fb_url ) ) { ?>
					<a
					class="icon facebook-icon"
					href="<?php echo esc_url( $fb_url ); ?>"
					aria-label="<?php esc_html_e( 'Check out ITSA on Facebook', 'itsa-core-plugin' ); ?>"
					target="_blank"></a>
				<?php } ?>
				<?php if ( ! empty( $ig_url ) ) { ?>
					<a
					class="icon instagram-icon"
					href="<?php echo esc_url( $ig_url ); ?>"
					aria-label="<?php esc_html_e( 'Check out ITSA on Instagram', 'itsa-core-plugin' ); ?>"
					target="_blank"></a>
				<?php } ?>
			</span>
			<?php

		}

		echo $args['after_widget']; //phpcs:ignore
	}
}

new SocialIconWidget();
