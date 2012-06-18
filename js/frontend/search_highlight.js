$(function()
{
	if(pWhat!='')
	{
		var justOneWord = true;

		if($('input[id^=any]').attr("checked") || $('input[id^=all]').attr("checked"))
		{
			var allWords = new Array;

			allWords = pWhat.split(" ");
			justOneWord = allWords.length ==1 ? true : false;

			if(!justOneWord)
			{
				for(i=0; i<allWords.length; i++)
				{
					if( allWords[i].length > 2 )
					{
						var pat = "("+allWords[i]+")";

						$('div.title a.title').each(function()
						{
							var th = $(this).html();

							th = doHighlight(th, allWords[i]);
							
							$(this).html(th);
						});

						$('div.description').each(function()
						{console.log('123');
							var th = $(this).html();

							th = doHighlight(th, allWords[i]);

							$(this).html(th);
						});
						
						$('a.categories').each(function()
						{
							var th = $(this).html();

							th = doHighlight(th, allWords[i]);
							
							$(this).html(th);
						});
					}
				}
			}
		}

		if($('input[id^=exact]').attr("checked") || justOneWord)
		{
			pWhat = "("+pWhat+")";

			$('div.title a.title').each(function()
			{
				var th = $(this).html();
				var re = new RegExp(pWhat,"gi");

				if(re.test(th))
				{
					th = th.replace(re, '<span class="highlight">$1</span>');
					$(this).html(th);
				}
			});
			
			$('a.categories').each(function()
			{
				var th = $(this).html();
				var re = new RegExp(pWhat,"gi");

				if(re.test(th))
				{
					th = th.replace(re, '<span class="highlight">$1</span>');
					$(this).html(th);
				}
			});

			$('div.description').each(function()
			{
				var th = $(this).html();
				var re = /<\/?\w+[^>]*>/gi;
				var tmp = th.split(re);

				var re2 = new RegExp(pWhat,"mgi");

				for(var i=0; i<tmp.length;i++)
				{
					var n =	tmp[i];
					var s = n.replace(re2, '<span class="highlight">$1</span>');
					th = th.replace(n,s);
				}

				$(this).html(th);
			});
		}
	}
	
	$("#adv_cat_search_submit").click(function() 
	{
		$("#adv_cat_search_form").submit();
		return false;
	});
});

function doHighlight(bodyText, searchTerm, highlightStartTag, highlightEndTag) 
{
	if ((!highlightStartTag) || (!highlightEndTag))
	{
		highlightStartTag = '<span class="highlight">';
		highlightEndTag = '</span>';
	}
  
	var newText = "";
	var i = -1;
	var lcSearchTerm = searchTerm.toLowerCase();
	var lcBodyText = bodyText.toLowerCase();
    
	while (bodyText.length > 0)
	{
		i = lcBodyText.indexOf(lcSearchTerm, i+1);
		
		if (i < 0)
		{
			newText += bodyText;
			bodyText = "";
		}
		else
		{
			// skip anything inside an HTML tag
			if (bodyText.lastIndexOf(">", i) >= bodyText.lastIndexOf("<", i))
			{
				// skip anything inside a <script> block
				if (lcBodyText.lastIndexOf("/script>", i) >= lcBodyText.lastIndexOf("<script", i))
				{
					newText += bodyText.substring(0, i) + highlightStartTag + bodyText.substr(i, searchTerm.length) + highlightEndTag;
					bodyText = bodyText.substr(i + searchTerm.length);
					lcBodyText = bodyText.toLowerCase();
					i = -1;
				}
			}
		}
	}
  
	return newText;
}

