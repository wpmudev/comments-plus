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
			'<h3>' . __('Third parties', 'wdcp') . '</h3>' .
			'<p>' . __('This site might be using third parties to assert your identity (with your explicit content) before leaving a comment. These services include Facebook, Google and Twitter.', 'wdcp') . '</p>' .
			'<h3>' . __('Additional data', 'wdcp') . '</h3>' .
			'<p>' . __('Your comments on this site will be augmented with additional data coming from the selected identity provider. This data includes your name, username, email, avatar and profile URL. This data can be exported and removed.', 'wdcp') . '</p>' .
			'<h3>' . __('Cookies', 'wdcp') . '</h3>' .
			'<p>' . __('In addition to standard WordPress comments cookies, this site might be setting an additional cookie to remember your preferred commenting identity provider. This cookie will last for one year.', 'wdcp') . '</p>' .
		'';
	}
}
