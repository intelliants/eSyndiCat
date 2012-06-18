if (1 == intelli.config.html_comments)
{
	$("textarea.ckeditor_textarea").each(function()
	{
		if(!CKEDITOR.instances[$(this).attr("id")])
		{
			intelli.ckeditor($(this).attr("id"), {toolbar: 'Basic'});
		}
	});	
}

$(function()
{
	if(intelli.config.listing_rating == 1)
	{
		var comment_rating = new commentRating({
			el: 'comment-rating',
			max: intelli.config.listing_rating_max,
			text: intelli.lang.comment_rate_this,
			cls: 'form_'
		});
		
		comment_rating.init();
	}

	var comment_textcounter = new intelli.textcounter({
		textarea_el: 'comment_form'
		,counter_el: 'comment_counter'
		,max: intelli.config.comment_max_chars
		,min: intelli.config.comment_min_chars
	});
	
	comment_textcounter.init();
	
	$("#comment").validate(
	{
		rules:
		{
			author: "required"
			,email:
			{
				required: true
				,email: true
			}
			,url:
			{
				url: true
			}
			,security_code: "required"
			,comment:
			{
				required: true
				,minlength: intelli.config.comment_min_chars
				,maxlength: intelli.config.comment_max_chars
			}
		}
		,submitHandler: function(form)
		{
			var el = $("#add");
			//var form = $(this);

			el.attr("disabled", "disabled");
			el.val("Loading...");
			el.css("background", "url('templates/common/img/ajax-loader.gif') left top no-repeat");
			el.css("padding-left", "15px");

			var author = $("#comment input[name='author']").val();
			var email = $("#comment input[name='email']").val();
			var url = $("#comment input[name='url']").val();
			var rating = $("#comment input[name='form_rating']").val();
			var body = $("#comment_form").val();
			var listing_id = $("input[name='listing_id']").val();
			var security_code = $("input[name='security_code']").val();

			$.post("controller.php?plugin=comments", {action: 'add', author: author, email: email, url: url, rating: rating, body: body, security_code:security_code, listing_id: listing_id}, function(out)
			{
				var data = eval('(' + out + ')');
				var type = data.error ? 'error' : 'notification';
				
				el.attr("disabled", "");
				el.val("Leave comment");
				el.css("background", "");
				el.css("padding-left", "");

				if(!data.error)
				{
					if(1 == intelli.config.comments_approval)
					{
						var html = new Array();

						html = ['<div class="posted">', intelli.lang.comment_author, '&nbsp;',
						'<strong>', data.comment.author, '</strong>&nbsp;/&nbsp;', data.comment.date, '</div>',
						'<div class="comment">', data.comment.body, '</div>'].join('');

						$("#comments_container").append(html);
					}

					$("#comment input[name='author'][type='text']").val('');
					$("#comment input[name='email'][type='text']").val('');
					$("#comment input[name='url'][type='text']").val('');
					$("#comment input[name='security_code'][type='text']").val('');
					$("#captcha_image_1").click();
					$("#comment_form").val('');

					comment_textcounter.init();
				}

				intelli.notifBox(
				{
					id: 'error',
					type: type,
					msg: data.msg
				});
			});
		}
	});
});
