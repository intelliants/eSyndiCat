intelli.suggestListing = function()
{
	var vUrl = 'controller.php?file=suggest-listing';

	return {
		vUrl: vUrl,
		categoriesTree: null,
		categoriesWin: null,
		pagingStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['10', '10'],['20', '20'],['30', '30'],['40', '40'],['50', '50']]
		})
	};
}();

Ext.onReady(function()
{
	if(Ext.get('date'))
	{
		new Ext.form.DateField(
		{
			allowBlank: false,
			format: 'Y-m-d H:i:s',
			applyTo: 'date'
		});
	}

	$("#change_category").click(function()
	{
		intelli.suggestListing.categoriesTree = new Ext.tree.TreePanel({
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
			})
		});
	
		// add a tree sorter in folder mode
		new Ext.tree.TreeSorter(intelli.suggestListing.categoriesTree, {folderSort: true});
		 
		// set the root node
		var root = new Ext.tree.AsyncTreeNode({
			text: 'ROOT', 
			id: '0'
		});
		
		intelli.suggestListing.categoriesTree.setRootNode(root);
			
		root.expand();

		intelli.suggestListing.categoriesTree.on('render', function()
		{
			var path = Ext.get('category_parents').getValue();

			this.expandPath(path);
		});

		var id = Ext.get('category_id').getValue();

		function onAppend(t, p, n)
		{
			if(id == n.id)
			{
				function onParentExpanded()
				{
					t.getSelectionModel().select(n, null, true);
				};

				p.on("expand", onParentExpanded, null, {single: true});
			}
		};
		
		intelli.suggestListing.categoriesTree.on("append", onAppend);

		intelli.suggestListing.categoriesWin = new Ext.Window(
		{
			title: intelli.admin.lang.tree,
			width : 400,
			height : 450,
			modal: true,
			autoScroll: true,
			closeAction : 'hide',
			items: [intelli.suggestListing.categoriesTree],
			buttons: [
			{
				text : intelli.admin.lang.ok,
				handler: function()
				{
					var category = intelli.suggestListing.categoriesTree.getSelectionModel().getSelectedNode();
					var category_url = intelli.config.esyn_url + 'controller.php?file=browse&id=' + category.id;

					$("#parent_category_title_container a").text(category.attributes.text).attr("href", category_url);
					$("#category_id").val(category.id);

					intelli.suggestListing.categoriesWin.hide();
				}
			},{
				text : intelli.admin.lang.cancel,
				handler : function()
				{
					intelli.suggestListing.categoriesWin.hide();
				}
			}]
		});

		intelli.suggestListing.categoriesWin.show();

		return false;
	});

	$("input[name='assign_account']").each(function()
	{
		if(1 == $(this).val() && $(this).attr("checked"))
		{
			$("#new_account").css('display', 'block');
		}

		if(2 == $(this).val() && $(this).attr("checked"))
		{
			$("#exist_account").css('display', 'block');
		}

		$(this).click(function()
		{
			if(1 == $(this).val())
			{
				$("#new_account").css('display', 'block');
				$("#exist_account").css('display', 'none');
			}

			if(2 == $(this).val())
			{
				$("#new_account").css('display', 'none');
				$("#exist_account").css('display', 'block');
			}

			if(0 == $(this).val())
			{
				$("#new_account").css('display', 'none');
				$("#exist_account").css('display', 'none');
			}
		});
	});

	$("a.clear").each(function()
	{
		$(this).click(function()
		{
			var obj = $(this);
			var params = obj.attr('href').split('/');

			var field_name = params[0];
			var listing_id = params[1];
			var image_name = params[2];

			$.get(intelli.suggestListing.vUrl, {field: field_name, id: listing_id, image: image_name, action: 'clear'}, function(data)
			{
				var response = Ext.decode(data);
				var type = response.error ? 'error' : 'notif';
						
				intelli.admin.notifFloatBox({msg: response.msg, type: type, autohide: true});
				
				$("#file_manage").remove();
				
				if(obj.parents("div.image_box").length > 0)
				{
					obj.parents("div.image_box").remove();
				}
			});

			return false;
		});
	});

	var account_ds = new Ext.data.Store(
	{
		proxy: new Ext.data.HttpProxy({url: intelli.suggestListing.vUrl + '&action=getaccounts', method: 'GET'}),
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

	var account_id = '';
	var account_username = '';
	var account_info = $("#accounts_list").text();

	if('' != account_info)
	{
		account_id = account_info.split('|')[0];
		account_username = account_info.split('|')[1];
	}

	$("#accounts_list").html('');

	var account_search = new Ext.form.ComboBox(
	{
		store: account_ds,
		displayField: 'username',
		valueField: 'id',
		allowBlank: false,
		triggeAction: 'all',
		minChars: 1,
		typeAhead: false,
		loadingText: intelli.admin.lang.searching,
		renderTo: 'accounts_list',
		emptyText: intelli.admin.lang.type_account_username,
		hiddenName: 'account',
		value: account_id,
		valueNotFoundText: account_username,
		pageSize: 10,
		width: 200,
		listWidth: 200,
		hideTrigger: true,
		tpl: resultTpl,
		itemSelector: 'div.search-item'
	});

	$("textarea.ckeditor_textarea").each(function()
	{
		if(!CKEDITOR.instances[$(this).attr("id")])
		{
			intelli.ckeditor($(this).attr("id"), {toolbar: 'User', height: '400px'});
		}
	});

	/**
	 * Deep links
	 */
	
	/** get checked plan if it exists **/
	var checked_plan = $("input[name='assign_plan']:checked");

	if(checked_plan.length > 0)
	{
		var id_plan = checked_plan.attr("id").replace("plan_", "");

		$("#deep_links_" + id_plan).css("display", "block");
	}

	$("input[name='assign_plan']").each(function()
	{
		$(this).click(function()
		{
			var id_plan = $(this).attr("id").replace("plan_", "");

			$("div.deep_links").each(function()
			{
				$(this).css("display", "none");
			});

			$("#deep_links_" + id_plan).css("display", "block");
		});
	});

	/** event for remove deep link button **/
	$("input.remove_deep").each(function()
	{
		$(this).click(function()
		{
			var item = $(this);
			var id_plan = item.attr("id").replace("deep_", "");

			Ext.Msg.show(
			{
				title: intelli.admin.lang.confirm,
				msg: intelli.admin.lang.are_you_sure_to_delete_this_deep_link,
				buttons: Ext.Msg.YESNO,
				icon: Ext.Msg.QUESTION,
				fn: function(btn)
				{
					if('yes' == btn)
					{
						Ext.Ajax.request(
						{
							url: intelli.suggestListing.vUrl,
							method: 'GET',
							params:
							{
								action: 'remove_deep',
								'ids[]': id_plan
							},
							failure: function()
							{
								Ext.MessageBox.alert(intelli.admin.lang.error_saving_changes);
							},
							success: function(data)
							{
								item.parent("div.deep_link_box").remove();
							}
						});
					}
				}
			});

			return false;
		});
	});

	// pictures field
	$("div.pictures").find("input[type='button']").each(function()
	{
		$(this).click(function()
		{
			var action = $(this).attr('class');

			if('add_img' == action)
			{
				addImgItem($(this));
			}
			else
			{
				removeImgItem($(this));
			}
		});
	});

	function addImgItem(btn)
	{
		var clone = btn.parent().clone(true);
		var name = btn.siblings("input[type='file']").attr("name").replace('[]', '');
		var num = $("#" + name + "_num_img").val();

		if(num > 0)
		{
			$('input:file', clone).val('');
			btn.parent().after(clone);
			$("#" + name + "_num_img").val(num - 1);
		}
		else
		{
			alert(intelli.lang.no_more_files);
		}
	}

	function removeImgItem(btn)
	{
		var name = btn.siblings("input[type='file']").attr("name").replace('[]', '');
		var num = $("#" + name + "_num_img").val();

		if (btn.parent().prev().attr('class') == 'pictures' || btn.parent().next().attr('class') == 'pictures')
		{
			btn.parent().remove();
			$("#" + name + "_num_img").val(num * 1 + 1);
		}
	}

	$("a.lightbox").lightBox();
});
