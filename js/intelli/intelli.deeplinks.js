/**
 * Class for creating deep links section.
 * @class This is the Deep Links class.  
 *
 * @param {Array} conf
 *
 * @param {String} conf.container The id container element for deep links
 * @param {String} conf.id The id of deep links element
 * @param {Array} conf.session The array of last state
 * @param {Boolean} conf.restore Restoring data after updating box
 *
 */
intelli.deeplinks = function(conf)
{
	var objContainer = (-1 != conf.container.indexOf('#')) ? $(conf.container) : $('#' + conf.container);
	var obj = (-1 != conf.id.indexOf('#')) ? $(conf.id) : $('#' + conf.id);

	var restore = conf.restore ? conf.restore : false;
	var deeplinks = (typeof conf.session != 'undefined') ? conf.session : new Array();

	var titleLabel = intelli.lang.title ? intelli.lang.title : 'Title';
	var titleUrl = intelli.lang.url ? intelli.lang.url : 'URL';

	this.init = function(num)
	{
		var existsLinks = obj.children("input[name='deep_links']");

		if(existsLinks.length > 0)
		{
			existsLinks.each(function(i, v)
			{
				var link = $(this).val().split('|');

				deeplinks[i] = {title: link[0], url: link[1]};
			});
		}

		obj.empty();

		this.create(num);
	};

	/**
	 * Create deep links section
	 *
	 * @param {Integer} The number of deep links
	 */
	this.create = function(num)
	{
		var html = '';
		
		html += '<div id="form_deep_links">';

		for(var i = 0; i < num; i++)
		{
			var defaultTitle = '';
			var defaultUrl = '';

			if(restore && (typeof deeplinks[i] != 'undefined'))
			{
				defaultTitle = deeplinks[i].title;
				defaultUrl = deeplinks[i].url;
			}

			html += '<div class="deeplinks_box">';
			html += '<table border="0">';
			
			html += '<tr>';
			html += '<td>';
			html += '<label for="deep_link_title_' + i + '">';
			html += '<strong>' + titleLabel + '</strong>:&nbsp;';
			html += '</label>';
			html += '</td>';
			html += '<td>';
			html += '<input type="text" id="deep_link_title_' + i + '" class="text" name="deep_links[' + i + '][title]" value="'+ defaultTitle +'" size="35" />';
			html += '</td>';
			html += '</tr>';
			
			html += '<tr>';
			html += '<td>';
			html += '<label for="deep_link_url_' + i + '">';
			html += '<strong>' + titleUrl + '</strong>:&nbsp;';
			html += '</label>';
			html += '</td>';			
			html += '<td>';
			html += '<input type="text" id="deep_link_url_' + i + '" class="text" name="deep_links[' + i + '][url]" value="'+ defaultUrl +'" size="35" />';
			html += '</td>';
			
			html += '</table>';
			html += '</div>';

			if(num > 1 && num - 1 != i)
			{
				html += '<hr />';
			}
		}

		html += '</div>';

		html += '<div id="val_deep_links" style="display: none; float: left;">';
		html += '</div>';
		html += '<div id="edit_button_deep_link" style="float: right; display: none;">';
		html += '<img class="edit-field" title="'+ intelli.lang.edit + ' Deep Links" alt="'+ intelli.lang.edit + ' Deep Links" src="templates/'+ intelli.config.tmpl +'/img/sp.gif" />';
		html += '</div>';

		obj.html(html);

		$('#edit_button_deep_link').click(function()
		{
			intelli.display('#divSuggestButton', 'hide');
			intelli.display('#saveChanges', 'show');

			intelli.display('#edit_button_deep_link', 'hide');
			intelli.display('#val_deep_links', 'hide');
			intelli.display('#form_deep_links', 'show');
		});
	};

	/**
	 * Show or hide deep links section
	 *
	 * @param {String} The action (show|hide)
	 */
	this.display = function(action)
	{
		intelli.display(objContainer, action);
	};

	/**
	 * Conversion the form
	 */
	this.conversion = function()
	{
		var html = '';
		var title = '';
		var url = '';
		var i = 0;

		$(obj).find("div[id='form_deep_links'] div").each(function()
		{
			var thisItem = $(this);

			title = $(thisItem).find("input[name*='title']").val();
			url = $(thisItem).find("input[name*='url']").val();
			
			html += '<div>';
			html += title;
			html += '&nbsp;';
			html += '<a href="'+ url +'" target="_blank">'+ url +'</a>';
			html += '</div>';

			if(restore)
			{
				deeplinks[i] = {title: title, url: url};
				i++;
			}
		});

		$('#val_deep_links').html(html);

		intelli.display('#form_deep_links', 'hide');
		intelli.display('#edit_button_deep_link', 'show');
		intelli.display('#val_deep_links', 'show');
	};

	/**
	 * Return last form data
	 */
	this.getLastState = function()
	{
		this.transform();

		return deeplinks;
	};

	this.transform = function()
	{
		var i = 0;

		$(obj).find("div.deeplinks_box").each(function()
		{
			var deep = new Array();
			
			$(this).find(":input").each(function(i, v)
			{
				deep[i] = $(this).val();
			});
			
			deeplinks[i++] = {title: deep[0], url: deep[1]};
		});
	};
};
