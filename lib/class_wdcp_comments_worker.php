<?php
class Wdcp_CommentsWorker {

	var $model;
	var $data;

	function Wdcp_CommentsWorker () { $this->__construct(); }

	function __construct () {
		$this->model = new Wdcp_Model;
		$this->data = new Wdcp_Options;
	}

	public static function get_services_list () {
		return apply_filters('wdcp-services-list', array(
			'wordpress',
			'twitter',
			'facebook',
			'google',
		));
	}

	function js_load_scripts () {
		wp_enqueue_script('jquery');
		if (!apply_filters('wdcp-script_inclusion-facebook', WDCP_SKIP_FACEBOOK)) {
			$locale = defined('WDCP_FACEBOOK_LOCALE') && WDCP_FACEBOOK_LOCALE
				? WDCP_FACEBOOK_LOCALE : preg_replace('/-/', '_', get_locale())
			;
			$locale = apply_filters('wdcp-locale-facebook_locale', $locale);
			wp_enqueue_script('facebook-all', WDCP_PROTOCOL . 'connect.facebook.net/' . $locale . '/all.js');
		}
		if (!apply_filters('wdcp-script_inclusion-twitter', WDCP_SKIP_TWITTER)) {
			wp_enqueue_script('twitter-anywhere', WDCP_PROTOCOL . 'platform.twitter.com/anywhere.js?id=' . WDCP_TW_API_KEY . '&v=1');
		}
		wp_enqueue_script('wdcp_comments', WDCP_PLUGIN_URL . '/js/comments.js', array('jquery'));
		wp_enqueue_script('wdcp_twitter', WDCP_PLUGIN_URL . '/js/twitter.js', array('jquery', 'wdcp_comments'));
		wp_enqueue_script('wdcp_facebook', WDCP_PLUGIN_URL . '/js/facebook.js', array('jquery', 'wdcp_comments'));
		if ($this->data->get_option('gg_client_id')) {
			wp_enqueue_script('wdcp_google_plus', WDCP_PLUGIN_URL . '/js/google_plus.js', array('jquery', 'wdcp_comments'));
		} else {
			wp_enqueue_script('wdcp_google', WDCP_PLUGIN_URL . '/js/google.js', array('jquery', 'wdcp_comments'));
		}

		$preferred_provider = $this->data->get_option('preferred_provider');
		$preferred_provider = $preferred_provider ? $preferred_provider : 'facebook';

		$js_data = apply_filters('wdcp-javascript-data', array(
			"post_id" => get_the_ID(),
			"fit_tabs" => (int)$this->data->get_option('stretch_tabs'),
			"text" => array(
				"reply" => esc_js(__('Reply', 'wdcp')),
				"cancel_reply" => esc_js(__('Cancel reply', 'wdcp')),
				"please_wait" => esc_js(__('Please, wait...', 'wdcp')),
			),
			'preferred_provider' => $preferred_provider,
		));
		echo '<script type="text/javascript">var _wdcp_data=' . json_encode($js_data) . ';</script>';
	}

