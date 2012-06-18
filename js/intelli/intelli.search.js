/**
 * Class for creating quick search listing.
 * @class This is the Quick Search class.  
 *
 * @param {Array} conf
 *
 * @param {String} conf.id The id search input element
 * @param {String} conf.container The container of quick search result 
 * @param {String} conf.minLength The min length of query string
 * @param {String} conf.url The URL to script
 * @param {String} conf.limit The number listings to display in the result box
 *
 */
intelli.search = function(conf)
{
	var obj = (-1 != conf.id.indexOf('#')) ? $(conf.id) : $('#' + conf.id);
	var objCon = (-1 != conf.container.indexOf('#')) ? $(conf.container) : $('#' + conf.container);

	var minLength = conf.minLingth ? conf.minLingth : 4;
	var url = conf.url ? conf.url : 'get-search.php';
	var searchFields = conf.searchFields ? conf.searchFields : 'title,description';
	var limit = conf.limit ? conf.limit : 10;

	var containerHidden = false;	
	var timeOutHandler = null;

	this.init = function()
	{
		obj.keydown(function()
		{
			if(obj.val().length >= minLength)
			{
				var qUrl = url;

				qUrl += '?q=' + obj.val();
				qUrl += '&fields=' + searchFields;
				qUrl += '&limit=' + limit;

				$.getJSON(qUrl, function(listings)
				{
					if(null != listings && '' != listings)
					{
						printElement(listings);
	
						intelli.display(objCon, 'show');
					}
				});
			}
			else
			{
				intelli.display(objCon, 'hide');
			}
		});

		objCon.mouseout(function()
		{
			containerHidden = true;
			timeOutHandler = setTimeout(hide, 2000);
		});

		objCon.mouseover(function()
		{
			clearTimeout(timeOutHandler);
			intelli.display(objCon, 'show');
		});
	};

	var hide = function()
	{
		if(containerHidden)
		{
			intelli.display(objCon, 'hide');
			containerHidden = false;
		}
	};

	var printElement = function(listings)
	{
		var html = '';

		objCon.empty();

		for(var i = 0; i < listings.length; i++)
		{
			var url = intelli.config.esyn_url + listings[i].url +'-l'+ listings[i].id +'.html';

			html += '<div class="quickSearchItem">';
			html += '<a href="'+ url +'"><h3>'+ listings[i].title +'</h3></a>';
			html += listings[i].description;
			html += '</div>';
		}

		objCon.append(html);
	};
};
