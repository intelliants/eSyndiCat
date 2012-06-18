intelli.language = function()
{
	var vUrl = 'controller.php?file=language&grid=phrase&language=' + intelli.urlVal('language');
	var vUrlCompare = 'controller.php?file=language&grid=compare';

	var tempLangStore = new Array();

	var j = 0;
	
	for(var i in intelli.admin.langList)
	{
		tempLangStore[j++] = [i, intelli.admin.langList[i]];
	}

	return {
		oGrid: null,
		oGridCompare: null,
		vUrl: vUrl,
		vUrlCompare: vUrlCompare,
		languageCount: tempLangStore.length,
		categoriesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['admin', 'Administration Board'],['frontend', 'User Frontend'],['common', 'Common']]
		}),
		categoriesStoreSearch: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['all', 'All Categories'],['admin', 'Administration Board'],['frontend', 'User Frontend'],['common', 'Common']]
		}),
		languagesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data: tempLangStore
		}),
		pagingStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['10', '10'],['20', '20'],['30', '30'],['40', '40'],['50', '50']]
		})
	};
}();

/**
 * Phrases grid model
 */
intelli.exGModel = Ext.extend(intelli.gmodel,
{
	constructor: function(config)
	{
		intelli.exGModel.superclass.constructor.apply(this, arguments);
	},
	setupReader: function()
	{
		this.record = Ext.data.Record.create([
			{name: 'key', mapping: 'key'},
			{name: 'value', mapping: 'value'},
			{name: 'lang', mapping: 'lang'},
			{name: 'category', mapping: 'category'},
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
			header: intelli.admin.lang.key, 
			dataIndex: 'key', 
			sortable: true, 
			width: 250,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: intelli.admin.lang.value, 
			dataIndex: 'value', 
			sortable: true, 
			width: 400,
			editor: new Ext.form.TextArea({
				allowBlank: false,
				grow: true
			})
		},{
			header: intelli.admin.lang.language, 
			dataIndex: 'lang',
			width: 100
		},{
			header: intelli.admin.lang.category, 
			dataIndex: 'category',
			sortable: true, 
			width: 100,
			editor:	new Ext.form.ComboBox(
			{
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.language.categoriesStore,
				value: 'admin',
				displayField: 'display',
				valueField: 'value',
				mode: 'local'
			})
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

/**
 * Compare phrases grid model
 */
intelli.exGModelCompare = Ext.extend(intelli.gmodel,
{
	constructor: function(config)
	{
		intelli.exGModelCompare.superclass.constructor.apply(this, arguments);
	},
	setupReader: function()
	{
		var fields = new Array();

		fields.push({name: 'key', mapping: 'key'});

		for(var i in intelli.admin.langList)
		{
			fields.push({name: 'lang_' + i});
		}

		fields.push({name: 'category', mapping: 'category'}, {name: 'remove', mapping: 'remove'});

		this.record = Ext.data.Record.create(fields);

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
		var columns = new Array();

		columns.push(this.checkColumn);

		columns.push({
			header: intelli.admin.lang.key, 
			dataIndex: 'key', 
			sortable: true, 
			width: 200,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		});

		for(var i in intelli.admin.langList)
		{
			columns.push({
				header: intelli.admin.langList[i], 
				dataIndex: 'lang_' + i, 
				sortable: true, 
				width: 250,
				editor: new Ext.form.TextField({
					allowBlank: false
				})
			});
		}

		columns.push({
			header: intelli.admin.lang.category, 
			dataIndex: 'category',
			sortable: true, 
			width: 100,
			editor:	new Ext.form.ComboBox(
			{
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.language.categoriesStore,
				value: 'admin',
				displayField: 'display',
				valueField: 'value',
				mode: 'local'
			})
		},{
			header: "",
			width: 40,
			dataIndex: 'remove',
			hideable: false,
			menuDisabled: true,
			align: 'center'
		});

		this.columnModel = new Ext.grid.ColumnModel(columns);

		return this.columnModel;
	}
});

/**
 * Phrases grid
 */
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

		this.dataStore.setDefaultSort('key');
	},
	init: function()
	{
		this.plugins = new Ext.ux.PanelResizer({
            minHeight: 100
		});

		this.title = intelli.admin.lang.phrase_manager;
		this.renderTo = 'box_phrases';

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
		this.topToolbar = new Ext.Toolbar(
		{
			items:[
				intelli.admin.lang.key + ':',
				{
					xtype: 'textfield',
					id: 'srchKey',
					emptyText: 'Phrase key'
				},
				' ',
				intelli.admin.lang.value + ':',
				{
					xtype: 'textfield',
					id: 'srchValue',
					emptyText: 'Phrase value'
				},
				' ',
				intelli.admin.lang.category + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.language.categoriesStoreSearch,
					value: 'all',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'srchCategory'
				},
				' ',
				intelli.admin.lang.plugin + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: new Ext.data.JsonStore({
						url: intelli.language.vUrl + '&action=getplugins',
						root: 'data',
						fields: ['value', 'display']
					}),
					displayField: 'display',
					valueField: 'value',
					id: 'srchPlugin'
				},
				' ',
				{
					text: intelli.admin.lang.search,
					iconCls: 'search-grid-ico',
					id: 'srchBtn',
					handler: function()
					{
						var searchKeyVal = Ext.getCmp('srchKey').getValue();
						var searchValueVal = Ext.getCmp('srchValue').getValue();
						var searchCategoryVal = Ext.getCmp('srchCategory').getValue();
						var searchPlugin = Ext.getCmp('srchPlugin').getValue();

						if('' != searchKeyVal || '' != searchValueVal || '' != searchCategoryVal || '' != searchPlugin)
						{
							intelli.language.oGrid.dataStore.baseParams = 
							{
								action: 'get',
								key: searchKeyVal,
								value: searchValueVal,
								category: searchCategoryVal,
								filter_plugin: searchPlugin
							};

							if(intelli.language.oGrid.dataStore.lastOptions.params.start > 0)
							{
								intelli.language.oGrid.dataStore.lastOptions.params.start = 0;
							}

							intelli.language.oGrid.dataStore.reload();
						}
					}
				},
				'-',
				{
					text: intelli.admin.lang.reset,
					id: 'resetBtn',
					handler: function()
					{
						Ext.getCmp('srchKey').setValue('');
						Ext.getCmp('srchValue').setValue('');
						Ext.getCmp('srchCategory').reset();
						Ext.getCmp('srchPlugin').setValue('');

						intelli.language.oGrid.dataStore.baseParams = 
						{
							action: 'get',
							key: '',
							value: '',
							category: '',
							filter_plugin: ''
						};

						intelli.language.oGrid.dataStore.reload();
					}
				}
			]
		});

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
					store: intelli.language.pagingStore,
					value: '20',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'pgnPnl'
				},
				'-',
				intelli.admin.lang.category + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.language.categoriesStore,
					value: 'admin',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					disabled: true,
					id: 'categoryCmb'
				},
				{
					text: intelli.admin.lang['do'],
					disabled: true,
					iconCls: 'go-grid-ico',
					id: 'goBtn',
					handler: function()
					{
						var rows = intelli.language.oGrid.grid.getSelectionModel().getSelections();
						var category = Ext.getCmp('categoryCmb').getValue();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Ajax.request(
						{
							url: intelli.language.vUrl,
							method: 'POST',
							params:
							{
								action: 'update',
								'ids[]': ids,
								field: 'category',
								value: category
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

								intelli.language.oGrid.grid.getStore().reload();
							}
						});
					}
				},
				'-',
				{
					text: intelli.admin.lang.remove,
					disabled: true,
					iconCls: 'remove-grid-ico',
					id: 'removeBtn',
					handler: function()
					{
						var rows = intelli.language.oGrid.grid.getSelectionModel().getSelections();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].json.id;
						}

						Ext.Msg.show(
						{
							title: intelli.admin.lang.confirm,
							msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_phrases : intelli.admin.lang.are_you_sure_to_delete_selected_phrase,
							buttons: Ext.Msg.YESNO,
							icon: Ext.Msg.QUESTION,
							fn: function(btn)
							{
								if('yes' == btn)
								{
									Ext.Ajax.request(
									{
										url: intelli.language.vUrl,
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

											intelli.language.oGrid.grid.getStore().reload();

											Ext.getCmp('categoryCmb').disable();
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
		this.dataStore.baseParams = {action: 'get'};
	},
	setRenderers: function()
	{
		/* add remove link */
		this.columnModel.setRenderer(5, function(value, metadata)
		{
			return '<img class="grid_action" alt="'+ intelli.admin.lang.remove +'" title="'+ intelli.admin.lang.remove +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/remove-grid-ico.png" />';
		});
	},
	setEvents: function()
	{
		/*
		 * Events
		 */

		/* activate actions button */
		intelli.language.oGrid.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('categoryCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('removeBtn').enable();
		});

		/* deactivate actions button */
		intelli.language.oGrid.grid.getSelectionModel().on('rowdeselect', function(sm)
		{
			if(0 == sm.getCount())
			{
				Ext.getCmp('categoryCmb').disable();
				Ext.getCmp('goBtn').disable();
				Ext.getCmp('removeBtn').disable();
			}
		});

		/* Edit fields */
		intelli.language.oGrid.grid.on('afteredit', function(editEvent)
		{
			var rows = intelli.language.oGrid.grid.getSelectionModel().getSelections();
			var ids = new Array();

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
			}

			Ext.Ajax.request(
			{
				url: intelli.language.vUrl,
				method: 'POST',
				params:
				{
					action: 'update',
					'ids[]': ids,
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
						
					intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});

					intelli.language.oGrid.grid.getStore().reload();
				}
			});
		});

		/* Remove button event */
		intelli.language.oGrid.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('remove' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_selected_phrase,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.language.vUrl,
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

									Ext.getCmp('categoryCmb').disable();
									Ext.getCmp('goBtn').disable();
									Ext.getCmp('removeBtn').disable();

									intelli.language.oGrid.grid.getStore().reload();
								}
							});
						}
					}
				});
			}
		});

		/* Paging panel event */
		Ext.getCmp('pgnPnl').on('change', function(field, new_value, old_value)
		{
			intelli.language.oGrid.grid.getStore().lastOptions.params.limit = new_value;
			intelli.language.oGrid.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.language.oGrid.grid.getStore().reload();
		});
	}
});

