<?php

namespace ITSACoreFunctionality;

use Abraham\TwitterOAuth\TwitterOAuth;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Nope!' );
}

class DisplayTweets {

	/**
	 * How often the tweets are refreshed (in seconds).
	 *
	 * @since 0.1.0
	 */
	public static $refresh = 300;

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
		add_shortcode( 'display_tweets', array( $this, 'do_shortcode' ) );
	}

	/**
	 * Executes a shortcode handler
	 *
	 * @since 0.1.0
	 */
	public function do_shortcode( $atts ) {

		/** Return the tweets to be printed by the shortcode */
		ob_start();
		$this->show();
		return ob_get_clean();

	}

	/**
	 * Formats tweet text to add URLs and hashtags
	 *
	 * @since 0.1.0
	 */
	public function format_tweet( $text ) {
		$text = preg_replace( '#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#', '\\1<a href=\"\\2\" target=\"_blank\">\\2</a>', $text );
		$text = preg_replace( '#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#', '\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>', $text );
		$text = preg_replace( '/@(\w+)/', '<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>', $text );
		$text = preg_replace( '/#(\w+)/', '<a href=\"http://twitter.com/search?q=%23\\1&src=hash\" target=\"_blank\">#\\1</a>', $text );
		return $text;
	}

	/**
	 * Gets the tweets
	 *
	 * @param string $handle
	 * @since 0.1.0
	 */
	public function get( $handle ) {

		$args = array(
			'screen_name'     => $handle,
			'count'           => 3,
			'include_rts'     => true,
			'exclude_replies' => false,
		);

		$tweets = get_transient( 'displaytweets_tweets' );
		if ( false === $tweets ) {

			require_once ICF_INC . '../vendor/autoload.php';

			$twitter_connection = new TwitterOAuth(
				'YQBW4TKhaojsw2s4SHKs2dsY1', //consumer_key
				'yeiyhs3RBctTEm8EqYawn0n356OSB57jz2xQZBMDu9dj7lC6jh', //consumer_secret
				'138073426-LRaha4iHtBGclcTN7VpJroZ96DElVWzu9GhIqtA6', //access_token
				'YtFajoirXSe3YaBSMnbretrcbexz2fORFko7S1FxNRhd4' //access_token_secret
			);

			$tweets = $twitter_connection->get(
				'statuses/user_timeline',
				$args
			);

			if ( ! $tweets || isset( $tweets->errors ) ) {
				return false;
			}

			set_transient( 'displaytweets_tweets', $tweets, self::$refresh );
		}

		return $tweets;
	}

	/**
	 * Prints the tweets
	 *
	 * @param string $handle
	 * @param array $tweet_classes
	 * @since 0.1.0
	 */
	public function show( $handle, $tweet_classes = [] ) {

		/** Get the tweets */
		$tweets = $this->get( $handle );

		/** Bail if there are no tweets */
		if ( ! $tweets ) {
			if ( current_user_can( 'manage_options' ) ) {
				esc_html_e( 'No tweets found. Please make sure your settings are correct.', 'itsa-core-plugin' );
			}

			return;
		}

		/** Print the tweets */
		foreach ( $tweets as $tweet ) {

			$tweet_date = date_i18n( 'F j, Y', strtotime( $tweet->created_at ) );
			$tweet_link = 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id_str;
			$tweet_text = $tweet->text;

			?>
			<article class="<?php echo esc_html( implode( ' ', $tweet_classes ) ); ?>">
				<p class="tweet-text">
					<a class="tweet-link" target="_blank" href="<?php echo esc_url( $tweet_link ); ?>"><?php echo esc_html( $tweet_text ); ?></a>
				</p>
				<a class="tweet-link tweet-date" target="_blank" href="<?php echo esc_url( $tweet_link ); ?>"><?php echo esc_attr( $tweet_date ); ?></a>
			</article>
			<?php

		}

	}
}

/**
 * Helper function for displaying tweets
 *
 * @param string $handle
 * @param array $tweet_classes
 * @since 0.1.0
 */
function display_tweets( $handle, $tweet_classes ) {
	DisplayTweets::factory()->show( $handle, $tweet_classes );
}
