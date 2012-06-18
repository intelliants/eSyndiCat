intelli.configuration = function()
{
	var vUrl = 'controller.php?file=configuration';

	return {
		vUrl: vUrl
	};
}();

Ext.onReady(function()
{
	$("#show_htaccess").click(function()
	{
		var display = 'block' == $("#htaccess").css("display") ? 'hide' : 'show';
		var button_clone = $("#htaccess a.button").clone(true);
		
		if(3 == button_clone.length)
		{
			for(var i = button_clone.length - 1; i >= 0; i--)
			{
				$("#htaccess_code").after(button_clone[i]);
				$("#htaccess_code").after('&nbsp;');
			}
		}

		$("#htaccess")[display]();

		intelli.initCopy2clipboard();

		return false;
	});

	$("#download").click(function()
	{
		window.location = window.location.href + '&download_htaccess';

		return false;
	});

	function showImagePanel(conf)
	{
		/*
		* Vewing Site Logo image
		*/
		var uploadForm = new Ext.FormPanel({
		   fileUpload: true,
		   border: false,
		   width: 500,
		   frame: true,
		   autoHeight: true,
		   bodyStyle: 'padding: 10px 10px 0 10px;',
		   labelWidth: 60,
		   defaults:
		   {
			   anchor: '95%',
			   allowBlank: false,
			   msgTarget: 'side'
		   },
		   items: [
		   {
			   xtype: 'fileuploadfield',
			   id: 'form-file',
			   emptyText: intelli.admin.lang.select_image,
			   fieldLabel: intelli.admin.lang.image,
			   allowBlank: false,
			   name: conf
		   }],
		   buttons: [
		   {
			   text: intelli.admin.lang.remove,
			   disable: '' != intelli.config[conf] ? false : true,
			   handler: function()
			   {
				   Ext.Ajax.request(
				   {
					   url: intelli.configuration.vUrl,
					   method: 'GET',
					   params:
					   {
						   action: 'remove_image',
						   conf: conf
					   },
					   failure: function()
					   {
						   Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
					   },
					   success: function(data)
					   {
						   Ext.get(viewImage.getEl().query('img')).remove();

						   $('#conf_' + conf + '').siblings('a').remove();
						   
						   upload_window.hide().show();
					   }
				   });
			   }
		   },{
			   text: intelli.admin.lang.upload,
			   handler: function()
			   {
				   if(uploadForm.getForm().isValid())
				   {
					   uploadForm.getForm().submit(
					   {
						   url: intelli.configuration.vUrl + '&action=upload&conf=' + conf,
						   waitMsg: intelli.admin.lang.uploading_image,
						   success: function(form, o)
						   {
							   var icon = (o.result.error) ? Ext.MessageBox.ERROR  : Ext.MessageBox.INFO;
   
							   Ext.Msg.show(
							   {
								   title: intelli.admin.lang.uploading_image,
								   msg: o.result.msg,
								   buttons: Ext.Msg.OK,
								   icon: icon
							   });
   
							   form.reset();
   
							   viewImage.getEl().update('<img src="' + intelli.config.esyn_url + 'uploads/' + o.result.file_name + '" alt="" />');
						   }
					   });
				   }
			   }
		   },{
			   text: intelli.admin.lang.reset,
			   handler: function()
			   {
				   uploadForm.getForm().reset();
			   }
		   }]
		});
		
		var viewImage = new Ext.Panel(
		{
			border: false,
			bodyStyle: "text-align: center",
			autoLoad:
			{
				url: intelli.configuration.vUrl + '&action=get_image&conf=' + conf,
				scripts: false
			}
		});
	
		var upload_window = new Ext.Window(
		{
			title: intelli.admin.lang.uploading_image,
			border: false,
			width: 515,
			modal: true,
			resizable: false,
			autoScroll: true,
			closeAction : 'hide',
			items: [viewImage, uploadForm],
			buttons: [
			{
				text: intelli.admin.lang.close,
				handler: function()
				{
					upload_window.hide();
				}
			}]
		});
		
		upload_window.show();
	}

	$("a.view_image").click(function()
	{
		var conf = $(this).siblings("input[type='file']").attr("name");
		
		showImagePanel(conf);

		return false;
	});

	$("a.remove_image").click(function()
	{
		var conf = $(this).siblings("input[type='file']").attr("name");
	
		Ext.Ajax.request(
		{
			url: intelli.configuration.vUrl,
			method: 'GET',
			params:
			{
				action: 'remove_image',
				conf: conf
			},
			failure: function()
			{
				Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
			},
			success: function(data)
			{
				$('#conf_' + conf).siblings('a').remove();

				Ext.MessageBox.alert(intelli.admin.lang.image_removed);
			}
		});

		return false;
	});

	$("textarea.cked").each(function()
	{
		intelli.ckeditor($(this).attr("id"), {toolbar: 'User', height: '200px'});
	});

	$.get(intelli.configuration.vUrl, {action: 'permission'}, function(data)
	{
		var data = eval('(' + data + ')');
		
		if(!data)
		{
			new Ext.ToolTip(
			{
				target: 'rebuild',
				html: intelli.admin.lang.notif_htaccess_permission
			});

			$('#rebuild').addClass("disabled");
		}
	});

	$("#copybutton").click(function()
	{
		return false;
	});

	$("#rebuild").click(function()
	{
		if(!$(this).hasClass("disabled"))
		{
			Ext.Ajax.request(
			{
				url: intelli.configuration.vUrl,
				method: 'GET',
				params:
				{
					action: 'rebuild'
				},
				failure: function()
				{
					Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
				},
				success: function(data)
				{
					Ext.MessageBox.alert(intelli.admin.lang.notification, intelli.admin.lang.notif_htaccess_rebuilt);
				}
			});
		}

		return false;
	});

	$("#close").click(function()
	{
		if('block' == $("#htaccess").css("display"))
		{
			$("#htaccess").hide();
		}

		return false;
	});
});
