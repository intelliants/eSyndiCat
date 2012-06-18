intelli.categories = function()
{
	var vUrl = 'controller.php?file=categories';

	return {
		oGrid: null,
		vUrl: vUrl,
		statusesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['active', 'active'],
				['approval', 'approval']
			]
		}),
		statusesStoreFilter: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['all', intelli.admin.lang._status_],
				['active', intelli.admin.lang.active],
				['approval', intelli.admin.lang.approval]
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
			{name: 'title', mapping: 'title'},
			{name: 'path', mapping: 'path'},
			{name: 'status', mapping: 'status'},
			{name: 'order', mapping: 'order'},
			{name: 'clicks', mapping: 'clicks'},
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
			header: intelli.admin.lang.title, 
			dataIndex: 'title', 
			sortable: true,
			width: 350
		},{
			header: intelli.admin.lang.path, 
			dataIndex: 'path', 
			sortable: true,
			width: 250
		},{
			header: intelli.admin.lang.status, 
			dataIndex: 'status',
			sortable: true,
			width: 100,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.categories.statusesStore,
				displayField: 'display',
				valueField: 'value',
				mode: 'local'
			})
		},{
			header: intelli.admin.lang.order, 
			dataIndex: 'order',
			sortable: true,
			width: 60,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: intelli.admin.lang.clicks, 
			dataIndex: 'clicks',
			sortable: true,
			width: 60
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

		this.dataStore.setDefaultSort('title');
	},
	init: function()
	{
		this.plugins = new Ext.ux.PanelResizer({
            minHeight: 100
		});

		this.title = intelli.admin.lang.categories;
		this.renderTo = 'box_categories';

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
		/*
		 * Top toolbar
		 */
		this.topToolbar = new Ext.Toolbar(
		{
			items:[
				intelli.admin.lang.title + ':',
				{
					xtype: 'textfield',
					id: 'searchTitle',
					emptyText: 'Title'
				},
				' ',
				intelli.admin.lang.status + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.categories.statusesStoreFilter,
					value: 'all',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'stsFilter'
				},
				{
					text: intelli.admin.lang.search,
					iconCls: 'search-grid-ico',
					id: 'fltBtn',
					handler: function()
					{
						var status = Ext.getCmp('stsFilter').getValue();
						var title = Ext.getCmp('searchTitle').getValue();

						if('all' != status || '' != title)
						{
							intelli.categories.oGrid.dataStore.baseParams = 
							{
								action: 'get',
								status: status,
								title: title
							};

							intelli.categories.oGrid.dataStore.reload();
						}
					}
				},
				'-',
				{
					text: intelli.admin.lang.reset,
					id: 'resetBtn',
					handler: function()
					{
						//Ext.getCmp('stsFilter').reset();
						Ext.getCmp('stsFilter').setValue('all');
						Ext.getCmp('searchTitle').reset();

						intelli.categories.oGrid.dataStore.baseParams = 
						{
							action: 'get',
							status: '',
							title: ''
						};

						intelli.categories.oGrid.dataStore.reload();
					}
				}
			]
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
					store: intelli.categories.pagingStore,
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
					store: intelli.categories.statusesStore,
					width: 80,
					value: 'active',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					disabled: true,
					id: 'statusCmb'
				},
				{
					text: intelli.admin.lang['do'],
					disabled: true,
					iconCls: 'go-grid-ico',
					id: 'goBtn',
					handler: function()
					{
						var rows = intelli.categories.oGrid.grid.getSelectionModel().getSelections();
						var status = Ext.getCmp('statusCmb').getValue();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Ajax.request(
						{
							url: intelli.categories.vUrl,
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
								intelli.categories.oGrid.grid.getStore().reload();

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
						var rows = intelli.categories.oGrid.grid.getSelectionModel().getSelections();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Msg.show(
						{
							title: intelli.admin.lang.confirm,
							msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_categories : intelli.admin.lang.are_you_sure_to_delete_this_category,
							buttons: Ext.Msg.YESNO,
							icon: Ext.Msg.QUESTION,
							fn: function(btn)
							{
								if('yes' == btn)
								{
									Ext.Ajax.request(
									{
										url: intelli.categories.vUrl,
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

											intelli.categories.oGrid.grid.getStore().reload();

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
		var quick_search = intelli.urlVal('quick_search');

		params.action = 'get';

		if(null != status)
		{
			params.status = status;
		}

		if(null != quick_search)
		{
			params.title = quick_search;
		}

		this.dataStore.baseParams = params;
	},
	setRenderers: function()
	{
		/* change background color for status field */
		this.columnModel.setRenderer(3, function(value, metadata)
		{
			metadata.css = value;

			return value;
		});

		/* add edit link */
		this.columnModel.setRenderer(6, function(value, metadata)
		{
			return '<img class="grid_action" alt="'+ intelli.admin.lang.edit +'" title="'+ intelli.admin.lang.edit +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/edit-grid-ico.png" />';
		});

		/* add remove link */
		this.columnModel.setRenderer(7, function(value, metadata)
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
		intelli.categories.oGrid.grid.on('afteredit', function(editEvent)
		{
			Ext.Ajax.request(
			{
				url: intelli.categories.vUrl,
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
		intelli.categories.oGrid.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('edit' == fieldName)
			{
				intelli.categories.oGrid.saveGridState();

				window.location = 'controller.php?file=suggest-category&do=edit&id='+ record.json.id;
			}

			if('remove' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_this_category,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.categories.vUrl,
								method: 'POST',
								params:
								{
									action: 'remove',
									'ids[]': record.json.id
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

									grid.getStore().reload();
								}
							});
						}
					}
				});
			}
		});

		/* Enable disable functionality buttons */
		intelli.categories.oGrid.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('statusCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('removeBtn').enable();
		});

		intelli.categories.oGrid.grid.getSelectionModel().on('rowdeselect', function(sm)
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
			intelli.categories.oGrid.grid.getStore().lastOptions.params.limit = new_value;
			intelli.categories.oGrid.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.categories.oGrid.grid.getStore().reload();
		});
	}
});

Ext.onReady(function()
{
	if(Ext.get('box_categories'))
	{
		intelli.categories.oGrid = new intelli.exGrid({url: intelli.categories.vUrl});

		/* Initialization grid */
		intelli.categories.oGrid.init();

		if(intelli.urlVal('status'))
		{
			Ext.getCmp('stsFilter').setValue(intelli.urlVal('status'));
		}

		if(intelli.urlVal('quick_search'))
		{
			Ext.getCmp('searchTitle').setValue(intelli.urlVal('quick_search'));
		}
	}
});

