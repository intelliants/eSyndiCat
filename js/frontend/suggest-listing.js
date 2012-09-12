$(function()
{
	var fields = new intelli.fields({
		id: 'fields',
		part: 'edit',
		restore: true,
		listingId: intelli.urlVal('edit'),
		session: sessvars.fields
	});

	fields.transform();

	/* Setting up the tree categories */
	var treeCat = new intelli.tree({
		id: 'tree',
		type: 'radio',
		state: $('#category_id').val(),
		hideRoot: true,
		menuType: intelli.config.categories_tree_type,
		callback: function()
		{
			var catId = $(this).val();
			var letter = $('#prefix_mCrossTree').val();

			$('#categoryTitle > strong').text($(this).attr('title'));
			$('#category_id').val($(this).val());
			
			fields.fillFields();
		},
		dropDownCallback: function(id, title)
		{
			/* hiding any notification boxes */
			intelli.display('notification', 'hide');

			$('#categoryTitle > strong').text(title);
			$('#category_id').val(id);
			
			fields.fillFields();
		}
	});

	treeCat.init();

	/* Deep links */
	var deeplinks = new intelli.deeplinks({
		id: 'deepLinks',
		container: 'deepLinksDiv',
		restore: true,
		session: sessvars.deeplinks
	});

	deeplinks.transform();

	fields.fillFields();

	// ajax form
	$.fn.ajaxSubmit.debug = true;
	$('#form_listing').ajaxForm({
		dataType: 'json',
		beforeSubmit: function(data, form) {
			form.ajaxLoader();
		},
		success: formResponse
	});
});

// handle the form POST response 
function formResponse(resp)
{
	$('#form_listing').ajaxLoaderRemove();
	if (resp.err)
	{
		$('#msg').html(resp.msg).attr('class', 'error');
	}
	else
	{
		$('#form_listing').hide();
		$('#msg').html(resp.msg).attr('class', 'notification');
	}
	$('html').animate({ scrollTop: $('#msg').offset().top }, { duration: 'slow', easing: 'swing'});
}
