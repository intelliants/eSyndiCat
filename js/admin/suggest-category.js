intelli.suggestCategory = function()
{
	var vUrl = 'controller.php?file=suggest-category';

	return {
		vUrl: vUrl,
		categoriesTree: null,
		categoriesWin: null,
		chooser: null
	};
}();

Ext.onReady(function()
{
	$("#change_category").click(function()
	{
		if(!intelli.suggestCategory.categoriesTree)
		{
			intelli.suggestCategory.categoriesTree = new Ext.tree.TreePanel({
				animate: true, 
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
			new Ext.tree.TreeSorter(intelli.suggestCategory.categoriesTree, {folderSort: true});
			 
			// set the root node
			var root = new Ext.tree.AsyncTreeNode({
				text: 'ROOT', 
				id: '0'
			});
			intelli.suggestCategory.categoriesTree.setRootNode(root);
				
			root.expand();
		}

		if(!intelli.suggestCategory.categoriesWin)
		{
			intelli.suggestCategory.categoriesWin = new Ext.Window(
			{
				title: intelli.admin.lang.tree,
				width : 400,
				height : 450,
				autoScroll: true,
				modal: true,
				closeAction : 'hide',
				items: [intelli.suggestCategory.categoriesTree],
				buttons: [
				{
					text : intelli.admin.lang.ok,
					handler: function()
					{
						var category = intelli.suggestCategory.categoriesTree.getSelectionModel().getSelectedNode();
						var category_url = intelli.config.esyn_url + 'controller.php?file=browse&id=' + category.id;

						$("#parent_category_title_container a[href!='#']").text(category.attributes.text).attr("href", category_url);
						$("#parent_id").val(category.id);

						intelli.suggestCategory.categoriesWin.hide();

						fillUrlBox();
					}
				},{
					text : intelli.admin.lang.cancel,
					handler : function()
					{
						intelli.suggestCategory.categoriesWin.hide();
					}
				}]
			});
		}

		intelli.suggestCategory.categoriesWin.show();

		return false;
	});

	$("input[name='num_cols_type']").each(function()
	{
		if(0 == $(this).val() && $(this).attr("checked"))
		{
			$("#nc").css("display", 'block');
		}
		
		$(this).click(function()
		{
			var display = (0 == $(this).val()) ? 'block' : 'none';

			$("#nc").css("display", display);
		});
	});

	$("input[name='num_neighbours_type']").each(function()
	{
		if(1 == $(this).val() && $(this).attr("checked"))
		{
			$("#nnc").css("display", 'block');
		}

		$(this).click(function()
		{
			var display = (1 == $(this).val()) ? 'block' : 'none';

			$("#nnc").css("display", display);

			if('block' == display)
			{
				$("input[name='num_neighbours']").val('');
			}
		});
	});

	intelli.ckeditor('description', {toolbar: 'User', height: '400px'});

	$("#choose_icon").click(function()
	{
		if(!intelli.suggestCategory.chooser)
		{
    		intelli.suggestCategory.chooser = new ImageChooser(
			{
    			url: intelli.suggestCategory.vUrl + '&action=getimages',
    			width: 515,
    			height: 350
    		});
    	}

		intelli.suggestCategory.chooser.show(this, function(data)
		{
			Ext.DomHelper.overwrite('icons', {
				tag: 'img', src: data.url, style:'margin:10px;visibility:hidden;'
			}, true).show(true).frame();

			$("#icon_name").val(data.url);
		});
	});

	$("#remove_icon").click(function()
	{
		$("#icons img").each(function()
		{
			$(this).hide('normal', function()
			{
				$(this).remove();
			});
		});

		$("#icon_name").val('');
	});

	/**
	 * Confirmation functionality for categories
	 *
	 */
	$("input[name='confirmation']").each(function()
	{
		$(this).click(function()
		{
			var display = ('1' == $(this).val()) ? "block" : "none";

			$("#confirmation_text").css("display", display);
		});

		if('1' == $(this).val() && $(this).attr("checked"))
		{
			$("#confirmation_text").css("display", "block");
		}
	});

	$("input[name='title'], input[name='path']").each(function()
	{
		$(this).blur(function()
		{
			fillUrlBox();
		});
	});

	function fillUrlBox()
	{
		var title = ('' == $("input[name='path']").val()) ? $("input[name='title']").val() : $("input[name='path']").val();

		if('' != title && $("#parent_id").length > 0)
		{
			$.get(intelli.suggestCategory.vUrl, {action: 'getcategoryurl', parent_id: $("#parent_id").val(), title: title}, function(data)
			{
				var data = eval('(' + data + ')');

				if('' != data.data)
				{
					$("#category_url").text(data.data);
					$("#category_url_box").fadeIn();
				}
				else
				{
					$("#category_url_box").fadeOut();
				}
			});
		}
		else
		{
			$("#category_url_box").fadeOut();
		}
	}
});
