(function ($) {
$(function () {

function send_request (request) {
	$.post(_wdcp_ajax_url, request, function (data) {
		$(document).trigger('wdcp_comment_sent', ['facebook']);
		window.location.reload(); // Refresh
    });
}

// Bind local event handlers
$(document).bind('wdcp_facebook_login_attempt', function () {
	FB.login(function (resp) {
		if (resp.authResponse && resp.authResponse.userID) $(document).trigger('wdcp_logged_in', ['facebook']);
	}, {scope: 'email'});
});
// Attempt auto-connect
if ($("#login-with-facebook").length) {
	if (typeof FB != "undefined") FB.getLoginStatus(function (resp) {
		if (resp.authResponse && resp.authResponse.userID) $(document).trigger('wdcp_logged_in', ['facebook', true]);
	});
}

// Handle logout requests gracefully
$(document).on('click', "#comment-provider-facebook a.comment-provider-logout", function () {
	var href = $(this).attr('href');
	$.post(_wdcp_ajax_url, {
		"action": "wdcp_facebook_logout"
    }, function (data) {
		FB.logout(function (resp) {
			window.location.reload(); // Refresh
		});
    });
	return false;
});

// Handle post comment requests
$(document).on('click', "#send-facebook-comment", function () {
	var comment = $("#facebook-comment").val(),
		repost = !!$("#post-on-facebook").is(":checked"),
		commentParent = $('#comment_parent').val(),
		subscribe = ($("#subscribe").length && $("#subscribe").is(":checked")) ? 'subscribe' : ''
	;

	var to_send = {
		"action": "wdcp_post_facebook_comment",
		"post_id": _wdcp_data.post_id,
		"post_on_facebook": 0,
		"comment_parent": commentParent,
		"subscribe": subscribe,
		"comment": comment
    };
    $(document).trigger('wdcp_preprocess_comment_data', [to_send]);
	// Start UI change...
	$(this).parents(".comment-provider").empty().append('<div class="comment-provider-waiting-response"></div>');

	if (repost) {
		FB.login(function (resp) {
			if (!resp.authResponse) return false;
			if (!resp.authResponse.grantedScopes) return false;
			var do_repost = !!resp.authResponse.grantedScopes.match(/publish_actions/);
			to_send.post_on_facebook = do_repost ? 1 : 0;
			send_request(to_send);
		}, {scope: 'publish_actions', return_scopes: true});
	} else {
		send_request(to_send);
	}
	return false;
});


// Attempt clearing out old style FB profile links
$('[class*="comment"] a[href*="facebook.com/profile.php?id="]').each(function () {
	var $me = jQuery(this),
		href = $me.attr('href'),
		nhref = (href && href.length ? href.split('?id=') : false)
	;
	if (!nhref) return true;

	$me.attr('href', 'https://facebook.com/' + nhref[1]);
});

});
})(jQuery);
