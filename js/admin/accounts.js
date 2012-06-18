intelli.accounts = function()
{
	var vUrl = 'controller.php?file=accounts';

	return {
		oGrid: null,
		vUrl: vUrl,
		statusesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['active', 'active'],
				['approval', 'approval'],
				['banned', 'banned'],
				['unconfirmed', 'unconfirmed']
			]
		}),
		statusesStoreFilter: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['all', intelli.admin.lang._status_],
				['active', 'active'],
				['approval', 'approval'],
				['banned', 'banned'],
				['unconfirmed', 'unconfirmed']
			]
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
			{name: 'listings', mapping: 'listings'},
			{name: 'email', mapping: 'email'},
			{name: 'status', mapping: 'status'},
			{name: 'date', mapping: 'date_reg'},
			{name: 'sendemail', mapping: 'sendemail'},
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
			width: 250,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: intelli.admin.lang.listings,
			dataIndex: 'listings',
			sortable: true,
			width: 80
		},{
			header: intelli.admin.lang.email,
			dataIndex: 'email',
			sortable: true,
			width: 250,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: intelli.admin.lang.status,
			dataIndex: 'status',
			width: 100,
			sortable: true,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.accounts.statusesStore,
				displayField: 'display',
				valueField: 'value',
				mode: 'local'
			})
		},{
			header: intelli.admin.lang.date,
			dataIndex: 'date',
			sortable: true,
			width: 100
		},{
			header: "",
			width: 40,
			dataIndex: 'sendemail',
			hideable: false,
			menuDisabled: true,
			align: 'center'
		},{
			header: "",
			width: 40,
			dataIndex: 'edit',
			hideable: false,
			menuDisabled: true,
			align: 'center'
		},{
			header: "",
			width: 40,
			dataIndex: 'remove',
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

		this.title = intelli.admin.lang.accounts;
		this.renderTo = 'box_accounts';

		this.setupBaseParams();
		this.setupPagingPanel();
		this.setupGrid();

		this.setRenderers();
		this.setEvents();

		this.grid.autoExpandColumn = 1;

		this.loadData();
	},
	setupPagingPanel: function()
	{
		/*
		 * Top toolbar
		 */
		this.topToolbar = new Ext.Toolbar(
		{
			items:[
			intelli.admin.lang.id + ':',
			{
				xtype: 'numberfield',
				allowDecimals: false,
				allowNegative: false,
				name: 'searchId',
				id: 'searchId',
				emptyText: 'Enter ID',
				style: 'text-align: left'
			},
			intelli.admin.lang.username + ':',
			{
				xtype: 'textfield',
				name: 'searchUsername',
				id: 'searchUsername',
				emptyText: 'Enter username'
			},
			intelli.admin.lang.email + ':',
			{
				xtype: 'textfield',
				name: 'searchEmail',
				id: 'searchEmail',
				emptyText: 'Enter email'
			},
			intelli.admin.lang.status + ':',
			{
				xtype: 'combo',
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.accounts.statusesStoreFilter,
				value: 'all',
				displayField: 'display',
				valueField: 'value',
				mode: 'local',
				id: 'stsFilter'
			},{
				text: intelli.admin.lang.search,
				iconCls: 'search-grid-ico',
				id: 'fltBtn',
				handler: function()
				{
					var id = Ext.getCmp('searchId').getValue();
					var username = Ext.getCmp('searchUsername').getValue();
					var email = Ext.getCmp('searchEmail').getValue();
					var status = Ext.getCmp('stsFilter').getValue();

					if('' != id || '' != username || '' != email || '' != status)
					{
						intelli.accounts.oGrid.dataStore.baseParams =
						{
							action: 'get',
							username: username,
							email: email,
							status: status,
							id: id
						};

						intelli.accounts.oGrid.dataStore.reload();
					}
				}
			},
			'-',
			{
				text: intelli.admin.lang.reset,
				id: 'resetBtn',
				handler: function()
				{
					Ext.getCmp('searchId').reset();
					Ext.getCmp('searchUsername').reset();
					Ext.getCmp('searchEmail').reset();
					Ext.getCmp('stsFilter').setValue('all');

					intelli.accounts.oGrid.dataStore.baseParams =
					{
						action: 'get',
						username: '',
						email: '',
						status: ''
					};

					intelli.accounts.oGrid.dataStore.reload();
				}
			}]
		});

		/*
		 * Bottom toolbar
		 */
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
					store: intelli.accounts.pagingStore,
					value: '20',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'pgnPnl'
				},
				intelli.admin.lang.status + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.accounts.statusesStore,
					value: 'active',
					displayField: 'value',
					valueField: 'display',
					mode: 'local',
					disabled: true,
					id: 'statusCmb'
				},{
					text: intelli.admin.lang['do'],
					disabled: true,
					iconCls: 'go-grid-ico',
					id: 'goBtn',
					handler: function()
					{
						var rows = intelli.accounts.oGrid.grid.getSelectionModel().getSelections();
						var status = Ext.getCmp('statusCmb').getValue();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Ajax.request(
						{
							url: intelli.accounts.vUrl,
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
								intelli.accounts.oGrid.grid.getStore().reload();

								var response = Ext.decode(data.responseText);
								var type = response.error ? 'error' : 'notif';

								intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});
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
						var rows = intelli.accounts.oGrid.grid.getSelectionModel().getSelections();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Msg.show(
						{
							title: intelli.admin.lang.confirm,
							msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_accounts : intelli.admin.lang.are_you_sure_to_delete_this_account,
							buttons: Ext.Msg.YESNO,
							icon: Ext.Msg.QUESTION,
							fn: function(btn)
							{
								if('yes' == btn)
								{
									Ext.Ajax.request(
									{
										url: intelli.accounts.vUrl,
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

											intelli.accounts.oGrid.grid.getStore().reload();

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
			]
		});
	},
	setupBaseParams: function()
	{
		var params = new Object();
		var status = intelli.urlVal('status');
		var search = intelli.urlVal('quick_search');

		params.action = 'get';

		if(null != status)
		{
			params.status = status;
		}

		if(null != search)
		{
			if(intelli.is_int(search))
			{
				params.id = search;
			}
			else if(intelli.is_email(search))
			{
				params.email = search;
			}
			else
			{
				params.username = search;
			}
		}

		this.dataStore.baseParams = params;
	},
	setRenderers: function()
	{
		/* change background color for status field */
		this.columnModel.setRenderer(4, function(value, metadata)
		{
			metadata.css = value;

			return value;
		});
		
		/* add sendemail link */
		this.columnModel.setRenderer(6, function(value, metadata)
		{
			if (value > 0)
			{
				return '<img class="grid_action" alt="' + intelli.admin.lang.resend_confirmation_email + '" title="' + intelli.admin.lang.resend_confirmation_email + '" src="templates/' + intelli.config.admin_tmpl + '/img/icons/email-grid-ico.png" />';
			}	
		});

		/* add edit link */
		this.columnModel.setRenderer(7, function(value, metadata)
		{
			return '<img class="grid_action" alt="'+ intelli.admin.lang.edit +'" title="'+ intelli.admin.lang.edit +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/edit-grid-ico.png" />';
		});

		/* add remove link */
		this.columnModel.setRenderer(8, function(value, metadata)
		{
			return '<img class="grid_action" alt="'+ intelli.admin.lang.remove +'" title="'+ intelli.admin.lang.remove +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/remove-grid-ico.png" />';
		});
	},
	setEvents: function()
	{
		/*
		 * Events
		 */

		/* Edit fields */
		intelli.accounts.oGrid.grid.on('afteredit', function(editEvent)
		{
			Ext.Ajax.request(
			{
				url: intelli.accounts.vUrl,
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
					editEvent.grid.getStore().reload();

					var response = Ext.decode(data.responseText);
					var type = response.error ? 'error' : 'notif';

					intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});
				}
			});
		});

		/* Edit and remove click */
		intelli.accounts.oGrid.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('edit' == fieldName)
			{
				intelli.accounts.oGrid.saveGridState();

				window.location = 'controller.php?file=accounts&do=edit&id='+ record.json.id;
			}
			
			if ('sendemail' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_resend_confirmation,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if ('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.accounts.vUrl,
								method: 'POST',
								params: {
									action: 'sendemail',
									'ids[]': record.id
								},
								failure: function(){
									Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
								},
								success: function(data){
									var response = Ext.decode(data.responseText);
									var type = response.error ? 'error' : 'notif';
									
									intelli.admin.notifBox({
										msg: response.msg,
										type: type,
										autohide: true
									});
									
									Ext.getCmp('statusCmb').disable();
									Ext.getCmp('goBtn').disable();
									Ext.getCmp('removeBtn').disable();
									
									intelli.accounts.oGrid.grid.getStore().reload();
								}
							});
						}
					}
				});
			}

			if('remove' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_this_account,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.accounts.vUrl,
								method: 'POST',
								params:
								{
									action: 'remove',
									'ids[]': record.id
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

									Ext.getCmp('statusCmb').disable();
									Ext.getCmp('goBtn').disable();
									Ext.getCmp('removeBtn').disable();

									intelli.accounts.oGrid.grid.getStore().reload();
								}
							});
						}
					}
				});
			}
		});

		/* Enable disable functionality buttons */
		intelli.accounts.oGrid.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('statusCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('removeBtn').enable();
		});

		intelli.accounts.oGrid.grid.getSelectionModel().on('rowdeselect', function(sm)
		{
			if(0 == sm.getCount())
			{
				Ext.getCmp('statusCmb').disable();
				Ext.getCmp('goBtn').disable();
				Ext.getCmp('removeBtn').disable();
			}
		});

		/* Paging panel event */
		Ext.getCmp('pgnPnl').on('change', function(field, new_value, old_value)
		{
			intelli.accounts.oGrid.grid.getStore().lastOptions.params.limit = new_value;
			intelli.accounts.oGrid.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.accounts.oGrid.grid.getStore().reload();
		});
	}
});

Ext.onReady(function()
{
	if(Ext.get('box_accounts'))
	{
		intelli.accounts.oGrid = new intelli.exGrid({url: intelli.accounts.vUrl});

		/* Initialization grid */
		intelli.accounts.oGrid.init();

		if(intelli.urlVal('status'))
		{
			Ext.getCmp('stsFilter').setValue(intelli.urlVal('status'));
		}

		var search = intelli.urlVal('quick_search');
		
		if(null != search)
		{
			if(intelli.is_int(search))
			{
				Ext.getCmp('searchId').setValue(search);
			}
			else if(intelli.is_email(search))
			{
				Ext.getCmp('searchEmail').setValue(search);
			}
			else
			{
				Ext.getCmp('searchUsername').setValue(search);
			}
		}
	}
});
