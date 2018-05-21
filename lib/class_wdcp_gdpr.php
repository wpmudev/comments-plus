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
		add_action('admin_init', array($this, 'add_privacy_policy'));
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
			'<p>' . __('', 'wdcp') . '</p>' .
		'';
	}
}
