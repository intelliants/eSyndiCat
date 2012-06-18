$(function ()
{
	$('div.groupWrapper').Sortable(
	{
		accept: 'box',
		helperclass: 'sortHelper',
		activeclass: 'dropActive',
		hoverclass: 'dropHover',
		tolerance: 'intersect',
		fx: function()
		{
		},
		onChange: function(groups)
		{
			var serial = '';
			var blocks = new Array();

			$.each(intelli.config.esyndicat_block_positions.split(','), function(i, v)
			{
				var b = $.SortSerialize(v + 'Blocks').hash;

				if(b)
				{
					blocks[i] = b;
				}
			});
			
			serial = blocks.join('&');

			$.get("order-change.php?type=blocks&"+serial, function()
			{
				$.each(groups, function(i, o)
				{
					$("#" + o.id).animate({backgroundColor: '#FFFF99'}, 300, function()
					{
						$(this).animate({backgroundColor: 'lightgreen'}, 300);
					});
				});
			});
		}
	}).each(function()
	{
		var id = $(this).attr("id").split("Blocks");

		$(this).prepend('<div style="text-align:center;font-size:16px;">&quot;' + id[0] + '&quot; blocks' + '</div>');
	});
});
