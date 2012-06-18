Ext.onReady(function()
{
	$("#update").click(function()
	{
		Ext.Msg.show(
		{
			title: intelli.admin.lang.warning,
			msg: intelli.admin.lang.update_overwrite_note,
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.WARNING,
			fn: function(btn)
			{
				if('yes' == btn)
				{
					$("#update_form").submit();
				}
			}
		});

		return false;
	});
});
