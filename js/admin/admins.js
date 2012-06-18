intelli.admins = function()
{
	var vUrl = 'controller.php?file=admins';

	return {
		oGrid: null,
		vUrl: vUrl,
		statusesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['active', 'active'],['inactive', 'inactive']]
		}),
		pagingStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['10', '10'],['20', '20'],['30', '30'],['40', '40'],['50', '50']]
		})
	};
}();

intelli.exGModel = Ext.extend(intelli.gmodel,
{
	constructor: function(config)
	{
		intelli.exGModel.superclass.constructor.apply(this, arguments);
	},
	setupReader: function()
	{
		this.record = Ext.data.Record.create([
			{name: 'username', mapping: 'username', type: 'string'},
			{name: 'fullname', mapping: 'fullname', type: 'string'},
			{name: 'email', mapping: 'email'},
			{name: 'status', mapping: 'status'},
			{name: 'date_reg', mapping: 'date_reg'},
			{name: 'last_visited', mapping: 'last_visited'},
			{name: 'edit', mapping: 'edit'},
			{name: 'remove', mapping: 'remove'}
		]);

		this.reader = new Ext.data.JsonReader({
			root: 'data',
			totalProperty: 'total',
			id: 'id'
			}, this.record
		);

		return this.reader;
	},
	setupColumnModel: function()
	{
		this.columnModel = new Ext.grid.ColumnModel([
		this.checkColumn,
		{
			header: intelli.admin.lang.username, 
			dataIndex: 'username', 
			sortable: true, 
			width: 250
		},{
			header: intelli.admin.lang.fullname, 
			dataIndex: 'fullname', 
			sortable: true,
			width: 250
		},{
			header: intelli.admin.lang.email, 
			dataIndex: 'email', 
			sortable: true,
			width: 250
		},{
			header: intelli.admin.lang.date,
			dataIndex: 'date_reg',
			width: 130,
			sortable: true
		},{
			header: intelli.admin.lang.last_visited,
			dataIndex: 'last_visited',
			width: 130,
			sortable: true
		},{
			header: intelli.admin.lang.status, 
			dataIndex: 'status',
			width: 100,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.admins.statusesStore,
				displayField: 'display',
				valueField: 'value',
				mode: 'local'
			})
		},{
			header: "", 
			dataIndex: 'edit',
			width: 40,
			hideable: false,
			menuDisabled: true,
			align: 'center'
		},{
			header: "",
			dataIndex: 'remove',
			width: 40,
			hideable: false,
			menuDisabled: true,
			align: 'center'
		}]);

		return this.columnModel;
	}
});

