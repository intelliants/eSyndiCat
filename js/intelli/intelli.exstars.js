/**
 * Class for creating exstars.
 * @class This is the ExStars class.  
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
intelli.exstars = function(conf)
{
	var obj = (-1 != conf.id.indexOf('#')) ? $(conf.id) : $('#' + conf.id);

	var numStars = conf.numStars ? conf.numStars : 10;
	var numDefault = conf.numDefault ? conf.numDefault : 0;
	var widthStar = conf.widthStar ? conf.widthStar : 30;
	var heightStar = conf.heightStar ? conf.heightStar : 30;
	var clsNoFill = conf.clsNoFill ? conf.clsNoFill : '';
	var clsFill = conf.clsFill ? conf.clsFill : '';
	var clsHalfFill = conf.clsHalfFill ? conf.clsHalfFill : '';
	var mOverText = conf.mOverText ? conf.mOverText : '{current} out of {max}';
	var callback = (typeof conf.callback == 'function') ? conf.callback : function(){};

	/* prevent duplicate id */
	var idPrefix = intelli.getRandomLetter();

	var printExStars = function()
	{
		var html = '';
		var titleText = '';

		if(numStars > 0)
		{
			mOverText = mOverText.replace('{max}', numStars);

			for(var i = 1; i <= numStars; i++)
			{
				titleText = mOverText.replace('{current}', i);

				html += '<div class="' + clsNoFill + '" id="star_' + idPrefix + '_' + i + '" title="'+ titleText +'">&nbsp;</div>';
			}
		}

		if(numDefault > 0)
		{
			var totalWidth = numStars * widthStar;

			var voteWidth = Math.round(numDefault * totalWidth / numStars);

			intelli.cssCapture('width', voteWidth + 'px');
			intelli.cssCapture('position', 'absolute');
			intelli.cssCapture('z-index', '9001');
			intelli.cssCapture('cursor', 'default');

			html += '<div class="'+ clsHalfFill +'" id="extstar_current_rating_'+ idPrefix +'" style="'+ intelli.cssExtract() +'">&nbsp;</div>';
			//html += '<div class="'+ clsHalfFill +'" style="width: 40px; position: absolute; z-index: 2;"><h2>123</h2></div>';

			intelli.cssClear();
		}

		html += '<div id="'+ idPrefix +'_ratingText" style="float: left; font-size: 10px; padding-left: 5px;">&nbsp;</div>';
		html += '<div style="clear:both; height:0; line-height: 0;">&nbsp;</div>';

		obj.html(html);

		for(var i = 1; i <= numStars; i++)
		{
			attachEvent($('#star_' + idPrefix + '_' + i), i);
		}

		$('#exstars').mouseover(function()
		{
			$('#extstar_current_rating_' + idPrefix).css('display', 'none');
		});

		$('#exstars').mouseout(function()
		{
			$('#extstar_current_rating_' + idPrefix).css('display', 'block');
		});
	};

	/**
	 *  Attach the events to stars elements
	 */
	var attachEvent = function(el, item)
	{
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
			}
		});

		el.click(function()
		{
			// selected stars
			for(var i = 1; i <= item; i++)
			{
				$('#star_' + idPrefix + '_' + i).attr('class', clsFill);
			}

			clicked = true;
			
			var itemValue = $(this).attr('id').split('_')[2];
			var listingId = $('#listing_id').val();

			$.post("exstars.php", {action: "vote", id: listingId, rating: itemValue}, function(result) 
			{
				if(" " != result)
				{
					rePrintStars(eval('(' + result + ')'));
				}
			});
		});

		el.bind('click', callback);
	};

	var rePrintStars = function(data)
	{
		var html = '';
		var totalWidth = numStars * widthStar;

		var voteWidth = Math.round(data.rating * totalWidth / numStars);

		intelli.cssCapture('width', totalWidth + 'px');
		intelli.cssCapture('cursor', 'default');
		
		html += '<div class="'+ clsNoFill +'" style="'+ intelli.cssExtract() +'">';
		
		intelli.cssClear();

		intelli.cssCapture('width', voteWidth + 'px');
		intelli.cssCapture('position', 'absolute');
		intelli.cssCapture('cursor', 'default');

		html += '<div class="'+ clsHalfFill +'" style="'+ intelli.cssExtract() +'"></div>';
		html += '</div>';
		html += '<div style="clear:both; height:0; line-height: 0;"></div>';

		intelli.cssClear();

		obj.html(html);
	};

	this.init = function()
	{
		printExStars();
	};
};
