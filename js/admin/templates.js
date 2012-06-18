intelli.templates = function()
{
	var vUrl = 'controller.php?file=templates';

	return {
		vUrl: vUrl
	};
}();

Ext.onReady(function()
{
	$("a.screenshots").each(function()
	{
		$(this).click(function()
		{
			$(this).siblings("a.lb").lightBox();
			$(this).siblings("a.lb:first").click();

			return false;
		});
	});
});
