intelli.categoryIcons = function()
{
	var vUrl = 'controller.php?file=category-icons';

	return {
		vUrl: vUrl
	};
}();

Ext.onReady(function()
{
	var store = new Ext.data.JsonStore({
        url: intelli.categoryIcons.vUrl + '&action=getimages',
        root: 'data',
        fields: ['name', 'url', {name:'size', type: 'float'}, {name:'lastmod', type:'date', dateFormat:'timestamp'}]
    });

	store.load();

    var tpl = new Ext.XTemplate(
		'<tpl for=".">',
            '<div class="thumb-wrap" id="{name}">',
		    '<div class="thumb"><img src="' + intelli.config.esyn_url + '{url}" title="{name}"></div>',
		    '<span class="x-editable">{shortName}</span></div>',
        '</tpl>',
        '<div class="x-clear"></div>'
	);

	var dataView = new Ext.DataView({
		store: store,
		tpl: tpl,
		autoHeight: true,
		multiSelect: true,
		overClass: 'x-view-over',
		itemSelector: 'div.thumb-wrap',
		emptyText: intelli.admin.lang.no_images,
		plugins: [
			new Ext.DataView.DragSelector()
			//new Ext.DataView.LabelEditor({dataIndex: 'name'})
		],
		prepareData: function(data)
		{
			data.shortName = Ext.util.Format.ellipsis(data.name, 15);
			data.sizeString = Ext.util.Format.fileSize(data.size);
			data.dateString = data.lastmod.format("m/d/Y g:i a");
			
			return data;
		},
		listeners:
		{
			selectionchange:
			{
				fn: function(dv,nodes)
				{
					var l = nodes.length;
					var s = l > 1 ? intelli.admin.lang.items_selected : intelli.admin.lang.item_selected;
					
					category_icons_panel.setTitle(intelli.admin.lang.category_icons + ' (' + l + ' ' + s + ' )');

					if(l > 0)
					{
						removeBtn.enable();

						if (l == 1)
						{
							defaultBtn.enable();
						}
						else
						{
							defaultBtn.disable();
						}
					}
					else
					{
						removeBtn.disable();
						defaultBtn.disable();

						dv.getStore().each(function(n)
						{
							if (n.json.default)
							{
								dv.select(n.json.name);
							}
						});
					}
            	}
           	}
		}
	});

	dataView.store.on('load', function(s, r, o)
	{
		for (var i = 0; i < r.length; i++)
		{
			if (r[i].json['default'])
			{
				dataView.select(r[i].json.name);
			}
		}
	});

	var removeBtn = new Ext.Button({
	    text: intelli.admin.lang.remove,
		disabled: true,
        renderTo: 'box_remove_button',
		handler: function()
		{
			Ext.Msg.show(
			{
				title: intelli.admin.lang.confirm,
				msg: intelli.admin.lang.are_you_sure_to_delete_selected_icons,
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function(btn)
				{
					var rows = dataView.getSelectedRecords();
					var ids = new Array();

					for(var i = 0; i < rows.length; i++)
					{
						if (rows[i].json.removeable)
						{
							ids[i] = rows[i].data.name;
						}
					}

					if('yes' == btn)
					{
						Ext.Ajax.request(
						{
							url: intelli.categoryIcons.vUrl,
							method: 'POST',
							params:
							{
								action: 'remove',
								'icons[]': ids
							},
							failure: function()
							{
								Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
							},
							success: function(data)
							{
								var response = Ext.decode(data.responseText);
								var type = response.error ? 'error' : 'notif';

								intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});

								store.reload();
							}
						});
					}
				}
			});
		}
    });

	var defaultBtn = new Ext.Button({
	    text: intelli.admin.lang.set_as_default,
		disabled: true,
        renderTo: 'box_default_button',
		handler: function()
		{
			Ext.Msg.show(
			{
				title: intelli.admin.lang.confirm,
				msg: intelli.admin.lang.are_you_sure_to_set_as_default_selected_icons,
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function(btn)
				{
					var rows = dataView.getSelectedRecords();
					var url = '';

					for(var i = 0; i < rows.length; i++)
					{
						url = rows[i].json.url;
					}

					if('yes' == btn)
					{
						Ext.Ajax.request(
						{
							url: intelli.categoryIcons.vUrl,
							method: 'POST',
							params:
							{
								action: 'default',
								url: url.replace(intelli.config.esyn_url, '')
							},
							failure: function()
							{
								Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
							},
							success: function(data)
							{
								var response = Ext.decode(data.responseText);
								var type = response.error ? 'error' : 'notif';

								intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});

								store.reload();
							}
						});
					}
				}
			});
		}
    });

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
			emptyText: intelli.admin.lang.select_icon,
			fieldLabel: intelli.admin.lang.icon,
			allowBlank: false,
			name: 'icon'
		}],
		buttons: [
		{
			text: intelli.admin.lang.upload,
			handler: function()
			{
				if(uploadForm.getForm().isValid())
				{
					uploadForm.getForm().submit(
					{
						url: intelli.categoryIcons.vUrl + '&action=upload',
						waitMsg: intelli.admin.lang.uploading_icon + '...',
						success: function(form, o)
						{
							var icon = (o.result.error) ? Ext.MessageBox.ERROR : Ext.MessageBox.INFO;

							Ext.Msg.show(
							{
								title: intelli.admin.lang.uploading_icon,
								msg: o.result.msg,
								buttons: Ext.Msg.OK,
								icon: icon
							});

							form.reset();

							store.reload();
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

	var upload_window = new Ext.Window(
	{
		title: intelli.admin.lang.upload_new_category_icon,
		width: 515,
		modal: true,
		resizable: false,
		autoScroll: true,
		closeAction : 'hide',
		items: uploadForm,
		buttons: [
		{
			text: 'Close',
			handler: function()
			{
				upload_window.hide();
			}
		}]
	});

	var category_icons_panel = new Ext.Panel({
		id: 'images-view',
		title: intelli.admin.lang.category_icons + ' (0 ' + intelli.admin.lang.item_selected + ' )',
		border: true,
		renderTo: 'box_category_icons',
        items: dataView
	});
	
	$("#upload_icon").click(function()
	{
		upload_window.show();

		return false;
	});
});
