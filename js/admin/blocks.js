intelli.blocks = function()
{
	var vUrl = 'controller.php?file=blocks';
	var blockPositions = new Array();

	$.each(intelli.config.esyndicat_block_positions.split(','), function(i, v)
	{
		blockPositions.push([v, v]);
	});

	return {
		positionsStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : blockPositions
		}),
		typesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['plain', 'plain'],['smarty', 'smarty'],['php', 'php'],['html', 'html']]
		}),
		statusesStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['active', 'active'],['inactive', 'inactive']]
		}),
		pagingStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['10', '10'],['20', '20'],['30', '30'],['40', '40'],['50', '50']]
		}),
		vUrl: vUrl,
		oGrid: null	
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
			{name: 'id', mapping: 'id'},
			{name: 'title', mapping: 'title'},
			{name: 'position', mapping: 'position'},
			{name: 'type', mapping: 'type'},
			{name: 'lang', mapping: 'lang'},
			{name: 'status', mapping: 'status'},
			{name: 'order', mapping: 'order'},
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
			width: 300,
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: intelli.admin.lang.position, 
			dataIndex: 'position', 
			sortable: true,
			width: 85,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.blocks.positionsStore,
				displayField: 'value',
				valueField: 'display',
				mode: 'local'
			})
		},{
			header: intelli.admin.lang.type, 
			dataIndex: 'type',
			width: 85,
			sortable: true,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.blocks.typesStore,
				displayField: 'value',
				valueField: 'display',
				mode: 'local'
			})
		},{
			header: intelli.admin.lang.language, 
			dataIndex: 'lang',
			width: 85
		},{
			header: intelli.admin.lang.status,
			dataIndex: 'status',
			sortable: true,
			width: 85,
			editor: new Ext.form.ComboBox({
				typeAhead: true,
				triggerAction: 'all',
				editable: false,
				lazyRender: true,
				store: intelli.blocks.statusesStore,
				displayField: 'value',
				valueField: 'display',
				mode: 'local'
			})
		},{
			header: intelli.admin.lang.order,
			dataIndex: 'order',
			sortable: true,
			width: 85,
			editor: new Ext.form.NumberField({
				allowBlank: false,
				allowDecimals: false,
				allowNegative: false
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

		this.dataStore.setDefaultSort('title');
	},
	init: function()
	{
		this.plugins = new Ext.ux.PanelResizer({
            minHeight: 100
		});

		this.title = intelli.admin.lang.blocks;
		this.renderTo = 'box_blocks';

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
					store: intelli.blocks.pagingStore,
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
					store: intelli.blocks.statusesStore,
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
						var rows = intelli.blocks.oGrid.grid.getSelectionModel().getSelections();
						var status = Ext.getCmp('statusCmb').getValue();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].data.id;
						}

						Ext.Ajax.request(
						{
							url: intelli.blocks.vUrl,
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

								intelli.blocks.oGrid.grid.getStore().reload();
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
						var rows = intelli.blocks.oGrid.grid.getSelectionModel().getSelections();
						var ids = new Array();

						for(var i = 0; i < rows.length; i++)
						{
							ids[i] = rows[i].data.id;
						}

						Ext.Msg.show(
						{
							title: intelli.admin.lang.confirm,
							msg: (ids.length > 1) ? intelli.admin.lang.are_you_sure_to_delete_selected_blocks : intelli.admin.lang.are_you_sure_to_delete_this_block,
							buttons: Ext.Msg.YESNO,
							icon: Ext.Msg.QUESTION,
							fn: function(btn)
							{
								if('yes' == btn)
								{
									Ext.Ajax.request(
									{
										url: intelli.blocks.vUrl,
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

											intelli.blocks.oGrid.grid.getStore().reload();

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
		this.dataStore.baseParams = {action: 'get'};
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

		// Edit fields
		intelli.blocks.oGrid.grid.on('afteredit', function(editEvent)
		{
			Ext.Ajax.request(
			{
				url: intelli.blocks.vUrl,
				method: 'POST',
				params:
				{
					action: 'update',
					ids: editEvent.record.id,
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

					intelli.blocks.oGrid.grid.getStore().reload();
				}
			});
		});

		intelli.blocks.oGrid.grid.on('cellclick', function(grid, rowIndex, columnIndex)
		{
			var record = grid.getStore().getAt(rowIndex);
			var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
			var data = record.get(fieldName);

			if('edit' == fieldName)
			{
				intelli.blocks.oGrid.saveGridState();

				window.location = 'controller.php?file=blocks&do=edit&id='+ record.json.id;
			}

			if('remove' == fieldName)
			{
				Ext.Msg.show(
				{
					title: intelli.admin.lang.confirm,
					msg: intelli.admin.lang.are_you_sure_to_delete_this_block,
					buttons: Ext.Msg.YESNO,
					icon: Ext.Msg.QUESTION,
					fn: function(btn)
					{
						if('yes' == btn)
						{
							Ext.Ajax.request(
							{
								url: intelli.blocks.vUrl,
								method: 'POST',
								params:
								{
									action: 'remove',
									ids: record.id
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

		intelli.blocks.oGrid.grid.getSelectionModel().on('rowselect', function()
		{
			Ext.getCmp('statusCmb').enable();
			Ext.getCmp('goBtn').enable();
			Ext.getCmp('removeBtn').enable();
		});

		intelli.blocks.oGrid.grid.getSelectionModel().on('rowdeselect', function(sm)
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
			intelli.blocks.oGrid.grid.getStore().lastOptions.params.limit = new_value;
			intelli.blocks.oGrid.grid.bottomToolbar.pageSize = parseInt(new_value);

			intelli.blocks.oGrid.grid.getStore().reload();
		});
	}
});

Ext.onReady(function()
{
	if(Ext.get("box_blocks"))
	{
		intelli.blocks.oGrid = new intelli.exGrid({url: intelli.blocks.vUrl});

		// Initialization grid
		intelli.blocks.oGrid.init();
	}

	/*
	 * Disable the language dropdown if block is multilanguage
	 *
	 */
	if($("#multi_language").attr("checked"))
	{
		$("select[name='lang']").attr("disabled", "disabled");
	}
	$("#multi_language").click(function()
	{
		var disabled = $(this).attr("checked") ? 'disabled' : '';

		$("select[name='lang']").attr("disabled", disabled);
	});

	/*
	 * Hide the pages section if block is sticky and show in otherwise
	 *
	 */
	if($("#sticky").attr("checked"))
	{
		$("#acos").css("display", 'none');
	}
	else
	{
		$("#acos").css("display", 'block');
	}

	$("#sticky").click(function()
	{
		var display = $(this).attr("checked") ? 'none' : 'block';

		$("#acos").css("display", display);
	});

	/*
	 * Select all pages checkbox
	 *
	 */
	var all_acos_count = $("#acos input[name^='visible_on_pages']").length;
	var checked_acos_count = $("#acos input[name^='visible_on_pages']:checked").length;

	if(checked_acos_count > 0 && all_acos_count == checked_acos_count)
	{
		$("#select_all").attr("checked", "checked");
	}

	$("#acos input[name^='visible_on_pages']").each(function()
	{
		$(this).click(function()
		{
			var checked = (all_acos_count == $("#acos input[name^='visible_on_pages']:checked").length) ? 'checked' : '';

			$("#select_all").attr("checked", checked);
		});
	});

	if($("#select_all").attr("checked"))
	{
		$("#acos input[type='checkbox']").each(function()
		{
			$(this).attr("checked", "checked")
		});
	}
	$("#select_all").click(function()
	{
		var checked = $(this).attr("checked") ? 'checked' : '';

		$("#acos input[type='checkbox']").each(function()
		{
			$(this).attr("checked", checked)
		});
	});

	$("#acos input[name^='select_all_']").each(function()
	{
		$(this).click(function()
		{
			var checked = $(this).attr("checked") ? 'checked' : '';
			var group_class = $(this).attr("class");

			$("input." + group_class).each(function()
			{
				$(this).attr('checked', checked);
			});
		});
	});

	/**
	 * Collapsible events
	 */
	if($("input[name='show_header']").attr("checked"))
	{
		$("input[name='collapsible']").attr("disabled", '');
		
		if($("input[name='collapsible']").attr("checked"))
		{
			$("input[name='collapsed']").attr("disabled", '');
		}
		else
		{
			$("input[name='collapsed']").attr("disabled", 'disabled');
			$("input[name='collapsed']").attr("checked", '');
		}
	}
	else
	{
		$("input[name='collapsible']").attr("disabled", 'disabled');
		$("input[name='collapsible']").attr("checked", '');
		
		if($("input[name='collapsible']").attr("checked"))
		{
			$("input[name='collapsed']").attr("disabled", '');
		}
		else
		{
			$("input[name='collapsed']").attr("disabled", 'disabled');
			$("input[name='collapsed']").attr("checked", '');
		}
	}

	$("input[name='show_header']").click(function()
	{
		if($("input[name='show_header']").attr("checked"))
		{
			$("input[name='collapsible']").attr("disabled", '');
			
			if($("input[name='collapsible']").attr("checked"))
			{
				$("input[name='collapsed']").attr("disabled", '');
			}
			else
			{
				$("input[name='collapsed']").attr("disabled", 'disabled');
				$("input[name='collapsed']").attr("checked", '');
			}
		}
		else
		{
			$("input[name='collapsible']").attr("disabled", 'disabled');
			$("input[name='collapsible']").attr("checked", '');
			
			if($("input[name='collapsible']").attr("checked"))
			{
				$("input[name='collapsed']").attr("disabled", '');
			}
			else
			{
				$("input[name='collapsed']").attr("disabled", 'disabled');
				$("input[name='collapsed']").attr("checked", '');
			}
		}
	});
	
	if($("input[name='collapsible']").attr("checked"))
	{
		$("input[name='collapsed']").attr("disabled", '');
	}
	else
	{
		$("input[name='collapsed']").attr("disabled", 'disabled');
		$("input[name='collapsed']").attr("checked", '');
	}
	
	$("input[name='collapsible']").click(function()
	{
		if($("input[name='collapsible']").attr("checked"))
		{
			$("input[name='collapsed']").attr("disabled", '');
		}
		else
		{
			$("input[name='collapsed']").attr("disabled", 'disabled');
			$("input[name='collapsed']").attr("checked", '');
		}
	});

	/**
	 * Multi Language events
	 */
	if($("#multi_language").attr("checked"))
	{
		$("#languages").css('display', 'none');

		$("#blocks_contents").css('display', 'block');

		$("input.block_languages").each(function()
		{
			$(this).attr("checked", "");

			$("#select_all_languages").attr("checked", "");

			initContentBox({lang: $(this).val(), checked: ''});
		});

		if('html' != $("#block_type").val() && CKEDITOR.instances.multi_contents)
		{
			CKEDITOR.instances.multi_contents.destroy();
		}
	}
	else
	{
		$("#languages").css('display', '');

		$("#blocks_contents").css('display', 'none');

		if('html' == $("#block_type").val() && !CKEDITOR.instances.multi_contents)
		{
			intelli.ckeditor('multi_contents', {toolbar: 'User', height: '400px'});
		}
	}

	$("#multi_language").click(function()
	{
		if($(this).attr("checked"))
		{
			$("#languages").css('display', 'none');

			$("#blocks_contents").css('display', 'block');

			$("input.block_languages").each(function()
			{
				$(this).attr("checked", "");

				$("#select_all_languages").attr("checked", "");

				initContentBox({lang: $(this).val(), checked: ''});
			});

			if('html' == $("#block_type").val() && !CKEDITOR.instances.multi_contents)
			{
				intelli.ckeditor('multi_contents', {toolbar: 'User', height: '400px'});
			}
		}
		else
		{
			$("#languages").css('display', '');

			$("#blocks_contents").css('display', 'none');

			if('html' == $("#block_type").val() && CKEDITOR.instances.multi_contents)
			{
				CKEDITOR.instances.multi_contents.destroy();
			}
		}
	});

	$("input.block_languages").each(function()
	{
		$(this).change(function()
		{
			initContentBox({lang: $(this).val(), checked: $(this).attr("checked")})
		});
	});

	$("input.block_languages:checked").each(function()
	{
		initContentBox({lang: $(this).val(), checked: $(this).attr("checked")})
	});

	$("#select_all_languages").click(function()
	{
		var checked = $(this).attr("checked") ? "checked" : "";

		$("input.block_languages").each(function()
		{
			$(this).attr("checked", checked);

			initContentBox({lang: $(this).val(), checked: checked});
		});
	});

	if($("input.block_languages:checked").length == $("input.block_languages").length)
	{
		$("#select_all_languages").attr("checked", "checked");
	}

	/*
	 * Block type change
	 */
	if('html' == $("#block_type").val())
	{
		$("textarea.cked:visible").each(function()
		{
			intelli.ckeditor($(this).attr("id"), {toolbar: 'User', height: '400px'});
		});
	}

	$("#type_tip_" + $("#block_type").val()).show();

	$("#block_type").change(function()
	{
		if('html' == $(this).val())
		{
		   $("textarea.cked:visible").each(function()
		   {
			   intelli.ckeditor($(this).attr("id"), {toolbar: 'User', height: '400px'});
		   });
		}
		else
		{
			$.each(CKEDITOR.instances, function(i, o)
			{
				o.destroy();
			});
		}

		$("div.option_tip").hide();
		$("#type_tip_" + $(this).val()).show();
	});

	function initContentBox(o)
	{
		var name = 'contents_' + o.lang;
		var display = o.checked ? 'block' : 'none';
		
		if('html' == $("#block_type").val())
		{
			if(!CKEDITOR.instances[name])
			{
				intelli.ckeditor(name, {toolbar: 'User', height: '400px'});
			}
		}
		else
		{
			if(CKEDITOR.instances[name])
			{
				CKEDITOR.instances[name].destroy();
			}
		}

		$("#blocks_contents_" + o.lang).css("display", display);
	}
});
