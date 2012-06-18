(function()
{
	CKEDITOR.plugins.charcounter =
	{
	};
	
	var plugin = CKEDITOR.plugins.charcounter;

	CKEDITOR.plugins.add('charcounter',
	{
		init: function(editor)
		{
			function counter(field, countfield, maxlimit, decrease)
			{
				field.value = field.value.replace(/(<([^>]+)>)/ig, "");
				field.value = field.value.replace(/\n/ig, "");

				if(decrease)
				{
					if(field.value.length > maxlimit)
					{
						field.value = field.value.substring(0, maxlimit);
					}
					else
					{
						countfield.value = maxlimit - field.value.length;
					}
				}
				else
				{
					countfield.value = field.value.length;
				}
			}

			function count_event(evt)
			{
				var currentLength = editor.getData().replace(/(<([^>]+)>)/ig,"").replace(/\n/ig, "").length;
				var maximumLength = editor.config.max_length;
				
				if(decrease)
				{
					if(currentLength >= maximumLength)
					{
						editor.execCommand( 'undo' );
					}
					
					counter_form.value = maximumLength - currentLength;
				}
				else
				{
					counter_form.value = currentLength;
				}
			}
			if('undefined' == typeof editor.config.counter)
			{
				return false;
			}

			var counter_form = document.getElementById(editor.config.counter);
			var locked;
			var decrease;

			if(!counter_form)
			{
				return false;
			}
			
			var maxLength = editor.config.max_length;
			var minLength = editor.config.min_length;
			
			if((minLength >= 0 && maxLength >= 0) || (isNaN(minLength) && maxLength >= 0))
			{
				decrease = true;
			}
	
			if((minLength >= 0 && isNaN(maxLength)))
			{
				decrease = false;
			}

			// init counter form
			counter_form.readOnly = true;
			counter_form.size = '3';
			counter_form.maxLength = '3';

			counter({value: editor.getData()}, counter_form, editor.config.max_length, decrease);
			
			editor.on("instanceReady", function()
			{
				this.document.on('paste', count_event);
				this.document.on('keydown', count_event);
				this.document.on('keyup', count_event);
			});
		}
	});
})();
