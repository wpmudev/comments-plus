<?php
/*
Plugin Name: Services order
Description: Allows you to re-order the services tabs.
Plugin URI: http://premium.wpmudev.org/project/comments-plus
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdcp_Slo_AdminPages {

	private $_data;

	private function __construct () {
		$this->_data = new Wdcp_Options;
	}

	public static function serve () {
		$me = new Wdcp_Slo_AdminPages;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('wdcp-options-plugins_options', array($this, 'register_settings'));
		add_action('wdcp-settings-page', array($this, 'include_scripts'));
	}

	public function include_scripts ($page) {
		add_action('admin_enqueue_scripts', create_function('$hook', "if (\$hook == '{$page}') wp_enqueue_script('jquery-ui-sortable');"));
	}

	function register_settings () {
		add_settings_section('wdcp_slo_settings', __('Tab list order', 'wdcp'), array($this, 'create_notice_box'), 'wdcp_options');
		add_settings_field('wdcp_slo_order', __('Order', 'wdcp'), array($this, 'create_services_box'), 'wdcp_options', 'wdcp_slo_settings');
	}

	function create_notice_box () {
		echo '<em>' . __('Drag and drop services into proper order.', 'wdcp') . '</em>';
	}
	
	function create_services_box () {
		$services = $this->_data->get_option('slo_services');
		$services = empty($services) ? Wdcp_CommentsWorker::get_services_list() : $services;

		echo '<div class="wdcp-services-order">';
		foreach ($services as $service) {
			echo '<div class="wdcp-service-sortable">' .
				'<b>' . $service . '</b>' .
				'<input type="hidden" name="wdcp_options[slo_services][]" value="' . esc_attr($service) . '" />' .
			'</div>';
		}
		echo '</div>';
		echo <<<EOJS
<script>
(function ($) {
$(function () {
	$(".wdcp-services-order").sortable();
});
})(jQuery);
</script>
EOJS;
	}
}


class Wdcp_Slo_PublicPages {

	private $_data;

	private function __construct () {
		$this->_data = new Wdcp_Options;
	}

	public static function serve () {
		$me = new Wdcp_Slo_PublicPages;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('wdcp-services-list', array($this, 'apply_order'));
	}

	function apply_order ($services) {
		$overrides = $this->_data->get_option('slo_services');
		return !empty($overrides)
			? $overrides
			: $services
		;
	}
}

if (is_admin()) Wdcp_Slo_AdminPages::serve();
else Wdcp_Slo_PublicPages::serve();