	function css_load_styles () {
		$skip = $this->data->get_option('skip_color_css');
		wp_enqueue_style('wdcp_comments', WDCP_PLUGIN_URL . '/css/comments.css');
		if (!current_theme_supports('wdcp_comments-specific') && !$skip) {
			wp_enqueue_style('wdcp_comments-specific', WDCP_PLUGIN_URL . '/css/comments-specific.css');
		}

		$icon = $this->data->get_option('wp_icon');
		if ($icon) {
			$selector = apply_filters('wdcp-wordpress_custom_icon_selector', 'ul#all-comment-providers li a#comment-provider-wordpress-link');
			printf(
				'<style type="text/css">
					%s {
						background-image: url(%s) !important;
					}
				</style>', $selector, $icon);
		}
	}

	function header_dependencies () {
		echo $this->_prepare_header_dependencies();
	}

	function begin_injection () {
		$skips = (array)$this->data->get_option('skip_services');
		$instructions = $this->data->get_option('show_instructions') ? '' : 'no-instructions';

		$services = self::get_services_list();

		if (!in_array('facebook', $skips)) $fb_html = $this->_prepare_facebook_comments();
		if (!in_array('twitter', $skips)) $tw_html = $this->_prepare_twitter_comments();
		if (!in_array('google', $skips)) $gg_html = $this->_prepare_google_comments();

		$names = array();
		if (!in_array('wordpress', $skips)) {
			$default_name = defined('WDCP_DEFAULT_WP_PROVIDER_NAME') && WDCP_DEFAULT_WP_PROVIDER_NAME ? WDCP_DEFAULT_WP_PROVIDER_NAME : get_bloginfo('name');
			$default_name = $default_name ? $default_name : 'WordPress';
			$names['wordpress'] = $this->model->current_user_logged_in('wordpress')
				? $this->model->current_user_name('wordpress')
				: apply_filters('wdcp-providers-wordpress-name', $default_name)
			;
			$names['wordpress'] = apply_filters( 'wdcp_wp_comment_name', $names['wordpress'] );
		}
		if (!in_array('twitter', $skips)) $names['twitter'] = $this->model->current_user_logged_in('twitter') ? $this->model->current_user_name('twitter') : apply_filters('wdcp-providers-twitter-name', 'Twitter');
		if (!in_array('facebook', $skips)) $names['facebook'] = $this->model->current_user_logged_in('facebook') ? $this->model->current_user_name('facebook') : apply_filters('wdcp-providers-facebook-name', 'Facebook');
		if (!in_array('google', $skips)) $names['google'] = $this->model->current_user_logged_in('google') ? $this->model->current_user_name('google') : apply_filters('wdcp-providers-google-name', 'Google');
		echo "
		<div id='comment-providers-select-message'>" . __("Click on a tab to select how you'd like to leave your comment", 'wdcp') . "</div>
		<div id='comment-providers'><a name='comments-plus-form'></a>
			<ul id='all-comment-providers'>";

		foreach ($services as $service) {
			if (in_array($service, $skips)) continue;
			echo '<li>' .
				"<a id='comment-provider-{$service}-link' href='#comment-provider-{$service}'><span>" .
					$names[$service] .
				'</span></a>' .
			'</li>';
		}
		echo "</ul>";
		if (!in_array('facebook', $skips)) echo "<div class='comment-provider' id='comment-provider-facebook'>$fb_html</div>";
		if (!in_array('twitter', $skips)) echo "<div class='comment-provider' id='comment-provider-twitter'>$tw_html</div>";
		if (!in_array('google', $skips)) echo "<div class='comment-provider' id='comment-provider-google'>$gg_html</div>";
		echo "<div class='comment-provider {$instructions}' id='comment-provider-wordpress'>";
	}

	function finish_injection () {
		echo "</div> <!-- Wordpress provider -->";
		echo "</div> <!-- #comment-providers -->";
	}

	function footer_dependencies () {
		echo $this->_prepare_footer_dependencies();
	}

	function replace_avatars ($avatar, $comment) {
		if (!is_object($comment) || !isset($comment->comment_ID)) return $avatar;
		$fb_uid = false;

		$meta = get_comment_meta($comment->comment_ID, 'wdcp_comment', true);
		if (!$meta) return $avatar;

		$fb_uid = !empty($meta['wdcp_fb_author_id']) ? $meta['wdcp_fb_author_id'] : false;
		$tw_avatar = !empty($meta['wdcp_tw_avatar']) ? $meta['wdcp_tw_avatar'] : false;
		$gg_avatar = !empty($meta['wdcp_gg_avatar']) ? $meta['wdcp_gg_avatar'] : false;

		if (!empty($fb_uid)) return "<img class='avatar avatar-40 photo' width='40' height='40' src='". WDCP_PROTOCOL . "graph.facebook.com/{$fb_uid}/picture' />";
		if (!empty($tw_avatar)) return "<img class='avatar avatar-40 photo' width='40' height='40' src='{$tw_avatar}' />";
		if (!empty($gg_avatar)) return "<img class='avatar avatar-40 photo' width='40' height='40' src='{$gg_avatar}' />";

		return $avatar;
	}

/*** Privates ***/

	function _prepare_header_dependencies () {
	}

	function _prepare_facebook_comments () {
		if (!$this->model->current_user_logged_in('facebook')) return $this->_prepare_facebook_login();
		$preselect = $this->data->get_option('dont_select_social_sharing') ? '' : 'checked="checked"';
		$disconnect = __('Disconnect', 'wdcp');
		$posting_box = $this->data->get_option('fb_dont_post_on_facebook')
			? ''
			: "<p><label for='post-on-facebook'><input type='checkbox' id='post-on-facebook' value='1' {$preselect} /> " . __("Post my comment on my wall", "wdcp"). "</label></p>"
		;
		return "
			<p>" . __('Connected as', 'wdcp') . " <b class='connected-as'>" . $this->model->current_user_name('facebook') . "</b>. <a class='comment-provider-logout' href='#'>{$disconnect}</a></p>
			<textarea id='facebook-comment' rows='8' cols='45' rows='6'></textarea>
			{$posting_box}	
			<p><a class='button' href='#' id='send-facebook-comment'>" . sprintf(__('Comment via %s', 'wdcp'), 'Facebook') . "</a></p>
		";
	}

	function _prepare_facebook_login () {
		return "<img src='" . WDCP_PLUGIN_URL . "/img/fb-login.png' style='position:absolute;left:-1200000000px;display:none' />" . '<div class="comment-provider-login-button" id="login-with-facebook"><a href="#" title="' . __('Login with Facebook', 'wdcp') . '"><span>Login</span></a></div>';
	}

	function _prepare_google_comments () {
		if (!$this->model->current_user_logged_in('google')) return $this->_prepare_google_login();
		$disconnect = __('Disconnect', 'wdcp');
		return "
			<p>" . __('Connected as', 'wdcp') . " <b class='connected-as'>" . $this->model->current_user_name('google') . "</b>. <a class='comment-provider-logout' href='#'>{$disconnect}</a></p>
			<textarea id='google-comment' rows='8' cols='45' rows='6'></textarea>
			<p><a class='button' href='#' id='send-google-comment'>" . sprintf(__('Comment via %s', 'wdcp'), 'Google') . "</a></p>
		";
	}

	function _prepare_google_login () {
		if ($this->data->get_option('gg_client_id')) return $this->_prepare_google_plus_login();

		$href = WDCP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return "<img src='" . WDCP_PLUGIN_URL . "/img/gg-login.png' style='position:absolute;left:-1200000000px;display:none' />" . '<div class="comment-provider-login-button" id="login-with-google"><a href="' . $href . '" title="' . __('Login with Google', 'wdcp') . '"><span>Login</span></a></div>';
	}

	function _prepare_google_plus_login () {
		return '<div id="login-with-google" class="plus"><span id="signinButton">
  <span
    class="g-signin"
    data-callback="wdcp_google_plus_login_callback"
    data-clientid="' . esc_attr($this->data->get_option('gg_client_id')) . '"
    data-cookiepolicy="single_host_origin"
    data-scope="profile email">
  </span>
</span></div>';
	}

	function _prepare_twitter_comments () {
		if (!$this->model->current_user_logged_in('twitter')) return $this->_prepare_twitter_login();
		$preselect = $this->data->get_option('dont_select_social_sharing') ? '' : 'checked="checked"';
		$disconnect = __('Disconnect', 'wdcp');
		$posting_box = $this->data->get_option('tw_dont_post_on_twitter')
			? ''
			: "<p><label for='post-on-twitter'><input type='checkbox' id='post-on-twitter' value='1' {$preselect} /> " . __("Post my comment on Twitter", "wdcp"). "</label></p>"
		;
		return "
			<p>" . __('Connected as', 'wdcp') . " <b class='connected-as'>" . $this->model->current_user_name('twitter') . "</b>. <a class='comment-provider-logout' href='#'>{$disconnect}</a></p>
			<textarea id='twitter-comment' rows='8' cols='45' rows='6'></textarea>
			{$posting_box}
			<p><a class='button' href='#' id='send-twitter-comment'>" . sprintf(__('Comment via %s', 'wdcp'), 'Twitter') . "</a></p>
		";
	}

	function _prepare_twitter_login () {
		$href = WDCP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return "<img src='" . WDCP_PLUGIN_URL . "/img/tw-login.png' style='position:absolute;left:-1200000000px;display:none' />" . '<div class="comment-provider-login-button" id="login-with-twitter"><a href="' . $href . '" title="' . __('Login with Twitter', 'wdcp') . '"><span>Login</span></a></div>';
	}

	function _prepare_footer_dependencies () {
		if (WDCP_SKIP_FACEBOOK) $fb_part = ''; // Solve possible UFb conflict
		else $fb_part = "<div id='fb-root'></div>" .
			"<script>
			FB.init({
				appId: '" . WDCP_APP_ID . "',
				status: true,
				cookie: true,
				xfbml: true,
				oauth: true
			});
			</script>";

		$tw_part = sprintf(
			'<script type="text/javascript">jQuery(function () { if ("undefined" != typeof twttr && twttr.anywhere && twttr.anywhere.config) twttr.anywhere.config({ callbackURL: "%s" }); });</script>',
			get_permalink()
		);
		$fb_part = apply_filters('wdcp-service_initialization-facebook', $fb_part);
		$tw_part = apply_filters('wdcp-service_initialization-twitter', $tw_part);
		return "{$fb_part}{$tw_part}";
	}

}