/**
 * Compare grid 
 */
intelli.exGridCompare = Ext.extend(intelli.grid,
{
	model: null,
	constructor: function(config)
	{
		intelli.exGridCompare.superclass.constructor.apply(this, arguments);

		this.model = new intelli.exGModelCompare({url: config.url});
			
		this.dataStore = this.model.setupDataStore();
		this.columnModel = this.model.setupColumnModel();
		this.selectionModel = this.model.setupSelectionModel();

		this.dataStore.setDefaultSort('key');
	},
	init: function()
	{
		this.plugins = new Ext.ux.PanelResizer({
            minHeight: 100
		});

		this.title = intelli.admin.lang.phrase_manager;
		this.renderTo = 'box_compare';

		this.setupBaseParams();
		this.setupPagingPanel();
		this.setupGrid();

		this.setRenderers();
		this.setEvents();

		//this.grid.autoExpandColumn = 2;

		this.loadData();
	},
	setupPagingPanel: function()
	{
		this.topToolbar = new Ext.Toolbar(
		{
			items:[
				intelli.admin.lang.key + ':',
				{
					xtype: 'textfield',
					id: 'srchKey',
					emptyText: 'Phrase key'
				},
				' ',
				intelli.admin.lang.value + ':',
				{
					xtype: 'textfield',
					id: 'srchValue',
					emptyText: 'Phrase value'
				},
				' ',
				intelli.admin.lang.category + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.language.categoriesStoreSearch,
					value: 'all',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'srchCategory'
				},
				' ',
				intelli.admin.lang.plugin + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: new Ext.data.JsonStore({
						url: intelli.language.vUrl + '&action=getplugins',
						root: 'data',
						fields: ['value', 'display']
					}),
					displayField: 'display',
					valueField: 'value',
					id: 'srchPlugin'
				},
				' ',
				{
					text: intelli.admin.lang.search,
					iconCls: 'search-grid-ico',
					id: 'srchBtn'
				},
				'-',
				{
					text: intelli.admin.lang.reset,
					id: 'resetBtn'
				}
			]
		});

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
					store: intelli.language.pagingStore,
					value: '10',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'pgnPnl'
				},
				'-',
				intelli.admin.lang.category + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.language.categoriesStore,
					value: 'admin',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					disabled: true,
					id: 'categoryCmb'
				},
				{
					text: intelli.admin.lang['do'],
					disabled: true,
					iconCls: 'go-grid-ico',
					id: 'goBtn'
				},
				'-',
				{
					text: intelli.admin.lang.remove,
					disabled: true,
					iconCls: 'remove-grid-ico',
					id: 'removeBtn'
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
		/* add remove link */
		var colNumber = intelli.language.languageCount + 3;

		this.columnModel.setRenderer(colNumber, function(value, metadata)
		{
			return '<img class="grid_action" alt="'+ intelli.admin.lang.remove +'" title="'+ intelli.admin.lang.remove +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/remove-grid-ico.png" />';
		});
	},
	setEvents: function()
	{
		/*
		 * Events
		 */

		/* activate actions button */
		intelli.language.oGridCompare.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('categoryCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('removeBtn').enable();
		});

		/* deactivate actions button */
		intelli.language.oGridCompare.grid.getSelectionModel().on('rowdeselect', function(sm)
		{
			if(0 == sm.getCount())
			{
				Ext.getCmp('categoryCmb').disable();
				Ext.getCmp('goBtn').disable();
				Ext.getCmp('removeBtn').disable();
			}
		});

		/* go button action */
		Ext.getCmp('goBtn').on('click', function()
		{
			var rows = intelli.language.oGridCompare.grid.getSelectionModel().getSelections();
			var category = Ext.getCmp('categoryCmb').getValue();
			var ids = new Array();

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
			}

			Ext.Ajax.request(
			{
				url: intelli.language.vUrl,
				method: 'POST',
				params:
				{
					action: 'update',
					'ids[]': ids,
					field: 'category',
					value: category
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

					intelli.language.oGrid.grid.getStore().reload();
				}
			});
		});

		/* remove button action */
		Ext.getCmp('removeBtn').on('click', function()
		{
			var rows = intelli.language.oGridCompare.grid.getSelectionModel().getSelections();
			var ids = new Array();

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
			}

			Ext.Msg.show(
			{
				title: intelli.admin.lang.confirm,
				msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_phrases : intelli.admin.lang.are_you_sure_to_delete_selected_phrase,
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function(btn)
				{
					if('yes' == btn)
					{
						Ext.Ajax.request(
						{
							url: intelli.language.vUrl,
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

								intelli.language.oGridCompare.grid.getStore().reload();

								Ext.getCmp('categoryCmb').disable();
								Ext.getCmp('goBtn').disable();
								Ext.getCmp('removeBtn').disable();
							}
						});
					}
				}
			});
		});

		/* Edit fields */
		intelli.language.oGridCompare.grid.on('afteredit', function(editEvent)
		{
			var rows = intelli.language.oGridCompare.grid.getSelectionModel().getSelections();
			var ids = new Array();
			var field = '';
			var value = '';
			var lang = '';
			var key = editEvent.record.json.key;

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
			}

			if(intelli.inArray(editEvent.field, ['key', 'category']))
			{
				field = editEvent.field;
			}
			else
			{
				field = 'value';
				lang = editEvent.field.replace('lang_', '');
			}
			

			Ext.Ajax.request(
			{
				url: intelli.language.vUrl,
				method: 'POST',
				params:
				{
					action: 'update',
					field: field,
					value: editEvent.value,
					lang: lang,
					key: key
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

					intelli.language.oGridCompare.grid.getStore().reload();
				}
			});
		});

		/* Remove button event */
		intelli.language.oGridCompare.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('remove' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_selected_phrase,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.language.vUrl,
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

									Ext.getCmp('categoryCmb').disable();
									Ext.getCmp('goBtn').disable();
									Ext.getCmp('removeBtn').disable();

									intelli.language.oGridCompare.grid.getStore().reload();
								}
							});
						}
					}
				});
			}
		});

		/* Search button */
		Ext.getCmp('srchBtn').on('click', function()
		{
			var searchKeyVal = Ext.getCmp('srchKey').getValue();
			var searchValueVal = Ext.getCmp('srchValue').getValue();
			var searchCategoryVal = Ext.getCmp('srchCategory').getValue();
			var searchPlugin = Ext.getCmp('srchPlugin').getValue();

			if('' != searchKeyVal || '' != searchValueVal || '' != searchCategoryVal || '' != searchPlugin)
			{
				intelli.language.oGridCompare.dataStore.baseParams = 
				{
					action: 'get',
					key: searchKeyVal,
					value: searchValueVal,
					category: searchCategoryVal,
					filter_plugin: searchPlugin
				};

				intelli.language.oGridCompare.dataStore.reload();
			}
		});

		/* Reset search form */
		Ext.getCmp('resetBtn').on('click', function()
		{
			Ext.getCmp('srchKey').setValue('');
			Ext.getCmp('srchValue').setValue('');
			Ext.getCmp('srchCategory').reset();
			Ext.getCmp('srchPlugin').setValue('');

			intelli.language.oGridCompare.dataStore.baseParams = 
			{
				action: 'get',
				key: '',
				value: '',
				category: '',
				filter_plugin: ''
			};

			intelli.language.oGridCompare.dataStore.reload();
		});

		/* Paging panel event */
		Ext.getCmp('pgnPnl').on('change', function(field, new_value, old_value)
		{
			intelli.language.oGridCompare.grid.getStore().lastOptions.params.limit = new_value;
			intelli.language.oGridCompare.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.language.oGridCompare.grid.getStore().reload();
		});
	}
});

