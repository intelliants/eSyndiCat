intelli.thumbs = function()
{
	this.init = function()
	{
		var aLinkUrls = new Array();
		var thumbService = "http://open.thumbshots.org/image.pxf?url=";
		var counter = 0, id, linkUrl, isLoaded = false, x, y;
		var clientWidth = document.body.clientWidth;

		$("div.url").mouseover(function() 
		{
			$("div.thumb").fadeIn(100);
			isLoaded = false;
			linkUrl = $(this).text();
			for (i = 0; i < counter; i++)
			{
				if (linkUrl == aLinkUrls[i])
				{
					isLoaded = true;
					break;
				}
			}
			id = i;
			/* hide all thumbs */
			for (i = 0; i < counter; i++)
			{
				$("img.thumb" + i).hide();
			}
			if (isLoaded)
			{
				$("div.loading").hide();
				$("img.thumb" + id).show();
			}
			else
			{
				if ($(this).parents("td").find("input[type=hidden]").val())
					$("div.thumb").append('<img class="thumb' + id + '" src="' + $(this).parents("td").find("input[type=hidden]").val() + '" alt="" />');					
				else 
					$("div.thumb").append('<img class="thumb' + id + '" src="' + thumbService + linkUrl + '" alt="" />');
				if ($.browser.opera)
				{
					$("img.thumb" + id).fadeIn(100);
				}
				else
				{
					$("img.thumb" + id).hide();
					$("div.loading").show();
					$("img.thumb" + id).load(function(){
						$("div.loading").hide();
						$("img.thumb" + id).fadeIn(100);
					});
				}
				aLinkUrls[counter] = linkUrl;
				counter++;
			}
		});

		$("div.url").mousemove(function(e) 
		{
			x = e.pageX - 5;
			y = e.pageY + 15;
			if (x > document.body.clientWidth - 200)
			{
				x = document.body.clientWidth - 200;
			}
			$("div.thumb").css({top: y, left: x});
		});
		
		$("div.url").mouseout(function() 
		{
			$("div.thumb").fadeOut(100);
		});
	};
};
