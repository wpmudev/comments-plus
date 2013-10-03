<?php
/*
Plugin Name: Troubleshooter
Description: Activate this add-on to troubleshoot possible configuration issues.
Plugin URI: http://premium.wpmudev.org/project/comments-plus
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdcp_Debugger {

	private $_data;

	private function __construct () {
		$this->_data = new Wdcp_Options;
	}

	public static function serve () {
		$me = new Wdcp_Debugger;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('wdcp-options-plugins_options', array($this, 'register_settings'));

		add_action('wp_ajax_wdcp_dbg_theme_compat', array($this, 'json_theme_compat_test'));
		add_action('wp_ajax_wdcp_dbg_twitter_timestamp', array($this, 'json_twitter_timestamp_test'));
		add_action('wp_ajax_wdcp_dbg_google_mod_security', array($this, 'json_google_response_test'));

		add_action('init', array($this, 'dispatch_theme_compat_debug_testing'));
	}

	function register_settings () {
		add_settings_section('wdcp_dbg_debugger', __('Troubleshooter', 'wdcp'), create_function('', ''), 'wdcp_options');
		add_settings_field('wdcp_dbg_general', __('General', 'wdcp'), array($this, 'create_general_box'), 'wdcp_options', 'wdcp_dbg_debugger');
		add_settings_field('wdcp_dbg_theme', __('Theme', 'wdcp'), array($this, 'create_theme_box'), 'wdcp_options', 'wdcp_dbg_debugger');
		add_settings_field('wdcp_dbg_google', __('Google', 'wdcp'), array($this, 'create_google_box'), 'wdcp_options', 'wdcp_dbg_debugger');
		add_settings_field('wdcp_dbg_twitter', __('Twitter', 'wdcp'), array($this, 'create_twitter_box'), 'wdcp_options', 'wdcp_dbg_debugger');
	}

	function create_general_box () {
		$all_good = true;
		$has_curl = function_exists('curl_init');
		if (!$has_curl) {
			echo '<div class="error below-h2"><p>' . __('The required cURL extension seems to be missing.', 'wdcp') . '</p></div>';
			$all_good = false;
		}
		$model = new Wdcp_Model;
		if (!class_exists('TwitterOAuth')) {
			echo '<div class="error below-h2"><p>' . __('There seems to be problems with the required TwitterOAuth class.', 'wdcp') . '</p></div>';
			$all_good = false;
		}
		if (!class_exists('Facebook')) {
			echo '<div class="error below-h2"><p>' . __('There seems to be problems with the required Facebook class.', 'wdcp') . '</p></div>';
			$all_good = false;
		}
		if (!class_exists('LightOpenID')) {
			echo '<div class="error below-h2"><p>' . __('There seems to be problems with the required LightOpenID class.', 'wdcp') . '</p></div>';
			$all_good = false;
		}
		if (!class_exists('OAuthRequest') || !method_exists('OAuthRequest', 'generate_raw_timestamp')) {
			echo '<div class="error below-h2"><p>' . __('There seems to be problems with the required OAuthRequest class.', 'wdcp') . '</p></div>';
			$all_good = false;	
		}
		if ($all_good) {
			echo '<p>' . __('Basic prerequisites seem to be in order.', 'wdcp') . '</p>';
		}
	}

	function create_theme_box () {
		echo '<a href="#theme-request" id="wdcp-dbg-theme-request">' . __('Check theme compatibility', 'wdcp') . '</a>';
		echo '<div id="wdcp-dbg-theme-status"></div>';
		echo <<<EoGoogleJs
<script>
(function ($) {

function status (html) {
	$("#wdcp-dbg-theme-status").html(html);
}

function loading () {
	status("Please, wait");
}

function done (response) {
	var msg = '';	
	if (response && response.msg) {
		msg = response.msg;
	}
	status(msg);
}

$(function () {
	$("#wdcp-dbg-theme-request").on("click", function () {
		loading();
		$.post(ajaxurl, {
			"action": "wdcp_dbg_theme_compat"
		}, done, 'json');
		return false;
	});
});
})(jQuery);
</script>
EoGoogleJs;
	}

	function create_google_box () {
		echo '<a href="#google-request" id="wdcp-dbg-google-request">' . __('Check response handling prerequisites', 'wdcp') . '</a>';
		echo '<div id="wdcp-dbg-google-status"></div>';
		echo <<<EoGoogleJs
<script>
(function ($) {

function status (html) {
	$("#wdcp-dbg-google-status").html(html);
}

function loading () {
	status("Please, wait");
}

function done (response) {
	var msg = '';	
	if (response && response.msg) {
		msg = response.msg;
	}
	status(msg);
}

$(function () {
	$("#wdcp-dbg-google-request").on("click", function () {
		loading();
		$.post(ajaxurl, {
			"action": "wdcp_dbg_google_mod_security"
		}, done, 'json');
		return false;
	});
});
})(jQuery);
</script>
EoGoogleJs;
	}

	function create_twitter_box () {
		echo '<a href="#twitter-request" id="wdcp-dbg-twitter-request">' . __('Check timestamps', 'wdcp') . '</a>';
		echo '<div id="wdcp-dbg-twitter-status"></div>';
		echo <<<EoTwitterJs
<script>
(function ($) {

function status (html) {
	$("#wdcp-dbg-twitter-status").html(html);
}

function loading () {
	status("Please, wait");
}

function done (response) {
	var msg = '';	
	if (response && response.msg) {
		msg = response.msg;
	}
	status(msg);
}

$(function () {
	$("#wdcp-dbg-twitter-request").on("click", function () {
		loading();
		$.post(ajaxurl, {
			"action": "wdcp_dbg_twitter_timestamp"
		}, done, 'json');
		return false;
	});
});
})(jQuery);
</script>
EoTwitterJs;
	}

	function dispatch_theme_compat_debug_testing () {
		if (empty($_GET['wdcp_debugger'])) return false;

		$start_hook = $this->_data->get_option('begin_injection_hook');
		$end_hook = $this->_data->get_option('finish_injection_hook');
		$begin_injection_hook = $start_hook ? $start_hook : 'comment_form_before';
		$finish_injection_hook = $end_hook ? $end_hook : 'comment_form_after';

		$footer_cback = create_function('$init', 'return $init . "<!--wdcp_debugger_footer-->";');
		$shook_cback = create_function('', 'echo "<!--wdcp_debugger_opening_hook-->";');
		$ehook_cback = create_function('', 'echo "<!--wdcp_debugger_closing_hook-->";');

		add_filter('wdcp-service_initialization-facebook', $footer_cback, 99);
		add_filter($begin_injection_hook, $shook_cback, 99);
		add_filter($finish_injection_hook, $ehook_cback, 99);
	}

	function json_theme_compat_test () {
		$components = array(
		// Status
			'fetching' => __("Fetching the post page: %s", 'wdcp'),
			'parsing' => __("Parsing the post page: %s", 'wdcp'),
			'head' => __("Checking header prerequisites: %s", 'wdcp'),
			'foot' => __("Checking footer prerequisites: %s", 'wdcp'),
			'facebook' => __("Checking Facebook initialization: %s", 'wdcp'),
			'form' => __("Checking form overall prerequisites: %s", 'wdcp'),
			'open_hook' => __("Checking form opening prerequisites: %s", 'wdcp'),
			'close_hook' => __("Checking form closing prerequisites: %s", 'wdcp'),
		// Advices
			'foot_advice' => __("Consider setting the <code>WDCP_FOOTER_DEPENDENCIES_HOOK</code> define to something your theme supports.", 'wdcp'),
			'facebook_advice' => __("Consider activating the &quot;Alternative Facebook Initialization&quot; add-on.", 'wdcp'),
			'hooks_advice' => __("Consider activating the &quot;Custom Comments Template&quot; add-on, <b>or</b> changing injection hooks to something your theme supports.", 'wdcp'),
		);
		$ok = '<b style="color:green">' . __("OK", 'wdcp') . '</b>';
		$fail = '<b style="color:red">' . __("failure", 'wdcp') . '</b>';
		$response = array();
		$advices = array();

		$posts = get_posts(array(
			'orderby' => 'comment_count',
			'posts_per_page' => 1,
		));
		$post = !empty($posts[0]) ? $posts[0] : false;
		if (empty($post)) {
			die(json_encode(array(
				'status' => 1,
				'msg' => __("Sorry, we couldn't find a post to test.", 'wdcp'),
			)));
		}

		$url = add_query_arg(array('wdcp_debugger' => '1'), get_permalink($post->ID));
		$request = wp_remote_get($url, array('sslverify' => false));
		if (200 == wp_remote_retrieve_response_code($request)) {
			$response[] = sprintf($components['fetching'], $ok);
		} else {
			$response[] = sprintf($components['fetching'], $fail);
		}

		$body = wp_remote_retrieve_body($request);
		if (empty($body)) {
			$response[] = sprintf($components['parsing'], $fail);
		} else {
			$response[] = sprintf($components['parsing'], $ok);
		}
		
		if (preg_match('/_wdcp_ajax_url/', $body)) {
			$response[] = sprintf($components['head'], $ok);
		} else {
			$response[] = sprintf($components['head'], $fail);
		}

		if (preg_match('/wdcp_debugger_footer/', $body)) {
			$response[] = sprintf($components['foot'], $ok);
		} else {
			$response[] = sprintf($components['foot'], $fail);
			$advices['foot_advice'] = $components['foot_advice'];
		}

		if (preg_match('/' . preg_quote(WDCP_APP_ID, '/') . '/', $body)) {
			$response[] = sprintf($components['facebook'], $ok);
		} else {
			$response[] = sprintf($components['facebook'], $fail);
			if (!class_exists('Wdcp_Afi_PublicPages')) $advices['facebook_advice'] = $components['facebook_advice'];
		}

		if (preg_match('/all-comment-providers/', $body)) {
			$response[] = sprintf($components['form'], $ok);
		} else {
			$response[] = sprintf($components['form'], $fail);
			if (!class_exists('Wdcp_Cct_Admin_Pages')) $advices['hooks_advice'] = $components['hooks_advice'];
		}
		if (preg_match('/wdcp_debugger_opening_hook/', $body)) {
			$response[] = sprintf($components['open_hook'], $ok);
		} else {
			$response[] = sprintf($components['open_hook'], $fail);
			if (!class_exists('Wdcp_Cct_Admin_Pages')) $advices['hooks_advice'] = $components['hooks_advice'];
		}
		if (preg_match('/wdcp_debugger_closing_hook/', $body)) {
			$response[] = sprintf($components['close_hook'], $ok);
		} else {
			$response[] = sprintf($components['close_hook'], $fail);
			if (!class_exists('Wdcp_Cct_Admin_Pages')) $advices['hooks_advice'] = $components['hooks_advice'];
		}

		if (empty($advices)) $advices[] = __("All seems to be okay.", 'wdcp');

		die(json_encode(array(
			'status' => 0,
			'msg' => '' .
				'<ul><li>' . join('</li><li>', $response) . '</li></ul>' .
				'<div style="font-style:italic"><p>' . join('<br />', $advices) . '</p></div>' .
			'',
		)));
	}

	function json_twitter_timestamp_test () {
		$test_time = OAuthRequest::generate_raw_timestamp();
		$test_url = "https://api.twitter.com/1/help/test.json";
		
		$request = wp_remote_get($test_url, array('sslverify' => false));
		$headers = wp_remote_retrieve_headers($request);
		if (!empty($headers['date'])) {
			$twitter_time = strtotime($headers['date']);
			$delta = $twitter_time - $test_time;
			if (abs($delta) > WDCP_TIMESTAMP_DELTA_THRESHOLD) {
				update_site_option('wdcp_twitter_timestamp_delta_fix', $delta);
				die(json_encode(array(
					'status' => 0,
					'msg' => sprintf(__('There seems to be some differences in Twitter and your notion of time, by %d sec. This should now be fixed.', 'wdcp'), $delta),
				)));
			} else {
				update_site_option('wdcp_twitter_timestamp_delta_fix', 0);
				die(json_encode(array(
					'status' => 1,
					'msg' => __('Timestamp settings seem to be within acceptable limits', 'wdcp'),
				)));
			}
		}

		die(json_encode(array(
			'status' => 1,
			'msg' => __('Could not determine Twitter time, assuming everything is OK.', 'wdcp'),
		)));
	}

	function json_google_response_test () {
		$test_url = add_query_arg('test', 'http://www.google.com', home_url());
		$request = wp_remote_get($test_url, array('sslverify' => false));
		if (200 != wp_remote_retrieve_response_code($request)) {
			die(json_encode(array(
				'status' => 0,
				'msg' => __('URLs in query strings seem not to be allowed, which will cause issues with Google OpenID authentication.', 'wdcp'),
			)));
		} else {
			die(json_encode(array(
				'status' => 1,
				'msg' => __('Response handling seems to be under control', 'wdcp'),
			)));
		}
	}
}
Wdcp_Debugger::serve();