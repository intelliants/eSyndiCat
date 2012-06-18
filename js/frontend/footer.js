$(function()
{
	// TODO: make quickSearch class singletone
	var quickSearch = new intelli.search(
	{
		id: 'search_input',
		container: 'quickSearch'
	});
	
	if(intelli.config.thumbshot)
	{
		var thumbs = new intelli.thumbs();
	
		thumbs.init();
	}

	// The quick search
	quickSearch.init();

	if($('#page_setup').length > 0)
	{
		// The resizing functionality
		intelli.resize.init();
	}

	// The common functionality
	intelli.common.init();

	// Language switcher
	if (jQuery('#language_select').length > 0)
	{
		jQuery('#language_select').change(function()
		{
			var s = (-1 == window.location.href.indexOf('?')) ? '?' : '&';
			var l = 'language=' + jQuery(this).val();

			if (-1 == window.location.href.indexOf('language='))
			{
				window.location.href = window.location.href + s + l;
			}
			else
			{
				var r = new RegExp("language=([a-zA-Z]{2})", "g");
				
				window.location.href = window.location.href.replace(r, l);
			}

			return false;
		});
	}


	/*
	 * eSyndiCat actions
	 */
	$("a[class^=esynactions]").each(function()
	{
		$(this).click(function()
		{
			var params = $(this).attr("class").split("_");

			if('add-favorite' == params[1])
			{
				intelli.common.actionFavorites(params[2], params[3], 'add');
			}

			if('remove-favorite' == params[1])
			{
				intelli.common.actionFavorites(params[2], params[3], 'remove');
			}

			if('report-listing' == params[1])
			{
				intelli.common.reportBrokenListing(params[2]);
			}

			if('visit-website' == params[1])
			{
				window.location = params[3];
			}

			return false;
		});
	});
});
