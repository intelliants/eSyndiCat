$(function()
{
	$("a[class^='actions']").each(function()
	{
		$(this).click(function()
		{
			var params = $(this).attr("class").split("_");

			if('broken' == params[1])
			{
				intelli.common.reportBrokenListing(params[2]);
			}

			if('add-favorite' == params[1])
			{
				intelli.common.actionFavorites(params[2], params[3], 'add');
			}

			if('remove-favorite' == params[1])
			{
				intelli.common.actionFavorites(params[2], params[3], 'remove');
			}

			if('move' == params[1])
			{
				intelli.common.moveListing(params[2], params[3]);
			}

			return false;
		});
	});
});
