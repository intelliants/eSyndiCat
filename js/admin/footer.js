Ext.onReady(function()
{
	/* header-menu show/hide START */
	var showHideMenu = false, hideMenu = false, menuOver = false;
	
	$('div.left-column').Sortable(
    {
        accept: 'dragGroup',
        helperclass: 'sortHelper',
        activeclass: 'dropActive',
        hoverclass: 'dropHover',
        tolerance: 'intersect',
        handle: '.menu-caption',
        onChange: function(groups)
        {
            jQuery.get("controller.php?file=order-change&type=adminblocks&"+$.SortSerialize().hash, function(){});
        }
    });

	$("div.jump-to").mouseover(function()
	{
		if ($("div.h-submenu").css("display") != 'block')
		{
			$("div.h-submenu").show(50);
			$("span.h-arrow").addClass("h-arrow-down");
		}
		menuOver = true;
	});

	$("div.h-submenu a, div.jump-to, div.h-divider").mouseover(function()
	{
		showHideMenu = !showHideMenu;
	});
	$("div.h-submenu a, div.jump-to, div.h-divider").mouseout(function()
	{
		showHideMenu = !showHideMenu;
	});

	$("body").mouseover(function()
	{
		if (!showHideMenu && menuOver)
		{
			setTimeout(function()
			{
				$("span.h-arrow").removeClass("h-arrow-down");
				$("div.h-submenu").hide();
				menuOver = false;
			}, 50);
		}
	});
	/* header-menu show/hide END */
	
	$(".tab-shortcut").click(function()
	{
		var id = $(this).attr("id").replace('esyntab-shortcut-', '');
		var tab_content = $("#esyntab-content-" + id);

		if('block' == tab_content.css("display"))
		{
			tab_content.hide();

			$(".tab-shortcut").each(function()
			{
				$(this).removeClass("tab-shortcut-active");
				$(this).children().removeClass("tab-shortcut-inner-active");
				$(this).children().addClass("tab-shortcut-inner");
			});
		}
		else
		{
			$(".tab-content").each(function()
			{
				if ($(this).css("display") == 'block')
				{
					$(this).hide();
				}
			});

			$(".tab-shortcut").each(function()
			{
				$(this).removeClass("tab-shortcut-active");
				$(this).children().removeClass("tab-shortcut-inner-active");
				$(this).children().addClass("tab-shortcut-inner");
			});

			$(this).addClass("tab-shortcut-active");
			$(this).children().removeClass("tab-shortcut-inner");
			$(this).children().addClass("tab-shortcut-inner-active");

			tab_content.show();
		}
	});

	$("div.minmax").each(function()
	{
		$(this).click(function()
		{
			var a = '';
			
			if ($(this).next(".box-content").css("display") == 'block')
			{
				$(this).next(".box-content").slideUp();
				$(this).removeClass("white-open");
				$(this).addClass("white-close");
			}
			else
			{
				$(this).next(".box-content").slideDown();
				$(this).removeClass("white-close");
				$(this).addClass("white-open");
			}

			$('div.menu').each(function()
			{
				var c = $(this).find('div.minmax').hasClass('white-open') ? 1 : 0;

				a += '&state[' + this.id.replace('menu_box_', '') + ']=' + c;
			});

			jQuery.get("controller.php?file=order-change&type=menu_close" + a, function(){});
			
			return false;
		});

	});
	/* box show/hide END */

	/* applying corners START */
	$("div.box .inner, div.tip .inner, div.message .inner, div.menu .inner, div.text-field .inner").corner("5px").parent().corner("bevel 3px");
	$("div.tab-content-inner").corner("bevel 1px").parent().corner("bevel 1px");
	$("div.box-caption, div.menu-caption").corner("bevel 1px top");
	$("div.header-buttons a.inner").corner("bevel 2px");
	/* applying corners END */

	// styles for disabled elements
	var inpObj;
	$("input, textarea").each(function()
	{
		inpObj = $(this);
		if (inpObj.attr("readonly") || inpObj.attr("disabled"))
		{
			inpObj.css({ background: "#ececec", border: "1px solid #bbb", color: "#666" });
		}
	});

	/* table highlighting START */
	var i = 0;
	$("table.common tr").each(function()
	{
		if (i % 2)
			$(this).addClass("highlight");
		i++;
	});

	$("table.common tr").mouseover(function()
	{
		$(this).addClass("hover");
	});
	$("table.common tr").mouseout(function()
	{
		$(this).removeClass("hover");
	});
	/* table highlighting END */

	/*
	 * Help tooltips
	 */
	$(".tip-header").each(function()
	{
		var id = $(this).attr("id").replace("tip-header-", "");

		if($("#tip-content-" + id).length > 0)
		{
			$(this).append('<span class="question" id="tip_" '+ id +'><img src="templates/default/img/icons/sp.gif" alt="" width="16" height="17" /></span>').find("span.question").each(function()
			{
				new Ext.ToolTip(
				{
					target: this,
					dismissDelay: 0,
					contentEl: 'tip-content-' + id
				});
			});
		}
	});

	/*
	 * Top menu
	 */
	if('' != $("#top_menu").text())
	{
		$("#top_menu").show();
	}

	/*
	 * qtip for quick search listing text input
	 */
	if(Ext.get("quick_search_listing"))
	{
		new Ext.ToolTip(
		{
			target: 'quick_search_listing',
			anchor: 'top',
			anchorOffset: 145,
			html: intelli.admin.lang.quick_search_listing_qtip
		});
	}

	/*
	 * qtip for quick search category text input
	 */
	if(Ext.get("quick_search_category"))
	{
		new Ext.ToolTip(
		{
			target: 'quick_search_category',
			anchor: 'top',
			anchorOffset: 145,
			width: 175,
			html: intelli.admin.lang.quick_search_category_qtip
		});
	}

	/*
	 * qtip for quick search account text input
	 */
	if(Ext.get("quick_search_account"))
	{
		new Ext.ToolTip(
		{
			target: 'quick_search_account',
			anchor: 'top',
			anchorOffset: 145,
			html: intelli.admin.lang.quick_search_account_qtip
		});
	}
	
	/*
	 * Iphone switcher
	 */
	$(".iphoneswitch").each(function()
	{
		var default_state = $(this).hasClass("on") ? "on" : "off";
		var id = $(this).attr("id").split("-")[1];
		
		if ('conf' == id)
		{
			id = 'param[' + $(this).attr("id").split("-")[2] + ']';
		}
		
		$(this).iphoneSwitch(default_state, function()
		{
			$("input[type='hidden'][name='" + id + "']").val(1);
		},
		function()
		{
			$("input[type='hidden'][name='" + id + "']").val(0);
		});
	});

	/*
	 * Init AJAX notification box
	 */
	intelli.admin.initAjaxLoader();
});
