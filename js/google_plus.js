;(function ($) {

function signinCallback(authResult) {
	if (authResult['status']['signed_in']) {
		$.post(_wdcp_ajax_url, {
			"action": "wdcp_google_plus_auth_check",
			"token": authResult['access_token']
		}, function (data) {
			$(document).trigger('wdcp_logged_in', ['google']);
		});
	}
}
window.wdcp_google_plus_login_callback = signinCallback;

//Handle logout requests gracefully
$(document).on('click', "#comment-provider-google a.comment-provider-logout", function () {
	gapi.auth.signOut();
	$.post(_wdcp_ajax_url, {
		"action": "wdcp_google_logout"
    }, function (data) {
		window.location.reload(); // Refresh
    });
	return false;
});

// Handle post comment requests
$(document).on('click', "#send-google-comment", function () {
	var comment = $("#google-comment").val();
	var commentParent = $('#comment_parent').val();
	var subscribe = ($("#subscribe").length && $("#subscribe").is(":checked")) ? 'subscribe' : '';

	var to_send = {
		"action": "wdcp_post_google_comment",
		"post_id": _wdcp_data.post_id,
		"comment_parent": commentParent,
		"subscribe": subscribe,
		"comment": comment
    };
    $(document).trigger('wdcp_preprocess_comment_data', [to_send]);
	// Start UI change...
	$(this).parents(".comment-provider").empty().append('<div class="comment-provider-waiting-response"></div>');

	$.post(_wdcp_ajax_url, to_send, function (data) {
		$(document).trigger('wdcp_comment_sent', ['google']);
		window.location.reload(); // Refresh
    });
	return false;
});

$(function () {
(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/client:plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
});

})(jQuery);