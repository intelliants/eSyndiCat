{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && ($smarty.get.do eq 'add' || $smarty.get.do eq 'edit')}
	{include file="box-header.tpl" title=$gTitle}
	<form action="controller.php?file=listing-fields&amp;do={$smarty.get.do}{if $smarty.get.do eq 'edit'}&amp;id={$smarty.get.id}{/if}" method="post">
	{preventCsrf}
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="150"><strong>{$esynI18N.name}:</strong></td>
		<td>
			<input type="text" name="name" size="24" class="common" value="{if isset($field.name)}{$field.name}{elseif isset($smarty.post.name)}{$smarty.post.name|escape:"html"}{/if}" {if isset($smarty.get.do) && $smarty.get.do eq 'edit'}readonly="readonly"{/if} />
		</td>
	</tr>

	{foreach from=$langs key=code item=lang}
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;<strong>{$lang}&nbsp;{$esynI18N.title}:</strong></td>
		<td>
			{if isset($field.name)}
				{assign var="page_title" value=$field_titles.$code}
			{/if}
			<input type="text" name="title[{$code}]" size="24" class="common" value="{if isset($page_title)}{$page_title}{elseif isset($smarty.post.title.$code)}{$smarty.post.title.$code}{/if}" />
		</td>
	</tr>
	{/foreach}

	<tr>
		<td><strong>{$esynI18N.show_on_pages}:</strong></td>
		<td>
			<input type="checkbox" name="pages[]" value="suggest" id="p1" {if in_array("suggest", $pages)}checked="checked"{/if} />
				<label for="p1">{$esynI18N.suggest_listing}</label>
			<input type="checkbox" name="pages[]" value="edit" id="p2" {if in_array("edit", $pages)}checked="checked"{/if} />
				<label for="p2">{$esynI18N.edit_listing}</label>
			<input type="checkbox" name="pages[]" value="view" id="p3" {if in_array("view", $pages)}checked="checked"{/if} />
				<label for="p3">{$esynI18N.listing_details}</label>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.tooltip}:</strong></td>
		<td>
			<textarea name="tooltip" rows="3" cols="20">{if isset($field.tooltip)}{$field.tooltip}{elseif isset($smarty.post.tooltip)}{$smarty.post.tooltip|escape:"html"}{/if}</textarea>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.wyswyg_editor}:</strong></td>
		<td>
			<input type="radio" name="editor" id="e1" value="1" {if isset($field.editor) && $field.editor eq '1'}checked="checked"{elseif isset($smarty.post.editor) && $smarty.post.editor eq '1'}checked="checked"{/if} disabled="disabled" /><label for="e1">{$esynI18N.yes}</label>
			<input type="radio" name="editor" id="e2" value="0" {if isset($field.editor) && $field.editor eq '0'}checked="checked"{elseif isset($smarty.post.editor) && $smarty.post.editor eq '0'}checked="checked"{elseif !isset($field) && !$smarty.post}checked="checked"{/if} disabled="disabled" /><label for="e2">{$esynI18N.no}</label>
		</td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.required_field}:</strong></td>
		<td>
			<input type="radio" name="required" id="r1" value="1" {if isset($field.required) && $field.required eq '1'}checked="checked"{elseif isset($smarty.post.required) && $smarty.post.required eq '1'}checked="checked"{/if} /><label for="r1">{$esynI18N.yes}</label>
			<input type="radio" name="required" id="r2" value="0" {if isset($field.required) && $field.required eq '0'}checked="checked"{elseif isset($smarty.post.required) && $smarty.post.required eq '0'}checked="checked"{elseif !isset($field) && !$smarty.post}checked="checked"{/if} /><label for="r2">{$esynI18N.no}</label>
		</td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.visible_for_admin}:</strong></td>
		<td>
			<input type="radio" name="adminonly" id="adminyes" value="1" {if isset($field.adminonly) && $field.adminonly eq '1'}checked="checked"{elseif isset($smarty.post.adminonly) && $smarty.post.adminonly eq '1'}checked="checked"{/if} /><label for="adminyes">{$esynI18N.yes}</label>
			<input type="radio" name="adminonly" id="adminno" value="0" {if isset($field.adminonly) && $field.adminonly eq '0'}checked="checked"{elseif isset($smarty.post.adminonly) && $smarty.post.adminonly eq '0'}checked="checked"{elseif !isset($field) && !$smarty.post}checked="checked"{/if} /><label for="adminno">{$esynI18N.no}</label>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.searchable}:</strong></td>
	<td>
		<input type="radio" name="searchable" id="searchable1" value="1" {if isset($field.searchable) && $field.searchable eq '1'}checked="checked"{elseif isset($smarty.post.searchable) && $smarty.post.searchable eq '1'}checked="checked"{/if} /><label for="searchable1">{$esynI18N.yes}</label>
		<input type="radio" name="searchable" id="searchable2" value="0" {if isset($field.searchable) && $field.searchable eq '0'}checked="checked"{elseif isset($smarty.post.searchable) && $smarty.post.searchable eq '0'}checked="checked"{elseif !isset($field) && !$smarty.post}checked="checked"{/if} /><label for="searchable2">{$esynI18N.no}</label>
			
		<div id="search_section" style="display: none;">
			<strong>{$esynI18N.section}:</strong>
			<select name="section_key">
				<option value="">{$esynI18N._select_}</option>
				{if $sections}
					{foreach from=$sections item=section}
						<option value="{$section.key}" {if isset($field.section_key) && $field.section_key eq $section.key}selected="selected"{/if}>{$section.title}</option>
					{/foreach}
				{/if}
			</select>
		</div>
		
		<div id="fulltext_search_zone" style="display: none;">
			<strong>{$esynI18N.fulltext_search}:</strong>
			<input type="checkbox" name="make_fulltext" id="fulltext_search" value="yes" />
		</div>			
	</td>
	</tr>
	
	{if $plans}
		<tr>
			<td><strong>{$esynI18N.bind_to_plans}:</strong></td>
			<td>
				{foreach from=$plans item=plan}
					<input name="plans[]" type="checkbox" id="plan_{$plan.id}" value="{$plan.id}" {if isset($field_plans) && in_array($plan.id, $field_plans)}checked="checked"{elseif isset($smarty.post.plans) && in_array($plan.id, $smarty.post.plans)}checked="checked"{/if}/>
					{assign var="lang" value=$plan.lang}
					<label for="plan_{$plan.id}">{$plan.title}&nbsp;({$langs.$lang})</label><br />
				{/foreach}
			</td>
		</tr>
	{/if}

	<tr>
		<td><strong>{$esynI18N.bind_to_categories}:</strong></td>
		<td>
			<div id="tree"></div>
			<input type="checkbox" name="recursive" id="recursive" value="1" {if isset($field.recursive) && $field.recursive eq '1'}checked="checked"{elseif isset($smarty.post.recursive) && $smarty.post.recursive eq '1'}checked="checked"{/if}/><label for="recursive">&nbsp;{$esynI18N.include_subcats}</label>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.field_type}:</strong></td>
		<td>
			<select name="field_type" id="type">
				<option value="">{$esynI18N._select_}</option>
				{foreach from=$field_types key=key item=type}
					<option value="{$key}"{if (isset($field.type) && $field.type eq $key) || (isset($smarty.post.field_type) && $smarty.post.field_type eq $key)}selected="selected"{/if}>{$type}</option>
				{/foreach}
			</select>
		</td>
	</tr>

	</table>

	<div id="text" style="display: none;">
		<table cellspacing="0" cellpadding="0" width="100%" class="striped">
		<tr>
			<td width="150"><strong>{$esynI18N.field_length}:</strong></td>
			<td><input type="text" name="length" size="24" class="common numeric" value="{if isset($field.length)}{$field.length}{elseif isset($smarty.post.length)}{$smarty.post.length}{/if}" /> {$esynI18N.digit_only}</td>
		</tr>
		<tr>
			<td><strong>{$esynI18N.field_default}:</strong></td>
			<td><input type="text" name="text_default" size="24" class="common" value="{if isset($field.default)}{$field.default}{elseif isset($smarty.post.text_default)}{$smarty.post.text_default}{/if}" /></td>
		</tr>
		</table>
	</div>

	<div id="number" style="display: none;">
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td colspan="2">
				{if isset($smarty.post._values)}
					{foreach from=$smarty.post._values key=key item=value}
						<div style="margin: 5px 0;">
						<span style="font-weight:bold; margin-right: 68px;">{$esynI18N.item_value}:</span>
							<input class="common numeric" type="text" name="_values[]" value="{$value}" size="10" />
						<span style="font-weight:bold; margin-left: 38px;">{$esynI18N.title}:</span>
							<input class="common" type="text" name="_titles[]" size="10" value="{$smarty.post._titles.$key}" />
							<a href="remove_item" class="actions_removeItem">{$esynI18N.remove}</a>
						</div>
					{/foreach}
				{/if}
				<a href="#" id="add_two_items"><strong>{$esynI18N.add_item_value}</strong></a>
			</td>
		</tr>
		</table>
	</div>
	
	<div id="textarea" style="display: none;">
		{if isset($field.length) && $field.type eq 'textarea' && !empty($field.length)}
			{assign var="lengths" value=','|explode:$field.length} 
			{assign var="min_length" value=$lengths[0]}
			{assign var="max_length" value=$lengths[1]}
		{elseif isset($smarty.post)}
			{if isset($smarty.post.min_length)}
				{assign var="min_length" value=$smarty.post.min_length}
			{/if}
			{if isset($smarty.post.max_length)}
				{assign var="max_length" value=$smarty.post.max_length}
			{/if}
		{/if}
		<table cellspacing="0" cellpadding="0" width="100%" class="striped">
		<tr>
			<td width="150"><strong>{$esynI18N.field_min_length}:</strong></td>
			<td><input type="text" class="common numeric" name="min_length" size="24" value="{if isset($min_length)}{$min_length}{/if}" /></td>
		</tr>
		<tr>
			<td><strong>{$esynI18N.field_max_length}:</strong></td>
			<td><input type="text" class="common numeric" name="max_length" size="24" value="{if isset($max_length)}{$max_length}{/if}" /></td>
		</tr>
		</table>
	</div>

	<div id="storage" style="display: none;">
		{if !is_writeable($smarty.const.ESYN_HOME|cat:$smarty.const.ESYN_DS|cat:'uploads')}
			<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i>{$esynI18N.upload_writable_permission}</i></div>							
		{else}
			<label for="file_types">{$esynI18N.file_types}</label><br />
			<textarea rows="3" cols="20" class="common" id="file_types" name="file_types" style="width:500px;height:40px">{if isset($field.file_types)}{$field.file_types}{elseif isset($smarty.post.file_types)}{$smarty.post.file_types}{/if}</textarea>
		{/if}
	</div>
	
	<div id="image" style="display: none;">
		{if !is_writeable($smarty.const.ESYN_HOME|cat:$smarty.const.ESYN_DS|cat:'uploads')}
			<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i>{$esynI18N.upload_writable_permission}</i></div>							
		{else}
			<table cellspacing="0" cellpadding="0" width="100%" class="striped">
			<tr>
				<td class="tip-header"><strong>{$esynI18N.instead_thumbnail}:</strong></td>
				{if isset($field.instead_thumbnail)}
					{assign var="instead_thumbnail_value" value=$field.instead_thumbnail}
				{elseif isset($smarty.post.instead_thumbnail)}
					{assign var="instead_thumbnail_value" value=$smarty.post.instead_thumbnail}
				{/if}
				<td>
					<input type="radio" name="instead_thumbnail" id="instead_thumbnail_yes" value="1" {if isset($field.instead_thumbnail) && $field.instead_thumbnail eq '1'}checked="checked"{elseif isset($smarty.post.instead_thumbnail) && $smarty.post.instead_thumbnail eq '1'}checked="checked"{/if} /><label for="instead_thumbnail_yes">{$esynI18N.yes}</label>
					<input type="radio" name="instead_thumbnail" id="instead_thumbnail_no" value="0" {if isset($field.instead_thumbnail) && $field.instead_thumbnail eq '0'}checked="checked"{elseif isset($smarty.post.instead_thumbnail) && $smarty.post.instead_thumbnail eq '0'}checked="checked"{elseif !isset($field) && !$smarty.post}checked="checked"{/if} /><label for="instead_thumbnail_no">{$esynI18N.no}</label>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>{$esynI18N.file_prefix}:</strong></td>
				<td><input type="text" name="file_prefix" size="24" class="common" value="{if isset($field.file_prefix)}{$field.file_prefix}{elseif isset($smarty.post.file_prefix)}{$smarty.post.file_prefix}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.image_width}:</strong></td>
				<td><input type="text" name="image_width" size="24" class="common" value="{if isset($field.image_width)}{$field.image_width}{elseif isset($smarty.post.image_width)}{$smarty.post.image_width}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.image_height}:</strong></td>
				<td><input type="text" name="image_height" size="24" class="common" value="{if isset($field.image_height)}{$field.image_height}{elseif isset($smarty.post.image_height)}{$smarty.post.image_height}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.thumb_width}:</strong></td>
				<td><input type="text" name="thumb_width" size="24" class="common" value="{if isset($field.thumb_width)}{$field.thumb_width}{elseif isset($smarty.post.thumb_width)}{$smarty.post.thumb_width}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.thumb_height}:</strong></td>
				<td><input type="text" name="thumb_height" size="24" class="common" value="{if isset($field.thumb_height)}{$field.thumb_height}{elseif isset($smarty.post.thumb_height)}{$smarty.post.thumb_height}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.resize_mode}:</strong></td>
				<td>
					<select name="resize_mode">
						<option value="1001" {if isset($field.resize_mode) && $field.resize_mode eq '1001'}selected="selected"{elseif isset($smarty.post.resize_mode) && $smarty.post.resize_mode eq '1001'}selected="selected"{/if}> {$esynI18N.proportional} </option>
						<option value="1002" {if isset($field.resize_mode) && $field.resize_mode eq '1002'}selected="selected"{elseif isset($smarty.post.resize_mode) && $smarty.post.resize_mode eq '1002'}selected="selected"{/if}> {$esynI18N.stretch} </option>
						<option value="1003" {if isset($field.resize_mode) && $field.resize_mode eq '1003'}selected="selected"{elseif isset($smarty.post.resize_mode) && $smarty.post.resize_mode eq '1003'}selected="selected"{/if}> {$esynI18N.fit} </option>
					</select>
					<span id="resize_mode_tip_1001" class="option_tip" style="display: none;">{$esynI18N.proportional_tip}</span>
					<span id="resize_mode_tip_1002" class="option_tip" style="display: none;">{$esynI18N.stretch_tip}</span>
					<span id="resize_mode_tip_1003" class="option_tip" style="display: none;">{$esynI18N.fit_tip}</span>
				</td>
			</tr>

			</table>
		{/if}
	</div>

	<div id="multiple" style="display: none;">
		<div id="textany_meta_container">
			<table cellspacing="0" width="100%" class="striped">
				{foreach from=$langs key=code item=lang}
				<tr>
					<td width="150">
						<label for="anyMeta{$code}"><strong>{$lang}:&nbsp;{$esynI18N.title_for_any}:</strong></label>
					</td>
					<td>
						<input type="text" name="any_meta[{$code}]" id="anyMeta{$code}" class="common" value="{if isset($field.any_meta.$code)}{$field.any_meta.$code}{elseif isset($smarty.post.any_meta.$code)}{$smarty.post.any_meta.$code}{/if}" disabled="disabled" />
					</td>
				</tr>
				{/foreach}
			</table>
		</div>
	
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td width="150"><strong>{$esynI18N.show_in_search_as}:</strong></td>
			<td>
				<select name="show_as" id="showAs" disabled="disabled">
					<option value="checkbox" {if isset($field.show_as) && $field.show_as eq 'checkbox'}selected="selected"{elseif isset($smarty.post.show_as) && $smarty.post.show_as eq 'checkbox'}selected="selected"{/if}>{$esynI18N.checkboxes}</option>
					<option value="radio" {if isset($field.show_as) && $field.show_as eq 'radio'}selected="selected"{elseif isset($smarty.post.show_as) && $smarty.post.show_as eq 'radio'}selected="selected"{/if}>{$esynI18N.radios}</option>
					<option value="combo" {if isset($field.show_as) && $field.show_as eq 'combo'}selected="selected"{elseif isset($smarty.post.show_as) && $smarty.post.show_as eq 'combo'}selected="selected"{/if}>{$esynI18N.dropdown}</option>
				</select>
			</td>
		</tr>
		</table>
	
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td colspan="2" class="td_items">
				{if isset($field_values) && !empty($field_values)}
					{foreach from=$field_values key=key item=value}
						<div style="margin: 10px 0 10px 0;">
							{foreach from=$langs key=key item=lang}
								<br />
								
								<a href="#" style="visibility: hidden;" class="arrow_up"><img src="templates/default/img/arrow_up.png" alt="" title="" style="float: left; margin-right: 2px;" /></a>
								<a href="#" style="visibility: hidden;" class="arrow_down"><img src="templates/default/img/arrow_down.png" alt="" title="" style="float: left; margin-right: 2px;" /></a>
								
								<input type="text" class="common" name="lang_values[{$key}][]" size="25" value="{$value.$key}" />
								<span style="margin-left: 30px;">{$lang}&nbsp;{$esynI18N.item_value}</span>
								
								{if $key eq $config.lang}
									&nbsp;|&nbsp;{$esynI18N.field_default}&nbsp;(&nbsp;
									<a href="set_default" class="actions_setDefault">{$esynI18N.set_default}</a>&nbsp;|&nbsp;
									<a href="remove_default" class="actions_removeDefault">{$esynI18N.remove}</a>&nbsp;)&nbsp;|&nbsp;
									<a href="remove_item" class="actions_removeItem">{$esynI18N.remove}</a>
								{/if}
							{/foreach}
						</div>
					{/foreach}
				{/if}
				<a href="add_item" id="add_item" onclick="return false;"><strong>{$esynI18N.add_item_value}</strong></a>
			</td>
		</tr>
		<tr>
			<td width="150"><strong>{$esynI18N.field_default}:</strong></td>
			<td><input type="text" readonly="readonly" name="multiple_default" id="multiple_default" size="45" class="common" value="{if isset($field.default)}{$field.default}{elseif isset($smarty.post.multiple_default)}{$smarty.post.multiple_default}{/if}" /></td>
		</tr>
		</table>
	</div>

	<div id="pictures" style="display: none;">
		{if !is_writeable($smarty.const.ESYN_HOME|cat:$smarty.const.ESYN_DS|cat:'uploads')}
			<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i>{$esynI18N.upload_writable_permission}</i></div>							
		{else}
			<table cellspacing="0" cellpadding="0" width="100%" class="striped">
			<tr>
				<td width="150"><strong>{$esynI18N.max_num_images}:</strong></td>
				<td><input type="text" name="pic_max_images" size="24" class="numeric common" value="{if isset($field.length)}{$field.length}{elseif isset($smarty.post.pic_max_images)}{$smarty.post.pic_max_images}{elseif !isset($field) && empty($smarty.post)}5{/if}" /></td>
			</tr>
			<tr>
				<td width="150"><strong>{$esynI18N.file_prefix}:</strong></td>
				<td><input type="text" name="pic_file_prefix" size="24" class="common" value="{if isset($field.file_prefix)}{$field.file_prefix}{elseif isset($smarty.post.pic_file_prefix)}{$smarty.post.pic_file_prefix}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.image_width}:</strong></td>
				<td><input type="text" name="pic_image_width" size="24" class="common" value="{if isset($field.image_width)}{$field.image_width}{elseif isset($smarty.post.pic_image_width)}{$smarty.post.pic_image_width}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.image_height}:</strong></td>
				<td><input type="text" name="pic_image_height" size="24" class="common" value="{if isset($field.image_height)}{$field.image_height}{elseif isset($smarty.post.pic_image_height)}{$smarty.post.pic_image_height}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.thumb_width}:</strong></td>
				<td><input type="text" name="pic_thumb_width" size="24" class="common" value="{if isset($field.thumb_width)}{$field.thumb_width}{elseif isset($smarty.post.pic_thumb_width)}{$smarty.post.pic_thumb_width}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.thumb_height}:</strong></td>
				<td><input type="text" name="pic_thumb_height" size="24" class="common" value="{if isset($field.thumb_height)}{$field.thumb_height}{elseif isset($smarty.post.pic_thumb_height)}{$smarty.post.pic_thumb_height}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.resize_mode}:</strong></td>
				<td>
					<select name="pic_resize_mode">
						<option value="1001" {if isset($field.resize_mode) && $field.resize_mode eq '1001'}selected="selected"{elseif isset($smarty.post.pic_resize_mode) && $smarty.post.pic_resize_mode eq '1001'}selected="selected"{/if}> {$esynI18N.proportional} </option>
						<option value="1002" {if isset($field.resize_mode) && $field.resize_mode eq '1002'}selected="selected"{elseif isset($smarty.post.pic_resize_mode) && $smarty.post.pic_resize_mode eq '1002'}selected="selected"{/if}> {$esynI18N.stretch} </option>
						<option value="1003" {if isset($field.resize_mode) && $field.resize_mode eq '1003'}selected="selected"{elseif isset($smarty.post.pic_resize_mode) && $smarty.post.pic_resize_mode eq '1003'}selected="selected"{/if}> {$esynI18N.fit} </option>
					</select>
					<span id="pic_resize_mode_tip_1001" class="option_tip" style="display: none;">{$esynI18N.proportional_tip}</span>
					<span id="pic_resize_mode_tip_1002" class="option_tip" style="display: none;">{$esynI18N.stretch_tip}</span>
					<span id="pic_resize_mode_tip_1003" class="option_tip" style="display: none;">{$esynI18N.fit_tip}</span>
				</td>
			</tr>

			</table>
		{/if}
	</div>

	<table cellspacing="0" width="100%" class="striped">
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="save" class="common" value="{if $smarty.get.do eq 'edit'}{$esynI18N.save_changes}{else}{$esynI18N.add}{/if}" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="old_name" value="{if isset($field.name)}{$field.name}{/if}" />
	<input type="hidden" name="do" value="{if isset($smarty.get.do)}{$smarty.get.do}{/if}" />
	<input type="hidden" name="categories" id="categories" value="{if isset($field_categories)}{$field_categories}{elseif isset($smarty.post.categories)}{$smarty.post.categories}{/if}" />
	<input type="hidden" name="categories_parents" id="categories_parents" value="{if isset($field_categories_parents)}{$field_categories_parents}{elseif isset($smarty.post.categories_parents)}{$smarty.post.categories_parents}{/if}" />
	</form>

	<div style="margin: 10px 0 10px 0; display: none;" id="value_item">
		{foreach from=$langs key=key item=lang name=lang}
			<br />
			
			<a href="#" style="visibility: hidden;" class="arrow_up"><img src="templates/default/img/arrow_up.png" alt="" title="" style="float: left; margin-right: 2px;" /></a>
			<a href="#" style="visibility: hidden;" class="arrow_down"><img src="templates/default/img/arrow_down.png" alt="" title="" style="float: left; margin-right: 2px;" /></a>

			<input type="text" class="common" name="lang_values[{$key}][]" size="25" value="" />
			<span style="margin-left: 30px;">{$lang}&nbsp;{$esynI18N.item_value}</span>
			
			{if $key eq $config.lang}
				&nbsp;|&nbsp;{$esynI18N.field_default}&nbsp;(&nbsp;
				<a href="set_default" class="actions_setDefault">{$esynI18N.set_default}</a>&nbsp;|&nbsp;
				<a href="remove_default" class="actions_removeDefault">{$esynI18N.remove}</a>&nbsp;)&nbsp;|&nbsp;
				<a href="remove_item" class="actions_removeItem" onclick="return false;">{$esynI18N.remove}</a>
			{/if}
		{/foreach}
	</div>

	<div style="margin: 5px 0; display: none" id="value_two_items">
		<span style="font-weight:bold; margin-right: 68px;">{$esynI18N.item_value}:</span>
		<input class="common numeric" type="text" name="_values[]" size="10" />
		<span style="font-weight:bold; margin-left: 38px;">{$esynI18N.title}:</span>
		<input type="text" class="common" name="_titles[]" size="10" />
		<a href="remove_item" class="actions_removeItem">{$esynI18N.remove}</a>
	</div>

	{include file="box-footer.tpl" class="box"}
{else}
	<div id="box_fields" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/jquery/plugins/jquery.numeric, js/admin/listing-fields"}

{include file="footer.tpl"}