intelli.exGrid = Ext.extend(intelli.grid,
{
	model: null,
	constructor: function(config)
	{
		intelli.exGrid.superclass.constructor.apply(this, arguments);

		this.model = new intelli.exGModel({url: config.url});
			
		this.dataStore = this.model.setupDataStore();
		this.columnModel = this.model.setupColumnModel();
		this.selectionModel = this.model.setupSelectionModel();

		this.dataStore.setDefaultSort('username');
	},
	init: function()
	{
		this.plugins = new Ext.ux.PanelResizer({
            minHeight: 100
		});

		this.title = intelli.admin.lang.admins;
		this.renderTo = 'box_admins';

		this.setupBaseParams();
		this.setupPagingPanel();
		this.setupGrid();

		this.setRenderers();
		this.setEvents();

		this.grid.autoExpandColumn = 2;

		this.loadData();
	},
	setupPagingPanel: function()
	{
		this.bottomToolbar = new Ext.PagingToolbar(
		{
			store: this.dataStore,
			pageSize: 10,
			displayInfo: true,
			plugins: new Ext.ux.ProgressBarPager(),
			items: [
				'-',
				intelli.admin.lang.items_per_page + ':',
				{
					xtype: 'bettercombo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					width: 80,
					store: intelli.admins.pagingStore,
					value: '20',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'pgnPnl'
				},
				'-',
				intelli.admin.lang.status + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.admins.statusesStore,
					value: 'active',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					disabled: true,
					id: 'statusCmb'
				},
				{
					text: intelli.admin.lang['do'],
					id: 'goBtn',
					iconCls: 'go-grid-ico',
					disabled: true,
					handler: function()
					{
						var rows = intelli.admins.oGrid.grid.getSelectionModel().getSelections();
						var status = Ext.getCmp('statusCmb').getValue();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Ajax.request(
						{
							url: intelli.admins.vUrl,
							method: 'POST',
							params:
							{
								action: 'update',
								'ids[]': ids,
								field: 'status',
								value: status
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

								intelli.admins.oGrid.grid.getStore().reload();
							}
						});
					}
				},
				'-',
				{
					text: intelli.admin.lang.remove,
					id: 'removeBtn',
					iconCls: 'remove-grid-ico',
					disabled: true,
					handler: function()
					{
						var rows = intelli.admins.oGrid.grid.getSelectionModel().getSelections();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							if(0 != rows[i].json.remove)
							{
								ids[i] = rows[i].json.id;
							}
						}

						if(ids.length > 0)
						{
							Ext.Msg.show(
							{
								title: intelli.admin.lang.confirm,
								msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_admins : intelli.admin.lang.are_you_sure_to_delete_this_admin,
								buttons: Ext.Msg.YESNO,
								icon: Ext.Msg.QUESTION,
								fn: function(btn)
								{
									if('yes' == btn)
									{
										Ext.Ajax.request(
										{
											url: intelli.admins.vUrl,
											method: 'POST',
											params:
											{
												action: 'remove',
												'ids[]': ids
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

												intelli.admins.oGrid.grid.getStore().reload();

												Ext.getCmp('statusCmb').disable();
												Ext.getCmp('goBtn').disable();
												Ext.getCmp('removeBtn').disable();
											}
										});
									}
								}
							});
						}
					}
				}
			]
		});
	},
	setupBaseParams: function()
	{
		this.dataStore.baseParams = {action: 'get'};
	},
	setRenderers: function()
	{
		this.columnModel.setRenderer(5, function(value, metadata)
		{
			if ('0000-00-00 00:00:00' == value)
			{
				value = intelli.admin.lang.never;
			}

			return value;
		});

		/* change background color for status field */
		this.columnModel.setRenderer(6, function(value, metadata)
		{
			metadata.css = value;

			return value;
		});

		/* add edit link */
		this.columnModel.setRenderer(7, function(value, metadata)
		{
			return '<img class="grid_action" alt="'+ intelli.admin.lang.edit +'" title="'+ intelli.admin.lang.edit +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/edit-grid-ico.png" />';
		});

		/* add remove link */
		this.columnModel.setRenderer(8, function(value, metadata)
		{
			if(1 == value)
			{
				return '<img class="grid_action" alt="'+ intelli.admin.lang.remove +'" title="'+ intelli.admin.lang.remove +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/remove-grid-ico.png" />';
			}
		});
	},
	setEvents: function()
	{
		/*
		 * Events
		 */

		intelli.admins.oGrid.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('edit' == fieldName)
			{
				intelli.admins.oGrid.saveGridState();

				window.location = 'controller.php?file=admins&do=edit&id='+ record.json.id;
			}

			if('remove' == fieldName)
			{
				// don't allow to remove current admin
				if(0 == data)
				{
					return false;
				}

				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_this_admin,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.admins.vUrl,
								method: 'POST',
								params:
								{
									action: 'remove',
									'ids[]': record.id
								},
								failure: function()
								{
									Ext.MessageBox.alert(intelli.admin.error_saving_changes);
								},
								success: function(data)
								{
									var response = Ext.decode(data.responseText);
									var type = response.error ? 'error' : 'notif';
									
									intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});

									Ext.getCmp('statusCmb').disable();
									Ext.getCmp('goBtn').disable();
									Ext.getCmp('removeBtn').disable();

									grid.getStore().reload();
								}
							});
						}
					}
				});
			}
		});

		/* activate actions button */
		intelli.admins.oGrid.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('statusCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('removeBtn').enable();
		});

		/* deactivate actions button */
		intelli.admins.oGrid.grid.getSelectionModel().on('rowdeselect', function(sm)
		{
			if(0 == sm.getCount())
			{
				Ext.getCmp('statusCmb').disable();
				Ext.getCmp('goBtn').disable();
				Ext.getCmp('removeBtn').disable();
			}
		});

		/* Edit fields */
		intelli.admins.oGrid.grid.on('afteredit', function(editEvent)
		{
			Ext.Ajax.request(
			{
				url: intelli.admins.vUrl,
				method: 'POST',
				params:
				{
					action: 'update',
					'ids[]': editEvent.record.id,
					field: editEvent.field,
					value: editEvent.value
				},
				failure: function()
				{
					Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
				},
				success: function(data)
				{
					var response = Ext.decode(data.responseText);
					var type = response.error ? 'error' : 'notif';
						
					intelli.admin.notifFloatBox({msg: response.msg, type: type, autohide: true});

					intelli.admins.oGrid.grid.getStore().reload();
				}
			});
		});

		/* Paging panel event */
		Ext.getCmp('pgnPnl').on('change', function(field, new_value, old_value)
		{
			intelli.admins.oGrid.grid.getStore().lastOptions.params.limit = new_value;
			intelli.admins.oGrid.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.admins.oGrid.grid.getStore().reload();
		});
	}
});

Ext.onReady(function()
{
	if(Ext.get('box_admins'))
	{
		intelli.admins.oGrid = new intelli.exGrid({url: intelli.admins.vUrl});

		/* Initialization grid */
		intelli.admins.oGrid.init();
	}

	$("input[name='super']").each(function()
	{
		$(this).click(function()
		{
			var display = (0 == $(this).attr("value")) ? 'block' : 'none';

			$("#permissions").css("display", display);
		});
	});

	if(1 == $("input[name='super'][value='0']:checked").length)
	{
		$("#permissions").css("display", "block");
	}

	$("#select_all_permis").click(function()
	{
		var checked = $(this).attr("checked") ? 'checked' : '';

		$("input[name='permissions[]']").each(function()
		{
			$(this).attr("checked", checked);
		});
	});

	if($("#select_all_permis").attr("checked"))
	{
		$("input[name='permissions[]']").each(function()
		{
			$(this).attr("checked", "checked");
		});
	}
});
