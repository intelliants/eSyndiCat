/**
 * Class for creating stars.
 * @class This is the Stars class.  
 *
 * @param {Array} conf
 *
 * @param {String} conf.id The id container element for stars
 * @param {String} conf.ratingField The id element for storing rate  
 * @param {String} conf.numStars The number of stars 
 * @param {String} conf.numDefault The number of stars which already marked by default
 * @param {String} conf.clsNoFill The CSS class
 * @param {String} conf.clsFill The CSS class
 * @param {String} conf.text The text. Could be contain {rate} phrase it will be replaced with current rate
 * @param {Function} conf.callback The callback function
 *
 * TODO:
 * Include type of event.
 */
intelli.stars = function(conf)
{
	var obj = (-1 != conf.id.indexOf('#')) ? $(conf.id) : $('#' + conf.id);
	var ratingField = (typeof conf.ratingField == 'undefined') ? null : (-1 != conf.ratingField.indexOf('#')) ? $(conf.ratingField) : $('#' + conf.ratingField);
	var numStars = conf.numStars ? conf.numStars : 10;
	var numDefault = conf.numDefault ? conf.numDefault : 0;
	var clsNoFill = conf.clsNoFill ? conf.clsNoFill : '';
	var clsFill = conf.clsFill ? conf.clsFill : '';
	var text = conf.text ? conf.text : 'Rate this {rate}';
	var mOverText = conf.mOverText ? conf.mOverText : '{current} out of {max}';
	var callback = (typeof conf.callback == 'function') ? conf.callback : function(){};

	/* prevent duplicate id */
	var idPrefix = intelli.getRandomLetter();

	/**
	 *  Printing stars
	 */
	var printStars = function()
	{
		var html = '';
		var titleText = '';

		if(numStars > 0)
		{
			mOverText = mOverText.replace('{max}', numStars);

			for(var i = 1; i <= numStars; i++)
			{
				titleText = mOverText.replace('{current}', i);

				if(i < numDefault)
				{
					html += '<div class="' + clsFill + '" id="star_' + idPrefix + '_' + i + '" title="'+ titleText +'"></div>';
				}
				else
				{
					html += '<div class="' + clsNoFill + '" id="star_' + idPrefix + '_' + i + '" title="'+ titleText +'"></div>';
				}
			}
		}

		html += '<div id="'+ idPrefix +'_ratingText" style="float: left; font-size: 10px; padding-left: 5px;"></div>';
		html += '<div style="clear:both; height:0; line-height: 0;"></div>';

		obj.html(html);

		for(var i = 1; i <= numStars; i++)
		{
			attachEvent($('#star_' + idPrefix + '_' + i), i);
		}
	};

	/**
	 *  Attach the events to stars elements
	 */
	var attachEvent = function(el, item)
	{
		var textDiv = $('#' + idPrefix + '_ratingText');

		var clicked = false;

		el.mouseover(function()
		{
			// reset all stars 
			for(var i = 1; i <= numStars; i++)
			{
				$('#star_' + idPrefix + '_' + i).attr('class', clsNoFill);
			}
			
			// selected stars
			for(var i = 1; i <= item; i++)
			{
				$('#star_' + idPrefix + '_' + i).attr('class', clsFill);
			}

			textDiv.text(text.replace('{rate}', item));

			clicked = false;
		});

		el.mouseout(function()
		{
			if(!clicked)
			{
				// reset all stars 
				for(var i = 1; i <= numStars; i++)
				{
					$('#star_' + idPrefix + '_' + i).attr('class', clsNoFill);
				}

				textDiv.text('');
			}
		});

		el.click(function()
		{
			// selected stars
			for(var i = 1; i <= item; i++)
			{
				$('#star_' + idPrefix + '_' + i).attr('class', clsFill);
			}

			textDiv.text(text.replace('{rate}', item));

			if(null != ratingField)
			{
				ratingField.val(item);
			}

			clicked = true;
		});

		el.bind('click', callback);
	};

	/**
	 *  Initialization stars
	 */
	this.init = function()
	{
		printStars();
	};
};
