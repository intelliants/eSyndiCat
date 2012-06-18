(function($) {
	$.fn.twitterList = function(username) {
		
		var twitterBlock = this;
		var pattern = /(http\S+)/gi;
		var relative_time = function(time_value) {
			var parsed_date = Date.parse(time_value);
			var relative_to = (arguments.length > 1) ? arguments[1]
					: new Date();
			var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
			if (delta < 60) {
				return 'less than a minute ago';
			} else if (delta < 120) {
				return 'about a minute ago';
			} else if (delta < (45 * 60)) {
				return (parseInt(delta / 60)).toString() + ' minutes ago';
			} else if (delta < (90 * 60)) {
				return 'about an hour ago';
			} else if (delta < (24 * 60 * 60)) {
				return 'about ' + (parseInt(delta / 3600)).toString()
						+ ' hours ago';
			} else if (delta < (48 * 60 * 60)) {
				return '1 day ago';
			} else {
				return (parseInt(delta / 86400)).toString() + ' days ago';
			}
		};
		
		intelli.twites_func = function(twites) {
			var text;
			$.each(twites,function(i, tweet) {
				text = '<a target="_blank" href="http://twitter.com/' + username + '" rel="nofollow" class="tb_photo">' +
							'<img alt="virtualformac" src="' + tweet.user.profile_image_url + '" />' +
						'</a>' +
						'<span class="tb_author">' +
							'<a target="_blank" href="http://twitter.com/' + username + '" rel="nofollow">' + username + '</a>: ' +
						'</span>' +
						'<span class="tb_msg">' + tweet.text.replace(pattern,'<a href="$1" target="_blank" rel="nofollow">$1</a>') + '</span>' +
						'<p class="tb_tweetinfo">' +
							'<a href="http://twitter.com/' + username + '/statuses/' + tweet.id + '" target="_blank" rel="nofollow">' +
							relative_time(tweet.created_at) + '</a> from ' + tweet.source +
						'</p>';
				$('<div id="' + tweet.id + '" class="tb_tweet">' + text + '</div>').appendTo(twitterBlock);
			});
		};
		$.ajax({
			url : 'http://api.twitter.com/1/statuses/user_timeline.json?callback=intelli.twites_func',
			dataType : 'jsonp',
			type : 'GET',
			data : 'screen_name=' + username + '&count=' + 5
		});
	};
})(jQuery);