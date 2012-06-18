/**
* Class for adding star for comments
*
* @el id of div element where will be placed stars
* @url url to template (for including images)
* @max max rating
* @text text
* @class unique adds for ids
*/

commentRating = function(conf)
{
	this.element = document.getElementById(conf.el);

	this.cls = conf.cls;	
	this.numStars = conf.max;
	this.URL = intelli.config.esyn_url + 'plugins/comments/templates';
	this.text = conf.text;
}

commentRating.prototype = 
{
	init: function()
	{
		if(this.element)
		{
			this.printStars();
		}
	}, 

	printStars: function()
	{
		var html = '';
		var css = '';

		css = "background: url('" + this.URL + "/img/gray.png'); ";
		css += "width: 15px; height: 15px; ";
		css += "cursor: pointer; ";
		css += "float: left;";

		for(var i = 1; i <= this.numStars; i++)
		{
			html += '<div style="' + css + '" ';
			html += 'id="'+this.cls+'rate' + i + '"';
			html += ' ></div>';
		}

		html += '<div id="'+this.cls+'rate_text" style="float: left; font-size: 11px; padding-left: 5px;">&nbsp;</div>';
		html += '<input type="hidden" name="'+this.cls+'rating" id="'+this.cls+'rating" value="" />';

		this.element.innerHTML = html;

		for(var i = 1; i <= this.numStars; i++)
		{
			this.attachEvent(this.cls+'rate' + i, i);
		}
	},

	attachEvent: function(el, item)
	{
		var cls = this.cls;
		var el = document.getElementById(el);
		var rateText = document.getElementById(cls+'rate_text');
			
		var numStars = this.numStars;
		var URL = this.URL;
		var text = this.text;
		var rating = document.getElementById(cls+'rating');
		var clicked = false;

		el.onmouseover = function()
		{
			// reset all stars 
			for(var i = 1; i <= numStars; i++)
			{
				var star = document.getElementById(cls+'rate' + i);
				star.style.background = "url('" + URL + "/img/gray.png')";
			}
			
			// selected new stars
			for(var i = 1; i <= item; i++)
			{
				var star = document.getElementById(cls+'rate' + i);
				star.style.background = "url('" + URL + "/img/gold.png')";
				rateText.innerHTML = text + '&nbsp;:&nbsp;' + i;
			}

			clicked = false;
		}
		el.onmouseout = function()
		{
			if(!clicked)
			{
				for(var i = 1; i <= item; i++)
				{
					var star = document.getElementById(cls+'rate' + i);
					star.style.background = "url('" + URL + "/img/gray.png')";
					rateText.innerHTML = '&nbsp;';
				}
				rating.value = '';
			}
		}
		el.onclick = function()
		{
			for(var i = 1; i <= item; i++)
			{
				var star = document.getElementById(cls+'rate' + i);
				star.style.background = "url('" + URL + "/img/gold.png')";
			}

			rating.value = item;
			clicked = true;
		}
	}
}
