$(function()
{
	/* Setting up the tree categories */
	var treeCat = new intelli.tree({
		id: 'tree',
		type: 'radio',
		state: '',
		hideRoot: true,
		menuType: intelli.config.categories_tree_type,
		callback: function()
		{
			var idCategory = $(this).val();
			var titleCategory = $(this).attr('title');

			/* hiding any notification boxes */
			intelli.display($("div.error"), 'hide');

			$('#category_id').val(idCategory);
			$('#category_title').val(titleCategory);

			$('#categoryTitle > strong').text(titleCategory);
		},
		dropDownCallback: function(id, title)
		{
			$('#category_id').val(id);
			$('#category_title').val(title);
			$('#categoryTitle > strong').text(title);
		}
	});

	/* Initialization tree categories */
	treeCat.init();

	/* Event handler for displaying tree categories */
	$('#changeLabel').click(function()
	{
		intelli.display('#treeContainer', 'auto'); 

		if(intelli.lang.change == $(this).text())
		{
			$(this).text(intelli.lang.apply);
		}
		else
		{
			$(this).text(intelli.lang.change);
		}
	});
});
