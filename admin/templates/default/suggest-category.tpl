{include file="header.tpl" css="js/ext/plugins/chooser/css/chooser"}

{include file="box-header.tpl" title=$gTitle}
<form action="controller.php?file=suggest-category{if isset($smarty.get.id)}&amp;id={$smarty.get.id}{/if}{if isset($smarty.get.do)}&amp;do={$smarty.get.do}{/if}" method="post">
{preventCsrf}
<table cellspacing="0" cellpadding="0" width="100%" class="striped">

{if isset($parent) && !empty($parent)}
	<tr>
		<td width="200"><strong>{$esynI18N.parent_category}:</strong></td>
		<td>
			<span id="parent_category_title_container"><strong>{if isset($parent) && !empty($parent)}<a href="controller.php?file=browse&amp;id={$parent.id}">{$parent.title}</a>{else}<a href="controller.php?file=browse">{$category.title}</a>{/if}</strong>&nbsp;|&nbsp;<a href="#" id="change_category">{$esynI18N.change}...</a></span>
			<input type="hidden" id="parent_id" name="parent_id" value="{if isset($parent) && !empty($parent)}{$parent.id}{/if}" />
		</td>
	</tr>
{/if}

<tr>
	<td width="200"><strong>{$esynI18N.title}:</strong></td>
	<td><input type="text" name="title" size="30" maxlength="150" class="common" value="{if isset($category.title) && isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$category.title}{elseif isset($smarty.post.title)}{$smarty.post.title|escape:"html"}{/if}" /></td>
</tr>

<tr>
	<td><strong>{$esynI18N.page_title}:</strong></td>
	<td><input type="text" name="page_title" size="30" maxlength="150" class="common" value="{if isset($category.page_title) && isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$category.page_title}{elseif isset($smarty.post.page_title)}{$smarty.post.page_title|escape:"html"}{/if}" /></td>
</tr>

{if isset($parent) && !empty($parent)}
	<tr>
		<td><strong>{$esynI18N.path}:</strong></td>
		<td>
			<input type="text" name="path" size="30" maxlength="150" class="common" style="float: left;" value="{if isset($category.path) && isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$category.path}{elseif isset($smarty.post.path)}{$smarty.post.path|escape:"html"}{/if}" />&nbsp;
			<div style="float: left; display: none; margin-left: 3px; padding: 4px;" id="category_url_box"><span>{$esynI18N.category_url_will_be}:&nbsp;</span><span id="category_url" style="padding: 3px; margin: 0; background: #FFE269;"></span></div>
		</td>
	</tr>
{/if}

<tr>
	<td><strong>{$esynI18N.description}:</strong></td>
	<td>
		<textarea name="description" id="description" cols="43" rows="8">{if isset($category.description) && isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$category.description}{elseif isset($smarty.post.description)}{$smarty.post.description}{/if}</textarea>
	</td>
</tr>

<tr>
	<td><strong>{$esynI18N.meta_description}:</strong></td>
	<td>
		<textarea name="meta_description" cols="43" rows="8" class="common">{if isset($category.meta_description) && isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$category.meta_description}{elseif isset($smarty.post.meta_description)}{$smarty.post.meta_description|escape:"html"}{/if}</textarea>
	</td>
</tr>

<tr>
	<td><strong>{$esynI18N.meta_keywords}:</strong></td>
	<td><input type="text" name="meta_keywords" size="60" maxlength="150" class="common" value="{if isset($category.meta_keywords) && isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$category.meta_keywords}{elseif isset($smarty.post.meta_keywords)}{$smarty.post.meta_keywords|escape:"html"}{/if}" /></td>
</tr>

<tr>
	<td class="first"><strong>{$esynI18N.enable_no_follow}:</strong></td>
	<td>{html_radio_switcher value=$category.no_follow|default:0 name="no_follow"}</td>
</tr>

<tr>
	<td class="first"><strong>{$esynI18N.lock_category}:</strong></td>
	<td>
		{html_radio_switcher value=$category.locked|default:0 name="locked"}
		<div style="padding: 5px 0 0 100px;"><label><input type="checkbox" name="subcategories" />&nbsp;{$esynI18N.include_subcats}</label></div>
	</td>
</tr>

<tr>
	<td class="tip-header first" id="tip-header-hide_category"><strong>{$esynI18N.hide_category}:</strong></td>
	<td>{html_radio_switcher value=$category.hidden|default:0 name="hidden"}</td>