Ext.onReady(function()
{
	var addPhrasePanel = new Ext.FormPanel(
	{
		frame: true,
		title: intelli.admin.lang.add_phrase,
		bodyStyle: 'padding: 5px 5px 0',
		renderTo: 'box_add_phrase',
		id: 'add_phrase_panel',
		hidden: true,
		items: [
		{
			fieldLabel: intelli.admin.lang.key,
			name: 'key',
			xtype: 'textfield',
			allowBlank: false,
			anchor: '40%'
		},{
			fieldLabel: intelli.admin.lang.value,
			name: 'value',
			xtype: 'textarea',
			allowBlank: false,
			width: '99%'
		},{
			fieldLabel: intelli.admin.lang.language,
			name: 'language',
			hiddenName: 'language',
			xtype: 'combo',
			allowBlank: false,
			editable: false,
			triggerAction: 'all',
			lazyRender: true,
			value: 'en',
			store: intelli.language.languagesStore,
			displayField: 'display',
			valueField: 'value',
			mode: 'local'
		},{
			fieldLabel: intelli.admin.lang.category,
			hiddenName: 'category',
			xtype: 'combo',
			allowBlank: false,
			editable: false,
			triggerAction: 'all',
			lazyRender: true,
			value: 'admin',
			store: intelli.language.categoriesStore,
			displayField: 'display',
			valueField: 'value',
			mode: 'local',
			listWidth: 167
		}],
		tools: [{
			id: 'minimize',
			handler: function(event, tool, panel)
			{
				panel.collapse();
			}
		},{
			id: 'maximize',
			handler: function(event, tool, panel)
			{
				panel.expand();
			}
		},{
			id: 'close',
			handler: function(event, tool, panel)
			{
				panel.hide();
			}
		}],
		buttons: [
		{
			text: intelli.admin.lang.add,
			handler: function()
			{
				addPhrasePanel.getForm().submit(
				{
					url: intelli.language.vUrl + '&action=add_phrase',
					method: 'POST',
					params:
					{
						prevent_csrf: $("#box_add_phrase input[name='prevent_csrf']").val()
					},
					success: function(form, action)
					{
						Ext.Msg.show(
						{
							title: intelli.admin.lang.add_new_phrase,
							msg: intelli.admin.lang.add_one_more_phrase,
							buttons: Ext.Msg.YESNO,
							fn: function(btn)
							{
								if('no' == btn)
								{
									addPhrasePanel.hide();
								}

								var response = action.result;
								var type = response.error ? 'error' : 'notif';
						
								intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});

								form.reset();
							},
							icon: Ext.MessageBox.QUESTION
						});
					}
				});
			}
		},{
			text: intelli.admin.lang.cancel,
			handler: function()
			{
				addPhrasePanel.hide();
			}
		}]
	});

	if(Ext.get('box_phrases'))
	{
		intelli.language.oGrid = new intelli.exGrid({url: intelli.language.vUrl});

		/* Initialization grid */
		intelli.language.oGrid.init();
	}

	if(Ext.get('box_compare'))
	{
		intelli.language.oGridCompare = new intelli.exGridCompare({url: intelli.language.vUrlCompare});

		/* Initialization grid */
		intelli.language.oGridCompare.init();
	}

	$("#add_phrase").click(function()
	{
		Ext.getCmp('add_phrase_panel').show();

		return false;
	});

	$("a.delete_language").each(function()
	{
		$(this).click(function()
		{
			var link = $(this);

			Ext.Msg.show(
			{
				title: intelli.admin.lang.confirm,
				msg: intelli.admin.lang.are_you_sure_to_delete_selected_language,
				buttons: Ext.Msg.YESNO,
				fn: function(btn)
				{
					if('yes' == btn)
					{
						window.location = link.attr("href");
					}
				},
				icon: Ext.MessageBox.QUESTION
			});

			return false;
		});
	});
});
