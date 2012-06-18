/**
 * Class a common admin functionality.
 * @class This is the basic admin class.  
 * It can be considered an abstract class, even though no such thing
 * really existing in JavaScript
 * @constructor
 */
intelli.admin = function()
{
	/*
	 * Constants 
	 */

	/**
	 * Debug mode true to enable debug mod
	 * @type Boolean
	 */
	var DEBUG = true;
	
	/**
	 * Notification box id
	 * @type String
	 */
	var BOX_NOTIF_ID = 'notification';
	
	/**
	 * Header box id
	 * @type String
	 */
	var BOX_HEADER_ID = 'header';

	/**
	 * Footer box id
	 * @type String
	 */
	var BOX_FOOTER_ID = 'footer';

	/**
	 * Main box id
	 * @type String
	 */
	var BOX_MAIN_ID = 'main';

	/** 
	 * Menu box id
	 * @type String
	 */
	var BOX_MENU_ID = 'admin_menu';

	/**
	 * AJAX loader box id
	 * @type String
	 */
	var BOX_AJAX_ID = 'ajax-loader';

	/*
	 * Variables
	 */
	var tempNotifElement = null;
	var layout = null;
	var loaderBox = null;
	var notifElement = null;
	var notifFloatElement = null;

	if(DEBUG)
	{
		//console.time('time');
	}
	
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

	Ext.BLANK_IMAGE_URL = '../js/ext/resources/images/default/s.gif';
	Ext.chart.Chart.CHART_URL = '../js/ext/resources/charts.swf';

	Ext.Ajax.defaultHeaders = {
		'X-FlagToPreventCSRF': 'using ExtJS'
	};

	Ext.Ajax.on('requestcomplete', function(conn, xhr)
	{
		if('undefined' != typeof xhr.getResponseHeader)
		{
			if(xhr.getResponseHeader('X-eSyndiCat-Redirect') && 'login' == xhr.getResponseHeader('X-eSyndiCat-Redirect'))
			{
				window.location = "login.php";
			}
		}
	});

	$.ajaxSetup(
	{
		global: true,
		beforeSend: function(xhr)
		{
			xhr.setRequestHeader("X-FlagToPreventCSRF", "using jQuery");
		},
		complete: function(xhr)
		{
			if(xhr && 'login' == xhr.getResponseHeader("X-eSyndiCat-Redirect"))
			{
				window.location = "login.php";
			}
		}
	});

	function buildAdminMenuItems(items)
	{
		var html = '';

		if(items)
		{
			for(var i = 0; i < items.length; i++)
			{
				html += '<li>';
				html += '<a class="submenu" href="'+ items[i].href +'"';

				if('undefined' != typeof items[i].attr && '' != items[i].attr)
				{
					html += ' ' + items[i].attr;
				}

				if('undefined' != typeof items[i].style && '' != items[i].style)
				{
					html += ' ' + items[i].style;
				}

				html += '>';
				
				html += items[i].text;
				html += '</a></li>';
			}
		}

		return html;
	}

	function ajaxLoader()
	{
		/* show and hide ajax loader box */
		var loaderBox = Ext.get(BOX_AJAX_ID);

		Ext.Ajax.on('beforerequest', function()
		{
			loaderBox.show();
		});
		
		Ext.Ajax.on('requestcomplete', function()
		{
			loaderBox.hide({duration: '1'});
		});

		$('#' + BOX_AJAX_ID).ajaxStart(function()
		{
			$(this).fadeIn('1000');
		});
	
		$('#' + BOX_AJAX_ID).ajaxStop(function()
		{
			$(this).fadeOut('1000');
		});

		return loaderBox;
	};
	
	return {
		/**
		 *  Debug mode
		 */
		DEBUG: DEBUG,
		/**
		 * Assign event for displaying AJAX actions
		 *
		 * @return object of box
		 */
		initAjaxLoader: ajaxLoader,
		/**
		 * Show or hide element 
		 *
		 * @opt array array of options
		 * @el string id of element
		 * @action string the action (show|hide|auto)
		 *
		 * @return object of element
		 */
		display: function(opt)
		{
			if(!opt.el)
			{
				return false;
			}

			var obj = ('string' == typeof opt.el) ? Ext.get(opt.el) : opt.el;
			var act = opt.action || 'auto';

			if('auto' == act)
			{
				act = obj.isVisible() ? 'hide' : 'show';
			}

			obj[act]();

			return obj;
		},
		/**
		 * Show notification box
		 *
		 * @opt array array of options
		 * @msg mixed string or array of messages
		 * @type string string of type of message
		 * @autohide boolean auto hide notification box
		 * @pause int number of seconds before hide box
		 *
		 * @return object of element
		 */
		notifBox: function(opt)
		{
			var msg = opt.msg;
			var type = opt.type || 'notification';
			var autohide = opt.autohide || false;
			var pause = opt.pause || 5;
			var html = '';

			if('notif' == type)
			{
				type = 'notification';
			}
			
			if (opt.boxid)
			{
				var boxid = opt.boxid;
			}
			else
			{
				var boxid = BOX_NOTIF_ID;
			}

			notifElement = Ext.get(boxid);
			notifElement.update('');

			if(tempNotifElement)
			{
				Ext.get(tempNotifElement).remove();
			}

			html += '<div class="message '+ type +'">';
			html += '<div class="inner">';
			html += '<div class="icon">&nbsp;</div>';

			if(Ext.isArray(msg))
			{
				html += '<ul>';
				for(var i = 0; i < msg.length; i++)
				{
					if('' != msg[i])
					{
						html += '<li>' + msg[i] + '</li>';
					}
				}
				html += '</ul>';
			}
			else
			{
				html += ['<ul><li>', msg, '</li></ul>'].join('');
			}
			
			html += '</div></div>';

			tempNotifElement = Ext.DomHelper.append(notifElement, html);

			this.display({el: notifElement, action: 'show'});

			if(autohide)
			{
				Ext.get(boxid).pause(pause).fadeOut({useDisplay: true});
			}

			$("#" + boxid + " .inner").corner("bevel 2px").parent().corner("bevel 3px");

			return notifElement;
		},
		notifFloatBox: function(opt)
		{
			var msg = opt.msg;
			var type = opt.type || 'notif';
			var pause = opt.pause || 5;
			var html = '';

			if(!notifFloatElement)
			{
				notifFloatElement = Ext.DomHelper.insertFirst(document.body, {id:'msg_box'}, true);
			}
			
			html += '<div class="msg_box_float">';
			if(Ext.isArray(msg))
			{
				html += '<ul>';
				for(var i = 0; i < msg.length; i++)
				{
					if('' != msg[i])
					{
						html += '<li>' + msg[i] + '</li>';
					}
				}
				html += '</ul>';
			}
			else
			{
				html += ['<ul><li>', msg, '</li></ul>'].join('');
			}
			html += '</div>';
			
            notifFloatElement.alignTo(document, 't-t');

			var m = Ext.DomHelper.append(notifFloatElement, {html: html}, true);
			
			m.slideIn('t').pause(pause).ghost("t", {remove:true});
		},
		/**
		 * Show alert notification message 
		 *
		 * @opt array array of options
		 * @msg string the message
		 * @title string the title of box
		 * @type string the type of message
		 *
		 * @return void
		 */
		alert: function(opt)
		{
			if(Ext.isEmpty(opt.msg))
			{
				return false;
			}

			opt.title = (Ext.isEmpty(opt.title)) ? 'Alert Message' : opt.title;
			opt.type = intelli.inArray(opt.type, ['error', 'notif']) ? opt.type : 'notif';

			var icon = ('error' == opt.type) ? Ext.MessageBox.ERROR : Ext.MessageBox.WARNING;

			Ext.Msg.show(
			{
				title: opt.title,
				msg: opt.msg,
				buttons: Ext.Msg.OK,
				icon: icon
			});
		},
		/**
		 * Return the viewport object 
		 *
		 * @return object
		 */
		getLayout: function()
		{
			return layout;
		},
		/**
		 * Reload the admin menu tree 
		 *
		 * @return void
		 */
		refreshAdminMenu: function()
		{
			var query = new Array();

			$("div.menu ul.menu").each(function(i, k)
			{
				query[i] = 'menu[]=' + $(this).attr("id").split('_')[1];
			});

			$.get('menu-items.php?' + query.join('&'), function(data)
			{
				var data = eval('(' + data + ')');

				$("div.menu ul.menu").each(function()
				{
					var name = $(this).attr("id").split('_')[1];
					var menu_box = $("#menu_box_" + name);
					var items_container = $(this);

					var html = buildAdminMenuItems(data[name]);
					var display = ('' == html) ? 'none' : 'block';

					items_container.html(html);

					menu_box.css("display", display);
				});
			});
		}
	}
}();

intelli.admin.lang = new Object();

