Ext.onReady(function()
{
	$("div.selecting a").each(function()
	{
		$(this).click(function()
		{
			var selected = ('select' == $(this).attr("class")) ? 'selected' : '';

			$('#tbl option').attr('selected', selected);

			return false;
		});
	});

	$("#save_file").click(function()
	{
		var display = $(this).attr("checked") ? 'block' : 'none';

		$("#save_to").css("display", display);
	});
	
	$("#exportAction").click(function()
	{
		if($("#sql_structure").attr("checked") || $("#sql_data").attr("checked"))
		{
			$("#export").attr("value", "1");
			$("#dump").submit();

			return true;
		}
		else
		{
			intelli.admin.alert(
			{
				title: intelli.admin.lang.error,
				type: 'error',
				msg: intelli.admin.lang.export_not_checked
			});
		}

		return false;
	});

	$("#importAction").click(function()
	{
		if($("#sql_file").attr("value"))
		{
			$("#run_update").attr("value", "1");
			$("#update").submit();

			return true;
		}
		else
		{
			intelli.admin.alert(
			{
				title: intelli.admin.lang.error,
				type: 'error',
				msg: intelli.admin.lang.choose_import_file
			});
		}
		
		return false;
	});


	$("#addTableButton").click(function()
	{
		addData('table');
	});

	$("#table").dblclick(function()
	{
		addData('table');
	});

	var tables = [];
	
	$("#table").click(function()
	{
		var table = $(this).attr("value");

		if(table)
		{
			if (!tables[table])
			{
				$.ajax(
				{ 
					type: "GET", 
					url: "controller.php?file=database",
					data: "action=fields&table=" + table, 
					success: function(data)
					{ 
						var items = eval('(' + data + ')');
						var fields = $("#field")[0];

						tables[table] = items;

						fields.options.length = 0;

						for (var i = 0; i < items.length; i++)
						{
							fields.options[fields.options.length] = new Option(items[i], items[i]);
						}
						
						fields.options[0].selected = true;

						// Show dropdown and the button
						$("#field").fadeIn();
						$("#addFieldButton").fadeIn();
					} 
				});
			}
			else
			{
				var items = tables[table];
				var fields = $("#field")[0];

				fields.options.length = 0;

				for (var i = 0; i < items.length; i++)
				{
					fields.options[fields.options.length] = new Option(items[i], items[i]);
				}
				
				fields.options[0].selected = true;

				// Show dropdown and the button
				$("#field").fadeIn();
				$("#addFieldButton").fadeIn();
			}
		}
	});

	$("#addFieldButton").click(function()
	{
		addData('field');
	});

	$("#field").dblclick(function()
	{
		addData('field');
	});

	$("#clearButton").click(function()
	{
		Ext.Msg.confirm('Question', intelli.admin.lang.clear_confirm, function(btn, text)
		{
			if (btn == 'yes')
			{
				$("#query").attr("value", "SELECT * FROM ");
				$("#field").fadeOut();
				$("#addFieldButton").fadeOut();
			}
		});
		
		return true;
	});

	if($("#backup_message").length > 0)
	{
		$("#server").attr("disabled", "disabled");
	}

	function addData(item)
	{
		var value = $("#" + item).attr("value");

		if (value)
		{
			$("#query").attr("value", $("#query").attr("value") + "`" + value + "` ");
		}
		else
		{
			intelli.admin.alert(
			{
				title: 'Error',
				type: 'error',
				msg: 'Please choose any ' + item + '.'
			});
		}
	}

	/*
	 * Reseting tables
	 */
	$("#all_options").click(function()
	{
		var checked = $(this).attr("checked") ? 'checked' : '';
		
		$("input[name='options[]']").each(function()
		{
			$(this).attr("checked", checked);
		});
	});
});