</tr>

<tr>
	<td class="first"><strong>{$esynI18N.unique_category_template}:</strong></td>
	<td>{html_radio_switcher value=$category.unique_tpl|default:0 name="unique_tpl"}</td>
</tr>

<tr>
	<td class="first"><strong>{$esynI18N.number_of_columns}:</strong></td>
	<td>
		<span style="float: left;">
			<input type="radio" name="num_cols_type" value="1" {if isset($category.num_cols) && isset($smarty.get.do) && $smarty.get.do eq 'edit' && $category.num_cols eq '0'}checked="checked"{elseif isset($smarty.post.num_cols_type) && $smarty.post.num_cols_type eq '1'}checked="checked"{elseif !$smarty.post}checked="checked"{/if} id="nc1" /><label for="nc1">&nbsp;{$esynI18N.default} ( {$config.num_categories_cols} )</label>
			<input type="radio" name="num_cols_type" value="0" {if isset($category.num_cols) && isset($smarty.get.do) && $smarty.get.do eq 'edit' && $category.num_cols neq '0'}checked="checked"{elseif isset($smarty.post.num_cols_type) && $smarty.post.num_cols_type eq '0'}checked="checked"{/if} id="nc2" /><label for="nc2">&nbsp;{$esynI18N.custom}</label>&nbsp;&nbsp;&nbsp;
		</span>
		<span id="nc" style="display: none;"><input class="common numeric" type="text" name="num_cols" size="5" value="{if isset($category.num_cols)}{$category.num_cols}{elseif isset($smarty.post.num_cols)}{$smarty.post.num_cols}{elseif empty($category)}{$config.num_categories_cols}{/if}" style="text-align: right;" />&nbsp;{$esynI18N.number_of_cols_tip}</span>
	</td>
</tr>

{if $config.neighbour}
<tr>
	<td class="first"><strong>{$esynI18N.number_of_neighbours}:</strong></td>
	<td>
		<span style="float: left;">
			<input type="radio" name="num_neighbours_type" value="-1" {if isset($category.num_neighbours) && isset($smarty.get.do) && $smarty.get.do eq 'edit' && $category.num_neighbours eq '0'}checked="checked"{elseif isset($smarty.post.num_neighbours_type) && $smarty.post.num_neighbours_type eq '-1'}checked="checked"{elseif !$smarty.post}checked="checked"{/if} id="nnc0" /><label for="nnc0">&nbsp;{$esynI18N.do_not_display_neighbours}</label>
			<input type="radio" name="num_neighbours_type" value="0" {if isset($category.num_neighbours) && isset($smarty.get.do) && $smarty.get.do eq 'edit' && $category.num_neighbours eq '-1'}checked="checked"{elseif isset($smarty.post.num_neighbours_type) && $smarty.post.num_neighbours_type eq '0'}checked="checked"{/if} id="nnc1" /><label for="nnc1">&nbsp;{$esynI18N.all_neighbours}</label>
			<input type="radio" name="num_neighbours_type" value="1" {if isset($category.num_neighbours) && isset($smarty.get.do) && $smarty.get.do eq 'edit' && $category.num_neighbours gt 0}checked="checked"{elseif isset($smarty.post.num_neighbours_type) && $smarty.post.num_neighbours_type eq '1'}checked="checked"{/if} id="nnc2"/><label for="nnc2">&nbsp;{$esynI18N.custom}</label>&nbsp;&nbsp;&nbsp;
		</span>
		<span id="nnc" style="display: none;">
			<input class="common numeric" type="text" name="num_neighbours" size="5" value="{if isset($category.num_neighbours)}{$category.num_neighbours}{elseif isset($smarty.post.num_neighbours)}{$smarty.post.num_neighbours}{/if}" style="text-align: right;" />&nbsp;{$esynI18N.number_of_neigh_tip}</span>
	</td>
</tr>
{/if}

