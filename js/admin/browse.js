intelli.browse = function()
{
	var vUrl = 'controller.php?file=browse';

	return {
		oGrid: null,
		vUrl: vUrl,
		vWindow: null,
		vTree: null,
		vWindowM: null,
		vTreeM: null,
		statusesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['active', 'active'],
				['approval', 'approval'],
				['banned', 'banned'],
				['suspended', 'suspended']
			]
		}),
		statusesStoreFilter: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['all', intelli.admin.lang._status_],
				['active', intelli.admin.lang.active],
				['approval', intelli.admin.lang.approval],
				['banned', intelli.admin.lang.banned],
				['suspended', intelli.admin.lang.suspended]
			]
		}),
		pagingStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['10', '10'],['20', '20'],['30', '30'],['40', '40'],['50', '50']]
		}),
		statesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['all', intelli.admin.lang._check_status_],
				['destvalid', intelli.admin.lang.destination_valid],
				['destbroken', intelli.admin.lang.destination_broken],
				['recipbroken', intelli.admin.lang.reciprocal_broken],
				['recipvalid', intelli.admin.lang.reciprocal_valid]
			]
		}),
		typesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['all', intelli.admin.lang._type_],
				['featured', intelli.admin.lang.featured],
				['sponsored', intelli.admin.lang.sponsored],
				['partner', intelli.admin.lang.partner],
				['regular', intelli.admin.lang.regular]
			]
		}),
		actionsStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['', intelli.admin.lang._action_],
				['check_broken', intelli.admin.lang.check],
				['update_pagerank', intelli.admin.lang.update_pagerank],
				['recip_recheck', intelli.admin.lang.recip_recheck],
				['copy', intelli.admin.lang.copy],
				['cross', intelli.admin.lang.cross],
				['move', intelli.admin.lang.move]
			]
		}) 
	};
}();

var expander = new Ext.grid.RowExpander({
	tpl : new Ext.Template(
		'<p><b>' + intelli.admin.lang.category + ':</b> {category}<br />',
		'<b>' + intelli.admin.lang.description + ':</b> {description}</p>'
	)
});

var account_ds = new Ext.data.Store(
{
	proxy: new Ext.data.HttpProxy({url: intelli.browse.vUrl + '&action=getaccounts', method: 'GET'}),
	reader: new Ext.data.JsonReader(
	{
		root: 'data',
		totalProperty: 'total'
	}, [
		{name: 'id', mapping: 'id'},
		{name: 'username', mapping: 'username'}
	])
});

var resultTpl = new Ext.XTemplate(
	'<tpl for="."><div class="search-item" style="padding: 3px;">',
		'<h4>{username}</h4>',
	'</div></tpl>'
);

