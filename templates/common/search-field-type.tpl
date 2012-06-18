{assign var="type" value=$variable.type}
{if 
	(($type eq 'checkbox' or $type eq 'combo'  or $type eq 'radio') and $variable.show_as eq 'checkbox')
}
		{assign var="varname" value=$variable.name}
      	{assign var="name" value="field_"|cat:$varname}
	      	<h3>{$lang.$name}</h3>
	      	<div class="checkboxGroup">
						<div>
							{html_checkboxes name=$variable.name options=$variable.values separator="<br />" grouping=5}
						</div>
					</div>
					<br style="clear:both" />
{elseif $variable.show_as eq 'radio'}
      	{assign var="name" value="field_"|cat:$variable.name}
	     	<h3>{$lang.$name}</h3>
	     		{html_radios name=$variable.name options=$variable.values separator="<br />" grouping=5}
				<br />
{elseif $variable.show_as eq 'combo'}
				<div style="margin:5px;">
      	{assign var="name" value="field_"|cat:$variable.name}
	      	<label for="{$variable.name}_domid"> {$lang.$name}: </label>
	      	<select name="{$variable.name}" id="{$variable.name}_domid">
						<option value="_doesnt_selected_">{assign var="any_meta" value=$name|cat:"_any_meta"}
							{if $lang.$any_meta}{$lang.$any_meta}
							{else}{$lang._select_}{/if}
						</option>
	      		{html_options options=$variable.values}
	      	</select>
				</div>
{elseif $type eq 'storage' or $type eq 'image'}
				{assign var="varname" value=$variable.name}
      	{assign var="name" value="field_"|cat:$varname}
	      	<h3>{$lang.contains} "{$lang.$name}"</h3>
		      	<input class="storage" type="radio" id="hasFile{$variable.name}" name="{$variable.name}[has]" value="y" />
						<label for="hasFile{$variable.name}">Yes</label>

		      	<input class="storage" type="radio" id="doesntHaveFile{$variable.name}" name="{$variable.name}[has]" value="n" />
						<label for="doesntHaveFile{$variable.name}">No</label>
				<br />
{elseif $type eq 'number'}
      	{assign var="name" value="field_"|cat:$variable.name}
	     	<h3>{$lang.$name}</h3>
	     	{if $variable.ranges}
	     		<label for="{$variable.name}_from_domid"><span style="font-size:11px;">{$lang.from}</span>&nbsp;</label>
	     		<select name="_from[{$variable.name}]" id="{$variable.name}_from_domid">
						<option value="_doesnt_selected_">{assign var="any_meta" value=$name|cat:"_any_meta"}
							{if $lang.$any_meta}{$lang.$any_meta}
							{else}{$lang._select_}{/if}
						</option>
	      		{html_options options=$variable.ranges}
	      	</select>

	     		<label for="{$variable.name}_to_domid"><span style="font-size:11px;"> {$lang.to}</span>&nbsp;</label>
	     		<select name="_to[{$variable.name}]" id="{$variable.name}_to_domid">
						<option value="_doesnt_selected_">{assign var="any_meta" value=$name|cat:"_any_meta"}
							{if $lang.$any_meta}{$lang.$any_meta}
							{else}{$lang._select_}{/if}
						</option>
	      		{html_options options=$variable.ranges}
	      	</select>
	     	{else}
      		<label for="{$variable.name}_{$name}_from_domid"><span style="font-size:11px;">{$lang.from}</span>&nbsp;</label>
		     	<input class="numeric" type="text" class="text" id="{$variable.name}_{$name}_from_domid" name="_from[{$variable.name}]" value="" size="7" />

					<label for="{$variable.name}_{$name}_to_domid">&nbsp;<span style="font-size:11px;">{$lang.to}</span>&nbsp;</label>
		     	<input class="numeric" type="text" class="text" id="{$variable.name}_{$name}_to_domid" name="_to[{$variable.name}]" value="" size="10" />
		    {/if}
	<br />
{/if}
