$(function()
{
	var obj = new Object();
	var	id = '';

	if (intelli.readCookie("first") == null)
	{
		$("div.collapsible").each(function() 
		{
			if ($(this).parent().attr("id") != "block_")
			{
				$(this).before('<div class="minmax"></div>');
				
				var id = $(this).attr("id");
				
				if ($(this).css("display") == 'block')
				{
					intelli.createCookie("box_" + id, "block");
				}
				else
				{
					intelli.createCookie("box_" + id, "none");
				}
			}
		});
		
		intelli.createCookie("first", "foo");
	}
	else
	{
		$("div.collapsible").each(function()
		{
			if ($(this).parent().attr("id") != "block_")
			{
				var id = $(this).attr("id");
				
				if (intelli.readCookie("box_" + id) == "block")
				{
					$(this).before('<div class="minmax" style="background-position: 0px -23px;"></div>');
				}
				else
				{
					if($(this).hasClass('collapsed'))
					{
						$(this).before('<div class="minmax" style="background-position: 0px 0px;"></div>');
					}
					else
					{
						$(this).before('<div class="minmax" style="background-position: 0px -23px;"></div>');	
					}
				}
				
				var box_state = intelli.readCookie("box_" + id);

				if(box_state)
				{
					$(this).css("display", box_state);
					$(this).next().css("display", box_state);
				}
			}
		});
	}

	$("div.minmax").each(function() 
	{
		$(this).click(function()
		{
			var obj = $(this).next();
			var id = obj.attr("id");
			
			if (obj.css("display") == "block")
			{
				$(this).css("backgroundPosition", "0px 0px");
				obj.slideUp("slow");
				obj.next().hide();
				intelli.createCookie("box_" + id, "none");
			}
			else
			{
				$(this).css("backgroundPosition", "0px -23px");
				obj.slideDown("slow");
				obj.next().show();
				intelli.createCookie("box_" + id, "block");
			}
		});
	});
});
