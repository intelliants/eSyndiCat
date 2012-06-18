$(function()
{
	$("#auto_generate").click(function()
	{
		if($(this).attr("checked"))
		{
			intelli.display('passwords', 'hide');

			$("#pass1").attr("disabled", "disabled");
			$("#pass2").attr("disabled", "disabled");
		}
		else
		{
			intelli.display('passwords', 'show');
			
			$("#pass1").removeAttr("disabled");
			$("#pass2").removeAttr("disabled");
		}
	});

	if(!$("#auto_generate").attr("checked"))
	{
		intelli.display('passwords', 'show');
	}
});