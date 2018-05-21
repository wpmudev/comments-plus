<?php
/**
 * Handles GDPR transition stuff.
 *
 * This includes policy copy suggestion, data erxport and data erase.
 *
 * @package wdcp
 */

/**
 * Privacy handler class
 */
class Wdcp_Gdpr {

	private function __construct() {}

	public static function serve() {
		$me = new self;
		$me->_add_hooks();
	}

	private function _add_hooks() {
		add_action( 'admin_init', array( $this, 'add_privacy_policy' ) );

		add_filter(
			'wp_privacy_personal_data_exporters',
			array( $this, 'register_data_exporter' )
		);
		add_filter(
			'wp_privacy_personal_data_erasers',
			array( $this, 'register_data_eraser' ),
			5 // Register *early*, before comments erasure kills the email.
		);
	}

	/**
	 * Augments exporters with plugins data exporter callback
	 *
	 * @param array $exporters Exporters this far.
	 *
	 * @return array
	 */
	public function register_data_exporter( $exporters ) {
		$exporters['wdcp'] = array(
			'exporter_friendly_name' => __( 'Comments Plus metadata', 'wdcp' ),
			'callback' => array( $this, 'export_user_metadata' ),
		);
		return $exporters;
	}

	/**
	 * Augments erasers with plugins data eraser callback
	 *
	 * @param array $erasers Exporters this far.
	 *
	 * @return array
	 */
	public function register_data_eraser( $erasers ) {
		$erasers['wdcp'] = array(
			'eraser_friendly_name' => __( 'Comments Plus metadata', 'wdcp' ),
			'callback' => array( $this, 'erase_user_metadata' ),
		);
		return $erasers;
	}

	/**
	 * Exports plugins metadata
	 *
	 * @param string $email User email.
	 * @param int    $page Data page.
	 *
	 * @return array
	 */
	public function export_user_metadata( $email, $page = 1 ) {
		$result	= array(
			'data' => array(),
			'done' => true,
		);
		$comment_ids = $this->get_comments_list( $email );
		if ( empty( $comment_ids ) ) {
			return $result;
		}

		$label = __( 'Comments Plus metadata', 'wcp' );
		$exports = array();
		foreach ( $comment_ids as $cid ) {
			$raw = get_comment_meta( $cid, 'wdcp_comment', true );
			if ( empty( $raw ) ) {
				continue;
			}

			$data = array();
			foreach ( $raw as $key => $value ) {
				if ( preg_match( '/author_id/', $key ) ) {
					$key = __( 'Author ID', 'wdcp' );
				} elseif ( preg_match( '/avatar/', $key ) ) {
					$key = __( 'Avatar', 'wcp' );
				}
				$data[] = array(
					'name' => $key,
					'value' => $value,
				);
			}

			$exports[] = array(
				'group_id' => 'comments-wdcp_meta',
				'group_label' => $label,
				'item_id' => "comments-comment-wdcp_meta-{$cid}",
				'data' => $data,
			);
		}
		$result['data'] = $exports;

		return $result;
	}

	/**
	 * Erases plugins metadata
	 *
	 * @param string $email User email.
	 * @param int    $page Data page.
	 *
	 * @return array
	 */
	public function erase_user_metadata( $email, $page = 1 ) {
		$result = array(
			'items_removed' => 0,
			'items_retained' => false,
			'messages' => array(),
			'done' => true
		);
		$comment_ids = $this->get_comments_list($email);
		if (empty($comment_ids)) {
			return $result;
		}

		foreach ($comment_ids as $cid) {
			error_log("Deleting comment meta for {$cid}");
			$result['items_removed'] = true;
			delete_comment_meta($cid, 'wdcp_comment');
			error_log("Meta: " . wp_json_encode(get_comment_meta($cid, 'wdcp_comment', true)));
		}

		return $result;
	}

	/**
	 * Affected comments getter method
	 *
	 * @param string $email User email to check.
	 *
	 * @return array List of comment IDs
	 */
	public function get_comments_list( $email ) {
		$args = array(
			'fields' => 'ids',
			'meta_query' => array(
				'key' => 'wdcp_comment',
				'compare' => 'EXISTS',
			),
		);
		$byid = $byemail = array();

		$user = get_user_by( 'email', $email );
		if ( ! empty( $user->ID ) ) {
			$byid = get_comments(
				wp_parse_args( "user_id={$user->ID}", $args )
			);
		}

		$byemail = get_comments(
			wp_parse_args( "author_email={$email}", $args )
		);

		return array_merge( $byid, $byemail );
	}

	/**
	 * Hooks into privacy policy content, if possible
	 */
	public function add_privacy_policy() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return false;
		}
		wp_add_privacy_policy_content(
			__( 'Comments Plus', 'wdcp' ),
			$this->get_policy_content()
		);
	}

	/**
	 * Gets policy content as string
	 *
	 * @return string Policy content HTML
	 */
	public function get_policy_content() {
		return '' .
			'<h3>' . __( 'Third parties', 'wdcp' ) . '</h3>' .
			'<p>' . __( 'This site might be using third parties to assert your identity (with your explicit content) before leaving a comment. These services include Facebook, Google and Twitter.', 'wdcp' ) . '</p>' .
			'<h3>' . __( 'Additional data', 'wdcp' ) . '</h3>' .
			'<p>' . __( 'Your comments on this site will be augmented with additional data coming from the selected identity provider. This data includes your name, username, email, avatar and profile URL. This data can be exported and removed.', 'wdcp' ) . '</p>' .
			'<h3>' . __( 'Cookies', 'wdcp' ) . '</h3>' .
			'<p>' . __( 'In addition to standard WordPress comments cookies, this site might be setting an additional cookie to remember your preferred commenting identity provider. This cookie will last for one year.', 'wdcp' ) . '</p>' .
		'';
	}
}