<tr>
	<td><strong>{$esynI18N.confirmation}:</strong></td>
	<td>
		<input type="radio" name="confirmation" value="1" {if isset($category.confirmation) && $category.confirmation eq '1'}checked="checked"{elseif isset($smarty.post.confirmation) && $smarty.post.confirmation eq '1'}checked="checked"{/if} id="confirmation1" /><label for="confirmation1">&nbsp;{$esynI18N.yes}</label>
		<input type="radio" name="confirmation" value="0" {if isset($category.confirmation) && $category.confirmation eq '0'}checked="checked"{elseif isset($smarty.post.confirmation) && $smarty.post.confirmation eq '0'}checked="checked"{elseif empty($category)}checked="checked"{/if} id="confirmation2"/><label for="confirmation2">&nbsp;{$esynI18N.no}</label>
		<div id="confirmation_text" style="display: none;">
			<textarea name="confirmation_text" cols="43" rows="8" class="common">{if isset($category.confirmation_text)}{$category.confirmation_text}{elseif isset($smarty.post.confirmation_text)}{$smarty.post.confirmation_text}{/if}</textarea>
		</div>
	</td>
</tr>

<tr>
	<td><strong>{$esynI18N.status}:</strong></td>
	<td>
		<select name="status">
			<option value="active" {if isset($category.status) && $category.status eq 'active'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'active'}selected="selected"{/if}>{$esynI18N.active}</option>
			<option value="approval" {if isset($category.status) && $category.status eq 'approval'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'approval'}selected="selected"{/if}>{$esynI18N.approval}</option>
		</select>
	</td>
</tr>

<tr>
	<td><strong>{$esynI18N.icon}:</strong></td>
	<td>
		<div id="icons">
			{if isset($category.icon) && !empty($category.icon)}
				<img style="margin: 10px; visibility: visible; opacity: 1;" src="{$category.icon}" />
			{elseif isset($smarty.post.icon) && !empty($smarty.post.icon)}
				<img style="margin: 10px; visibility: visible; opacity: 1;" src="{$smarty.post.icon}" />
			{/if}
		</div>

		<input type="button" id="choose_icon" name="choose" class="common" value="{$esynI18N.choose_icon}" />
		<input type="button" id="remove_icon" name="remove" class="common" value="{$esynI18N.remove_icon}" />
		<input type="hidden" id="icon_name" name="icon" value="{if isset($category.icon)}{$category.icon}{elseif isset($smarty.post.icon)}{$smarty.post.icon}{/if}" />
	</td>
</tr>
{if !file_exists($smarty.const.ESYN_CATEGORY_ICONS_DIR)}
<tr>
	<td>&nbsp;</td>
	<td>
		<span class="option_tip">
			{$esynI18N.categories_icon_notif}
		</span>
	</td>
</tr>
{/if}
</table>

<table cellspacing="0" width="100%" class="striped">
<tr>
	<td style="padding: 0 0 0 11px; width: 0;">
		<input type="submit" name="save" class="common" value="{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$esynI18N.save_changes}{else}{$esynI18N.add}{/if}" />
	</td>
	<td style="padding: 0; width:99%;">
		{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}
			{if stristr($smarty.server.HTTP_REFERER, 'browse')}
				<input type="hidden" name="goto" value="browse_new" />
			{else}
				<input type="hidden" name="goto" value="list" />
			{/if}
		{else}
			<span><strong>&nbsp;{$esynI18N.and_then}&nbsp;</strong></span>
			<select name="goto">
				<option value="list" {if isset($smarty.post.goto) && $smarty.post.goto eq 'list'}selected="selected"{/if}>{$esynI18N.go_to_list}</option>
				<option value="browse_add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'browse_add'}selected="selected"{/if}>{$esynI18N.go_to_browse} {$parent.title}</option>
				<option value="browse_new" {if isset($smarty.post.goto) && $smarty.post.goto eq 'browse_new'}selected="selected"{/if}>{$esynI18N.go_to_browse_new_category}</option>
				<option value="add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'add'}selected="selected"{/if}>{$esynI18N.add_another_one}</option>
			</select>
		{/if}
	</td>
</tr>

</table>
<input type="hidden" name="id" value="{if isset($category.id)}{$category.id}{/if}" />
<input type="hidden" name="old_path" value="{if isset($category.old_path)}{$category.old_path}{/if}" />
</form>

<div style="display: none;">
	<div id="tip-content-hide_category" >{$esynI18N.hide_category_option}</div>
</div>

{include file="box-footer.tpl"}

{include_file js="js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/ckeditor/ckeditor, js/ext/plugins/chooser/chooser, js/admin/suggest-category"}

{include file="footer.tpl"}
