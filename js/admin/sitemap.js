$(function() 
{
	$("#start").bind('click', function() {
		
		intelli.sitemap.type_sitemap = $("input:checked").val();
		
		if (1 == intelli.sitemap.pause)
		{
			intelli.sitemap.pause = 0;
			$("input").attr("disabled","disabled");
			$('#start').val(intelli.admin.lang.processing);
			send();
		}
		else
		{
			intelli.sitemap.pause = 1;
			$('#start').val(intelli.admin.lang.start);
		}
	});
});

var send = function()
{
	if (0 != intelli.sitemap.pause) return false;
	
	if ('undefined' == typeof(intelli.sitemap.items[intelli.sitemap.current]))
	{
		intelli.sitemap.pause = 1;
		$("input").removeAttr("disabled");
		$('#start').val(intelli.admin.lang.start);
		intelli.sitemap.current = 0;
		intelli.sitemap.start = 0;
		intelli.sitemap.stage = 1;
		intelli.sitemap.pause = 1;
		return;
	}
	
	if (intelli.sitemap.start >= intelli.sitemap.items[intelli.sitemap.current][1])
	{
		intelli.sitemap.start = 0;
		intelli.sitemap.current++;
		send();
		return false;
	}
	
	Ext.Ajax.request(
	{
		waitMsg: intelli.admin.lang.saving_changes,
		url: intelli.sitemap.url,
		method: 'POST',
		params:
		{
			action	: 'create',
			stage	: intelli.sitemap.stage,
			item	: intelli.sitemap.items[intelli.sitemap.current][0],
			start	: intelli.sitemap.start,
			limit	: intelli.sitemap.limit,
			file	: Math.floor ( ( intelli.sitemap.limit * ( intelli.sitemap.stage) ) / 49999),
			stage_all : intelli.sitemap.stage_all, 
			items_count	: intelli.sitemap.items[intelli.sitemap.current][1],
			type_sitemap : intelli.sitemap.type_sitemap
		},
		failure: function()
		{
			$('#progress_bar').css("width","0%");
			$('#percent').empty().html("0%");
			$('#start').val(intelli.admin.lang.start);
			$("input").removeAttr("disabled");
			Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
		},
		success: function (outdata)
		{
			data = eval('(' + outdata['responseText'] + ')');
			
			if (data['error'])
			{
				Ext.MessageBox.alert(data['msg']);
				intelli.sitemap.pause = 1;
				$("input").removeAttr("disabled");
				$('#start').val(intelli.admin.lang.start);
				intelli.sitemap.current = 0;
				intelli.sitemap.start = 0;
				intelli.sitemap.stage = 1;
				intelli.sitemap.pause = 1;
				return false;
			}
			intelli.sitemap.percent = Math.ceil(intelli.sitemap.stage * 100 / intelli.sitemap.stage_all);
			$('#start_num').val(intelli.sitemap.start);
			$('#progress_bar').css("width",intelli.sitemap.percent+"%");
			$('#percent').empty().html(intelli.sitemap.percent+"%");
			intelli.sitemap.start = intelli.sitemap.start + intelli.sitemap.limit;
			intelli.sitemap.stage++;
			send();
		}
	});
};