var account_search = new Ext.form.ComboBox(
{
	store: account_ds,
	displayField: 'username',
	valueField: 'id',
	allowBlank: true,
	minChars: 1,
	typeAhead: false,
	loadingText: 'Searching...',
	hiddenName: 'listing',
	pageSize: 10,
	hideTrigger: true,
	tpl: resultTpl,
	itemSelector: 'div.search-item'
});

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
			{name: 'account_id', mapping: 'username'},
			{name: 'category', mapping: 'category'},
			{name: 'status', mapping: 'status'},
			{name: 'date', mapping: 'date'},
			{name: 'description'},
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
		expander,
		{
			header: intelli.admin.lang.title, 
			dataIndex: 'title', 
			sortable: true, 
			renderer: function(value, p, record)
			{
				return String.format('<b><a href="{0}" target="_blank">{1}</a></b>', record.json.url, value);
			},
			width: 250,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: intelli.admin.lang.account, 
			dataIndex: 'account_id', 
			sortable: true, 
			width: 120,
			editor: account_search
		},{
			header: intelli.admin.lang.category, 
			dataIndex: 'category', 
			width: 250
		},{
			header: intelli.admin.lang.status, 
			dataIndex: 'status',
			width: 100,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.browse.statusesStore,
				displayField: 'value',
				valueField: 'display',
				mode: 'local'
			})
		},{
			header: intelli.admin.lang.date, 
			dataIndex: 'date',
			sortable: true, 
			width: 130,
			editor: new Ext.form.DateField(
			{
				format: 'Y-m-d H:i:s',
				xtype: 'datefield',
				allowBlank: false
			})
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
	},
	init: function()
	{
		this.plugins = [new Ext.ux.PanelResizer({
            minHeight: 100
		}), expander];

		this.title = intelli.admin.lang.listings;
		this.renderTo = 'box_listings';

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
				intelli.admin.lang.status + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.browse.statusesStoreFilter,
					value: 'all',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'stsFilter'
				},
				' ',
				intelli.admin.lang.type + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.browse.typesStore,
					value: 'all',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'tpFilter'
				},
				' ',
				intelli.admin.lang.state + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.browse.statesStore,
					value: 'all',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					id: 'stFilter'
				},
				{
					text: intelli.admin.lang.filter,
					iconCls: 'search-grid-ico',
					id: 'fltBtn',
					handler: function()
					{
						var status = Ext.getCmp('stsFilter').getValue();
						var type = Ext.getCmp('tpFilter').getValue();
						var state = Ext.getCmp('stFilter').getValue();

						if('' != status || '' != type || '' != state)
						{
							intelli.browse.oGrid.dataStore.baseParams = 
							{
								action: 'get',
								status: status,
								type: type,
								state: state
							};

							intelli.browse.oGrid.dataStore.reload();
						}
					}
				},
				'-',
				{
					text: intelli.admin.lang.reset,
					id: 'resetBtn',
					handler: function()
					{
						Ext.getCmp('stsFilter').reset();
						Ext.getCmp('tpFilter').reset();
						Ext.getCmp('stFilter').reset();

						intelli.browse.oGrid.dataStore.baseParams = 
						{
							action: 'get',
							status: '',
							type: '',
							state: ''
						};

						intelli.browse.oGrid.dataStore.reload();
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
					store: intelli.browse.pagingStore,
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
					store: intelli.browse.statusesStore,
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
					id: 'goBtn'
				},
				'-',
				{
					text: intelli.admin.lang.remove,
					id: 'removeBtn',
					iconCls: 'remove-grid-ico',
					disabled: true
				},
				'-',
				intelli.admin.lang.action + ':',
				{
					xtype: 'combo',
					typeAhead: true,
					triggerAction: 'all',
					editable: false,
					lazyRender: true,
					store: intelli.browse.actionsStore,
					width: 120,
					value: '',
					displayField: 'display',
					valueField: 'value',
					mode: 'local',
					disabled: true,
					id: 'actCmb'
				},
				{
					text: intelli.admin.lang['do'],
					disabled: true,
					iconCls: 'go-grid-ico',
					id: 'actBtn'
				}
			]
		});
	},
	setupBaseParams: function()
	{
		this.dataStore.baseParams = {action: 'get', category: intelli.urlVal('id')};
	},
	setRenderers: function()
	{
		/* change background color for status field */
		this.columnModel.setRenderer(5, function(value, metadata)
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
			return '<img class="grid_action" alt="'+ intelli.admin.lang.remove +'" title="'+ intelli.admin.lang.remove +'" src="templates/'+ intelli.config.admin_tmpl +'/img/icons/remove-grid-ico.png" />';
		});
	},
	setEvents: function()
	{
		/* 
		 * Events
		 */

		/* Edit fields */
		intelli.browse.oGrid.grid.on('afteredit', function(editEvent)
		{
			var value = 'date' == editEvent.field ? editEvent.value.format("Y-m-d H:i:s") : editEvent.value;

			Ext.Ajax.request(
			{
				url: intelli.browse.vUrl,
				method: 'POST',
				params:
				{
					action: 'update',
					'ids[]': editEvent.record.id,
					field: editEvent.field,
					value: value
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
		intelli.browse.oGrid.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('edit' == fieldName)
			{
				intelli.browse.oGrid.saveGridState();

				window.location = 'controller.php?file=suggest-listing&do=edit&id='+ record.json.id;
			}

			if('remove' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_this_listing,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							var reasonWindow = new Ext.Window(
							{
								title: intelli.admin.lang.remove_reason,
								width : 550,
								height : 220,
								contentEl : 'remove_reason',
								modal: true,
								bodyStyle: 
								{
									padding: '5px'
								},
								closeAction : 'hide',
								listeners:
								{
									'afterrender': function(cmp)
									{
										$("#remove_reason").css("display", "block");
									}
								},
								buttons: [
								{
									text : intelli.admin.lang.ok,
									handler: function()
									{
										var reason_text = $("#remove_reason_text").val();

										$("#remove_reason_text").val('');

										Ext.Ajax.request(
										{
											url: intelli.browse.vUrl,
											method: 'POST',
											params:
											{
												action: 'remove',
												'ids[]': record.json.id,
												reason: reason_text
											},
											failure: function()
											{
												Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
											},
											success: function(data)
											{
												var response = Ext.decode(data.responseText);
												var type = response.error ? 'error' : 'notif';
												
												Ext.getCmp('statusCmb').disable();
												Ext.getCmp('goBtn').disable();
												Ext.getCmp('actBtn').disable();
												Ext.getCmp('actCmb').disable();
												Ext.getCmp('removeBtn').disable();

												intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});

												grid.getStore().reload();
											}
										});

										reasonWindow.hide();
									}
								}]
							});

							reasonWindow.show();
						}
					}
				});
			}
		});

		/* Enable disable functionality buttons */
		intelli.browse.oGrid.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('statusCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('actBtn').enable();
			Ext.getCmp('actCmb').enable();
			Ext.getCmp('removeBtn').enable();
		});

		intelli.browse.oGrid.grid.getSelectionModel().on('rowdeselect', function(sm)
		{
			if(0 == sm.getCount())
			{
				Ext.getCmp('statusCmb').disable();
				Ext.getCmp('goBtn').disable();
				Ext.getCmp('actBtn').disable();
				Ext.getCmp('actCmb').disable();
				Ext.getCmp('removeBtn').disable();
			}
		});

		/* Go button action */
		Ext.getCmp('goBtn').on('click', function()
		{
			var rows = intelli.browse.oGrid.grid.getSelectionModel().getSelections();
			var status = Ext.getCmp('statusCmb').getValue();
			var ids = new Array();

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
			}

			Ext.Ajax.request(
			{
				url: intelli.browse.vUrl,
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
					intelli.browse.oGrid.grid.getStore().reload();

					var response = Ext.decode(data.responseText);
					var type = response.error ? 'error' : 'notif';
						
					intelli.admin.notifBox({msg: response.msg, type: type, autohide: true});
				}
			});
		});

		/* remove button action */
		Ext.getCmp('removeBtn').on('click', function()
		{
			var rows = intelli.browse.oGrid.grid.getSelectionModel().getSelections();
			var ids = new Array();

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
			}

			Ext.Msg.show(
			{
				title: intelli.admin.lang.confirm,
				msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_listings : intelli.admin.lang.are_you_sure_to_delete_this_listing,
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function(btn)
				{
					if('yes' == btn)
					{
						var reasonWindow = new Ext.Window(
						{
							title: intelli.admin.lang.remove_reason,
							width : 550,
							height : 220,
							contentEl : 'remove_reason',
							modal: true,
							bodyStyle: 
							{
								padding: '5px'
							},
							closeAction : 'hide',
							listeners:
							{
								'afterrender': function(cmp)
								{
									$("#remove_reason").css("display", "block");
								}
							},
							buttons: [
							{
								text : intelli.admin.lang.ok,
								handler: function()
								{
									var reason_text = $("#remove_reason_text").val();

									$("#remove_reason_text").val('');

									Ext.Ajax.request(
									{
										url: intelli.browse.vUrl,
										method: 'POST',
										params:
										{
											action: 'remove',
											'ids[]': ids,
											reason: reason_text
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
											Ext.getCmp('actBtn').disable();
											Ext.getCmp('actCmb').disable();
											Ext.getCmp('removeBtn').disable();

											intelli.browse.oGrid.grid.getStore().reload();
										}
									});

									reasonWindow.hide();
								}
							}]
						});

						reasonWindow.show();
					}
				}
			});
		});

		/* Paging panel event */
		Ext.getCmp('pgnPnl').on('change', function(field, new_value, old_value)
		{
			intelli.browse.oGrid.grid.getStore().lastOptions.params.limit = new_value;
			intelli.browse.oGrid.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.browse.oGrid.grid.getStore().reload();
		});

		/* Do action event */
		Ext.getCmp('actBtn').on('click', function()
		{
			var action = Ext.getCmp('actCmb').getValue();
			var rows = intelli.browse.oGrid.grid.getSelectionModel().getSelections();
			var ids = new Array();
			var listings = new Array();
			var msg = '';

			for(var i = 0; i < rows.length; i++)
			{
				ids[i] = rows[i].json.id;
				listings[i] = rows[i].json.title;
			}

			if(intelli.inArray(action, ['copy', 'cross', 'move']))
			{
				intelli.browse.vTree = new Ext.tree.TreePanel({
					animate: true, 
					autoScroll: true,
					width: 'auto',
					height: 'auto',
					border: false,
					plain: true,
					loader: new Ext.tree.TreeLoader(
					{
						dataUrl: 'get-categories.php',
						baseParams: {single: 1},
						requestMethod: 'GET'
					}),
					containerScroll: true
				});
			
				// add a tree sorter in folder mode
				new Ext.tree.TreeSorter(intelli.browse.vTree, {folderSort: true});
				 
				// set the root node
				var root = new Ext.tree.AsyncTreeNode({
					text: 'ROOT', 
					id: '0'
				});
				intelli.browse.vTree.setRootNode(root);
					
				root.expand();

				intelli.browse.vWindow = new Ext.Window(
				{
					title: intelli.admin.lang.tree,
					width : 400,
					height : 450,
					modal: true,
					autoScroll: true,
					closeAction : 'hide',
					items: [intelli.browse.vTree],
					buttons: [
					{
						text : intelli.admin.lang.ok,
						handler: function()
						{
							var category = intelli.browse.vTree.getSelectionModel().getSelectedNode();

							var msg = intelli.admin.lang.action_confirm;

							msg = msg.replace("{action}", action);
							msg = msg.replace("{listings}", listings.join(', '));
							msg = msg.replace("{category}", category.text);

							Ext.Msg.show(
							{
								title: intelli.admin.lang.confirm,
								msg: msg,
								buttons: Ext.Msg.YESNO,
								icon: Ext.Msg.QUESTION,
								fn: function(btn)
								{
									if('yes' == btn)
									{
										Ext.Ajax.request(
										{
											url: intelli.browse.vUrl,
											method: 'POST',
											params:
											{
												action: action,
												'ids[]': ids,
												category: category.id
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

												intelli.browse.oGrid.grid.getStore().reload();
												intelli.browse.vWindow.hide();
											}
										});
									}
								}
							});
						}
					},{
						text : intelli.admin.lang.cancel,
						handler : function()
						{
							intelli.browse.vWindow.hide();
						}
					}]
				});

				intelli.browse.vWindow.show();
			}
			else
			{
				Ext.Ajax.request(
				{
					url: intelli.browse.vUrl,
					method: 'POST',
					params:
					{
						action: action,
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
					}
				});
			}
		});
	}
});

