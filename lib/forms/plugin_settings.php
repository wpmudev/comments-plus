<div class="wrap">
	<h2><?php __('Comments Plus settings', 'wdcp');?></h2>

<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post">
<?php } else { ?>
	<form action="options.php" method="post">
<?php } ?>

	<?php settings_fields('wdcp'); ?>
	<?php do_settings_sections('wdcp_options'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>

</div>

<script type="text/javascript">
(function ($) {
$(function () {

// ----- More help -----
$(document).on('click', ".wdcp-more_help-fb", function () {
	if ($(this).parents(".wdcp-setup-pointer").length) $(this).parents(".wdcp-setup-pointer").remove();
	$("#contextual-help-link").click();
	$("#tab-link-wdcp-fb-setup a").click();
	$(window).scrollTop(0);
	return false;
});

// ----- Subprovider toggling -----
$(document).on("click", ".wdcp-provider-wrapper h4", function () {
	var $me = $(this),
		$selected = $me.closest(".wdcp-provider-wrapper"),
		$root = $me.closest(".wdcp-provider-toggle")
	;
	if (!$root.length) return false;

	$root.find(".wdcp-provider-wrapper").removeClass("selected");
	$selected.addClass("selected");
});

/**
 * Handle tutorial resets.
 */
$(".wdcp-restart_tutorial").click(function () {
	var $me = $(this);
	// Change UI
	$me.after(
		'<img src="<?php echo WDCP_PLUGIN_URL;?>/img/loading.gif" />'
	).remove();
	// Do call
	$.post(ajaxurl, {
		"action": "wdcp_restart_tutorial"
	}, function () {
		window.location.reload();
	});
	return false;
});

<?php do_action('wdcp-plugin_settings-javascript_init'); ?>

});
})(jQuery);
</script>
<style type="text/css">
.wdcp-provider-wrapper {
	border: 1px solid #ccc;
}
.wdcp-provider-wrapper h4 {
	background-color: #343434;
	color: #eee;
	margin: 0;
	padding: 1em 2em;
}
.wdcp-provider-wrapper h4:before {
	content: "[+]";
	color: #eee;
	display: block;
	position: absolute;
	margin-left: -1.5em;
}
.wdcp-provider-wrapper.selected h4:before {
	content: "[-]";
}
.wdcp-provider-wrapper-inside {
	display: none;
}
.wdcp-provider-wrapper.selected .wdcp-provider-wrapper-inside {
	display: block;
	padding: 1em 2em;
}
</style>
