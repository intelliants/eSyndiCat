/**
 * Class for resizing page.
 * @class This is the Resize class.  
 *
 *
 * TODO: 
 * - change works with cookie
 */
intelli.resize = function()
{
	var cookiePageWidth = 'cookiePageWidth', cookieLetterSize = 'cookieLetterSize';
	var curLetterSize, coeff = 1000;

	var minLetterSize = '0.7em';	// can be changed (only in em)
	var norLetterSize = '1em';
	var maxLetterSize = '1.3em';	// can be changed (only in em)
	var letterStep = 0.1;			// can be changed (step of letters size change only in em)

	var minPageWidth = '920px';		// can be changed (only in px)
	var norPageWidth = '1100px';	// can be changed (only in px)
	var maxPageWidth = '98%';		// can be changed (only in %)

	if (intelli.readCookie(cookiePageWidth) == null)
	{
		intelli.createCookie(cookiePageWidth, minPageWidth, 1);
	}

	if (intelli.readCookie(cookieLetterSize) == null)
	{
		intelli.createCookie(cookieLetterSize, norLetterSize, 1);
	}

	function hideAll()
	{
		$("#small").empty();	$("#small").append('&nbsp;');
		$("#normal").empty();	$("#normal").append('&nbsp;');
		$("#large").empty();	$("#large").append('&nbsp;');

		$("#w800").empty();		$("#w800").append('&nbsp;');
		$("#w1024").empty();	$("#w1024").append('&nbsp;');
		$("#wLiquid").empty();	$("#wLiquid").append('&nbsp;');
	};

	function setCurrentStatus()
	{
		hideAll();

		if (intelli.readCookie(cookieLetterSize) == minLetterSize)
		{
			$("#small").empty();
			$("#small").append('<div id="dsmall">&nbsp;</div>');
		}
		else if (intelli.readCookie(cookieLetterSize) == norLetterSize)
		{
			$("#normal").empty();
			$("#normal").append('<div id="dnormal">&nbsp;</div>');
		}
		else if (intelli.readCookie(cookieLetterSize) == maxLetterSize)
		{
			$("#large").empty();
			$("#large").append('<div id="dlarge">&nbsp;</div>');
		}

		if (intelli.readCookie(cookiePageWidth) == minPageWidth)
		{
			$("#w800").empty();
			$("#w800").append('<div id="dw800">&nbsp;</div>');
		}
		else if (intelli.readCookie(cookiePageWidth) == norPageWidth)
		{
			$("#w1024").empty();
			$("#w1024").append('<div id="dw1024">&nbsp;</div>');
		}
		else if (intelli.readCookie(cookiePageWidth) == maxPageWidth)
		{
			$("#wLiquid").empty();
			$("#wLiquid").append('<div id="dwLiquid">&nbsp;</div>');
		}
	}

	return {
		init: function()
		{
			setCurrentStatus();

			$("#w800").click(function() 
			{
				var w = intelli.readCookie(cookiePageWidth);
				
				if (maxPageWidth == w)
				{
					w = document.body.clientWidth;
					w = w * parseInt(maxPageWidth) / 100;
					$('div.page').css('width', w + 'px');
				}
				
				intelli.createCookie(cookiePageWidth, minPageWidth, 1);

				$("div.page").animate({
					width: parseInt(minPageWidth)
				}, "slow");
				
				setCurrentStatus();
			});

			$("#w1024").click(function() 
			{
				var w = intelli.readCookie(cookiePageWidth);
				
				if (maxPageWidth == w)
				{
					w = document.body.clientWidth;
					w = w * parseInt(maxPageWidth) / 100;
					$('div.page').css('width', w + 'px');
				}
				
				$("div.page").animate({
					width: parseInt(norPageWidth)
				}, "slow");

				intelli.createCookie(cookiePageWidth, norPageWidth, 1);
				
				setCurrentStatus();
			});

			$("#wLiquid").click(function() 
			{
				if (maxPageWidth != intelli.readCookie(cookiePageWidth))
				{
					var w = document.body.clientWidth;
					w = w * parseInt(maxPageWidth) / 100;
					$("div.page").animate({
						width: w
					}, "slow");
				}
				intelli.createCookie(cookiePageWidth, maxPageWidth, 1);
				setCurrentStatus();
			});

			$("#small").click(function() 
			{
				curLetterSize = parseFloat(intelli.readCookie(cookieLetterSize)) * coeff - letterStep * coeff;
				curLetterSize /= coeff;
				if (curLetterSize < parseFloat(minLetterSize))
				{
					curLetterSize = parseFloat(minLetterSize);
				}
				$("div.page").css({ fontSize: curLetterSize + 'em' });
				intelli.createCookie(cookieLetterSize, curLetterSize + 'em', 1);
				setCurrentStatus();
			});

			$("#normal").click(function() 
			{
				$("div.page").css({ fontSize: norLetterSize });
				intelli.createCookie(cookieLetterSize, norLetterSize, 1);
				setCurrentStatus();
			});
			
			$("#large").click(function() 
			{
				curLetterSize = parseFloat(intelli.readCookie(cookieLetterSize)) * coeff + letterStep * coeff;
				curLetterSize /= coeff;
				if (curLetterSize > parseFloat(maxLetterSize))
				{
					curLetterSize = parseFloat(maxLetterSize);
				}
				$("div.page").css({ fontSize: curLetterSize + 'em' });
				intelli.createCookie(cookieLetterSize, curLetterSize + 'em', 1);
				setCurrentStatus();
			});

			$(window).resize(function() 
			{
				if (maxPageWidth == intelli.readCookie(cookiePageWidth))
				{
					$("div.page").css("width", intelli.readCookie(cookiePageWidth));
				}
			});
		}
	}
}();