Ext.onReady(function()
{
	if(Ext.get('box_listings'))
	{
		intelli.browse.oGrid = new intelli.exGrid({url: intelli.browse.vUrl});

		/* Initialization grid */
		intelli.browse.oGrid.init();
	}

	/**
	 * Event action for delete category icon 
	 */
	$("#delete_category").click(function()
	{
		var delete_icon = $(this);

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
					window.location = delete_icon.attr("href");
				}
			}
		});
		
		return false;
	});

	/**
	 * Event actions for move and copy icons 
	 */
	$("a[class*='actions']").each(function()
	{
		$(this).click(function()
		{
			var params = $(this).attr("class").split("_");
			var msg = '';

			if(intelli.inArray(params[1], ['catcopy', 'catmove']))
			{
				intelli.browse.vTree = new Ext.tree.TreePanel({
					animate: true, 
					width: 'auto',
					height: 'auto',
					border: false,
					plain: true,
					loader: new Ext.tree.TreeLoader(
					{
						dataUrl: 'get-categories.php',
						baseParams:
						{
							single: ('catcopy' == params[1]) ? 0 : 1
						},
						requestMethod: 'GET'
					}),
					containerScroll: true
				});
			
				// add a tree sorter in folder mode
				new Ext.tree.TreeSorter(intelli.browse.vTree, {folderSort: true});
				 
				// set the root node
				var root = new Ext.tree.AsyncTreeNode({
					text: 'ROOT', 
					id: '0'
				});
				intelli.browse.vTree.setRootNode(root);
					
				root.expand();

				intelli.browse.vWindow = new Ext.Window(
				{
					title: intelli.admin.lang.tree,
					width : 400,
					height : 450,
					modal: true,
					autoScroll: true,
					closeAction : 'hide',
					items: [intelli.browse.vTree],
					buttons: [
					{
						text : intelli.admin.lang.ok,
						handler: function()
						{
							if('catcopy' == params[1])
							{
								var category = intelli.browse.vTree.getChecked('id');
							}
							else
							{
								var category = intelli.browse.vTree.getSelectionModel().getSelectedNode();
							}
							
							msg = intelli.admin.lang.copy_with_listings;

							Ext.Msg.show(
							{
								title: intelli.admin.lang.confirm,
								msg: msg,
								buttons: Ext.Msg.YESNO,
								icon: Ext.Msg.QUESTION,
								fn: function(btn)
								{
									if('yes' == btn)
									{
										Ext.Ajax.request(
										{
											url: intelli.browse.vUrl,
											method: 'POST',
											params:
											{
												action: params[1],
												id: params[2],
												'category[]': Ext.isArray(category) ? category : category.id
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

												intelli.browse.oGrid.grid.getStore().reload();
												intelli.browse.vWindow.hide();
											}
										});
									}
								}

							});
						}
					},{
						text : intelli.admin.lang.cancel,
						handler : function()
						{
							intelli.browse.vWindow.hide();
						}
					}]
				});

				intelli.browse.vWindow.show();
			}

			if(intelli.inArray(params[1], ['related', 'crossed']))
			{
				intelli.browse.vTreeM = new Ext.tree.TreePanel({
					animate: true, 
					width: 'auto',
					height: 'auto',
					border: false,
					plain: true,
					loader: new Ext.tree.TreeLoader(
					{
						dataUrl: 'get-categories.php',
						baseParams: {single: 0},
						requestMethod: 'GET'
					}),
					containerScroll: true
				});
			
				// add a tree sorter in folder mode
				new Ext.tree.TreeSorter(intelli.browse.vTreeM, {folderSort: true});
				 
				// set the root node
				var root = new Ext.tree.AsyncTreeNode({
					text: 'ROOT', 
					id: '0'
				});
				intelli.browse.vTreeM.setRootNode(root);
					
				root.expand();

				intelli.browse.vWindowM = new Ext.Window(
				{
					title: intelli.admin.lang.tree,
					width : 400,
					height : 450,
					modal: true,
					autoScroll: true,
					closeAction : 'hide',
					items: [intelli.browse.vTreeM],
					buttons: [
					{
						text : intelli.admin.lang.ok,
						handler: function()
						{
							var categories = intelli.browse.vTreeM.getChecked();
							var ids = new Array();

							for(var i = 0; i < categories.length; i++)
							{
								ids[i] = categories[i].id;
							}

							Ext.Ajax.request(
							{
								url: intelli.browse.vUrl,
								method: 'POST',
								params:
								{
									action: params[1],
									id: params[2],
									'categories[]': ids
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

									intelli.browse.vWindowM.hide();

									window.location = window.location.href;
								}
							});

						}
					},{
						text : intelli.admin.lang.cancel,
						handler : function()
						{
							intelli.browse.vWindowM.hide();
						}
					}]
				});

				intelli.browse.vWindowM.show();
			}
			
			if('rmv-related' == params[1])
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.related_confirm,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.browse.vUrl,
								method: 'POST',
								params:
								{
									action: 'remove_related',
									'ids[]': params[2],
									category: intelli.urlVal('id')
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

									window.location = window.location.href;
								}
							});
						}
					}
				});
			}

			if('edt-crossed' == params[1])
			{
				Ext.Msg.prompt('Crossed Category Title', 'Please enter category title:', function(btn, text){
    				if (btn == 'ok')
					{
						Ext.Ajax.request(
						{
							url: intelli.browse.vUrl,
							method: 'POST',
							params:
							{
								action: 'edit_crossed',
								title: text,
								'ids[]': params[2],
								category: intelli.urlVal('id')
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
								window.location = window.location.href;
							}
						});
        			}
				});
			}

			if('rmv-crossed' == params[1])
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_category_crossing,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.browse.vUrl,
								method: 'POST',
								params:
								{
									action: 'remove_crossed',
									'ids[]': params[2],
									category: intelli.urlVal('id')
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

									window.location = window.location.href;
								}
							});
						}
					}
				});
			}

			if('catlock' == params[1])
			{
				var link = $(this).attr("href");
				
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.category_locked_subcategories_alert,
					buttons: Ext.Msg.YESNOCANCEL,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							window.location = link + '&subcategories=true';
						}
						
						if('no' == btn)
						{
							window.location = link + '&subcategories=false';
						}
					}
				});
			}

			if('catunlock' == params[1])
			{
				var link = $(this).attr("href");
				
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.category_unlocked_subcategories_alert,
					buttons: Ext.Msg.YESNOCANCEL,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							window.location = link + '&subcategories=true';
						}
						
						if('no' == btn)
						{
							window.location = link + '&subcategories=false';
						}
					}
				});
			}

			return false;
		});
	});
});
