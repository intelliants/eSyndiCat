/**
 * Class for creating tree categories.
 * @class This is the Tree class.  
 *
 * @param {Array} conf
 *
 * @param {String} conf.id The id container element for tree
 * @param {String} conf.type The type of tree element. (Radio | Checkbox)  
 * @param {String} conf.disabled The list of disabled categories by default
 * @param {String} conf.state The list of checked categories by default
 * @param {String} conf.expanded The list of expanded categories by default
 * @param {String} conf.url The URL to script
 * @param {Boolean} conf.hideRoot Showing root category
 * @param {Boolean} expandableLocked Display the subcategories of locked category
 * @param {Array} conf.defaultCategory The array of default category
 * @param {Function} conf.callback The callback function
 *
 * TODO: 
 * Add ability to pass as defaultCategory the titles of categories
 * Add spiner
 */
intelli.tree = function(conf)
{
	var obj = (-1 != conf.id.indexOf('#')) ? $(conf.id) : $('#' + conf.id);
	var type = conf.type ? conf.type : 'radio';
	
	var disabled = conf.disabled ? conf.disabled.split(',') : '';
	var state = conf.state ? conf.state.split(',') : '';
	var expanded = conf.expanded ? conf.expanded.split(',') : '';

	var url = conf.url ? conf.url : 'get-categories.php';
	var hideRoot = conf.hideRoot ? conf.hideRoot : false;

	var expandableLocked = conf.expandableLocked ? conf.expandableLocked : false;

	var menuType = conf.menuType ? conf.menuType : 'tree';

	var defaultIdCategory = conf.defaultIdCategory ? conf.defaultIdCategory : 0;

	var callback = (typeof conf.callback == 'function') ? conf.callback : function(){};
	var dropDownCallback = (typeof conf.dropDownCallback == 'function') ? conf.dropDownCallback : function(){};

	var dropdownInputId = conf.dropdownInputId || 'category';
	var dropdownItemId = conf.dropdownItemId || 'dropdownmenu';
	var simpleDropDownId = conf.simpleDropDownId || 'simpledropdown';
	var treeName = conf.treeName ? conf.treeName : 'categories[]';
	/* prevent duplicate id if there are several tree on page */
	var idPrefix = intelli.getRandomLetter();
	
	var defaultCategory;

	if(hideRoot || defaultIdCategory)
	{
		var params = '';
		
		$.ajaxSetup({async: false});

		params += (url.indexOf('?') > 0) ? '&' : '?';
		params += (defaultIdCategory) ? 'id=' + defaultIdCategory + '&type=' + menuType : 'id=0' + '&type=' + menuType;

		if('tree' == menuType)
		{
			$.getJSON(url + params, function(categories)
			{
				if('null' != categories)
				{
					defaultCategory = categories;
				}
			});
		}
		else if('dropdown' == menuType)
		{
			$.get(url + params, function(categories)
			{
				if('null' != categories)
				{
					defaultCategory = categories;
				}
			});
		}
		else if ('simple dropdown' == menuType)
		{
			$.get(url + params, function(categories)
			{
				if('null' != categories)
				{
					defaultCategory = categories;
				}
			});
		}

		$.ajaxSetup({async: true});
	}
	else
	{
		defaultCategory = conf.defaultCategory ? conf.defaultCategory : [{id: 0, title: 'ROOT', sub: true, disabled: false, checked: false}];
	}

	/**
	 *  Initialization the tree
	 */
	this.init = function()
	{
		if('tree' == menuType)
		{
			build(obj, defaultCategory);
		}
		else if('dropdown' == menuType)
		{
			buildDropdown();
		}
		else if('simple dropdown' == menuType)
		{
			buildSimpleDropDown();
		}

		/* Saving the random letter */
		obj.append('<input type="hidden" id="prefix_'+ conf.id +'" value="'+ idPrefix +'" />');
	};

	function buildSimpleDropDown()
	{
		var html = '';

		html += '<select name="category" id="' + simpleDropDownId + '">';
		html += defaultCategory;
		html += '</select>';

		obj.html(html);

		if(defaultIdCategory > 0)
		{
			$("#" + simpleDropDownId + " option[value='" + defaultIdCategory + "']").attr("selected", true);
		}
		
		$("#" + simpleDropDownId).change(callback);
	};

	function buildDropdown()
	{
		var html = '';

		html += '<input type="text" name="category" id="'+ dropdownInputId +'" />';
		html += '<ul id="'+ dropdownItemId +'" class="mcdropdown_menu">';
		html += defaultCategory;
		html += '</ul>';

		obj.html(html);

		var dd = $("#" + dropdownInputId).mcDropdown("#" + dropdownItemId, {
			allowParentSelect: true,
			delim: '/',
			valueAttr: 'rel',
			init: function()
			{
				var id = $('#category_id').val();

				if(0 == id)
				{
					$("div.mcdropdown input[type='text']").val();
				}
				else
				{
					this.setValue(id, true);
				}
			},
			select: dropDownCallback
		});
	};

	/**
	 * Building html
	 * 
	 * @param {Object} obj The container element
	 * @param {Array} categories The array of categories
	 */
	function build(obj, categories)
	{
		var html = '';

		html += '<ul class="tree">';

		for(var i = 0; i < categories.length; i++)
		{
			categories[i].locked = categories[i].locked ? categories[i].locked : false;
			categories[i].crossed = categories[i].crossed ? categories[i].crossed : false;
			categories[i].disabled = categories[i].disabled ? categories[i].disabled : intelli.inArray(categories[i].id, disabled);
			categories[i].checked = categories[i].checked ? categories[i].checked : intelli.inArray(categories[i].id, state)

			html += '<li>';
			html += '<div class="tree-col">';

			var locked = (categories[i].locked && !expandableLocked) ? false : true;
			var cls = (intelli.inArray(categories[i].id, expanded)) ? 'expanded' : 'collapsed';
			var crossedClass = categories[i].crossed ? ' crossed' : '';
			var crossedIdPref = categories[i].crossed ? '_crs' : '';

			categories[i].id += crossedIdPref;

			if(categories[i].sub && locked)
			{
				html += '<a href="#" id="c_'+ idPrefix + '_' + categories[i].id + '" class="'+ cls +'" onclick="return false;">';
				html += '<img class="tree-icon-collapsed" src="templates/'+ intelli.config.tmpl +'/img/sp.gif" id="icon_'+ idPrefix +'_'+ categories[i].id +'" />&nbsp;';
				html += '<img class="tree-folder-collapsed" src="templates/'+intelli.config.tmpl+'/img/sp.gif" id="folder_'+ idPrefix +'_'+categories[i].id +'" /></a>';
			}
			else
			{
				html += '<img class="tree-icon-space" src="templates/'+ intelli.config.tmpl +'/img/sp.gif" id="icon_'+ idPrefix +'_'+ categories[i].id +'" />&nbsp;';
				html += '<img class="tree-folder-collapsed" src="templates/'+intelli.config.tmpl+'/img/sp.gif" id="folder_'+ idPrefix +'_'+categories[i].id +'" />';
			}

			html += '<input ';
			html += 'type="'+ type +'" ';
			html += 'title="'+ categories[i].title +'" ';
			html += 'name="'+ treeName +'" ';
			html += 'value="'+ categories[i].id +'" ';
			
			if(categories[i].locked || categories[i].disabled)
			{
				html += 'disabled="disabled" ';
			}

			if(categories[i].checked)
			{
				html += 'checked="checked" ';
			}

			html += 'id="cat_'+ idPrefix + '_' + categories[i].id +'" />';

			html += '<label for="cat_'+ idPrefix + '_' + categories[i].id +'" style="cursor: pointer;">'+ categories[i].title +'</label>';

			if(categories[i].locked)
			{
				html += '<img class="tree-cat-locked" src="templates/'+intelli.config.tmpl+'/img/sp.gif" />';
			}

			if(categories[i].crossed)
			{
				html += '<img class="tree-cat-crossed" src="templates/'+intelli.config.tmpl+'/img/sp.gif" />';
			}

			html += '</div>';
			html += '<div id="categories_'+ idPrefix + '_' +categories[i].id +'" style="display:none;"></div>';
			html += '</li>';
		}

		html += '</ul>';

		obj.html(html);

		for(var i = 0; i < categories.length; i++)
		{
			if(intelli.inArray(categories[i].id, expanded))
			{
				var obj = $('#categories_' + idPrefix + '_' + categories[i].id);

				getChildren(categories[i].id);

				obj.attr('class', 'loaded').css('display', 'block');

				$('#icon_' + idPrefix + '_' + categories[i].id).attr('class', 'tree-icon-expanded');
				$('#folder_' + idPrefix + '_' + categories[i].id).attr('class', 'tree-folder-expanded');
			}

			$('#cat_' + idPrefix + '_' + categories[i].id).click(callback);

			$('#c_' + idPrefix + '_' + categories[i].id).click(function()
			{
				var obj = $(this);
				var id = obj.attr('id').replace('c_' + idPrefix + '_', '');
				var catDiv = $('#categories_' + idPrefix + '_' + id);

				if('collapsed' == obj.attr('class'))
				{
					obj.attr('class', 'expanded');

					if('loaded' == catDiv.attr('class'))
					{
						intelli.display(catDiv, 'show');
					}
					else
					{
						getChildren(id);
						
						catDiv.attr('class', 'loaded');
						intelli.display(catDiv);
					}

					$('#icon_' + idPrefix + '_' + id).attr('class', 'tree-icon-expanded');
					$('#folder_' + idPrefix + '_' + id).attr('class', 'tree-folder-expanded');
				}
				else
				{
					obj.attr('class', 'collapsed');
					intelli.display(catDiv, 'hide');

					$('#icon_' + idPrefix + '_' + id).attr('class', 'tree-icon-collapsed');
					$('#folder_' + idPrefix + '_' + id).attr('class', 'tree-folder-collapsed');
				}

				return false;
			});
		}
	};

	/**
	 * Insert subcategories by id category 
	 * 
	 * @param {String} id id category
	 * TODO: checking id param
	 */
	function getChildren(id)
	{
		var divCategories = $('#categories_' + idPrefix + '_' + id);

		var query_url = (url.indexOf('?') > 0) ? '&' : '?';

		id = id.replace('_crs', '');

		$.getJSON(url + query_url + 'id=' + id, function(categories)
		{
			if('null' != categories)
			{
				build(divCategories, categories);
			}
		});
	};
};

