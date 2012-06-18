{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && ($smarty.get.do eq 'add' || $smarty.get.do eq 'edit')}
	{include file="box-header.tpl" title=$gTitle}
	<form action="controller.php?file=pages&amp;do={$smarty.get.do}{if $smarty.get.do eq 'edit'}&amp;id={$smarty.get.id}{/if}" method="post" id="page_form">
	{preventCsrf}
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong>{$esynI18N.page_url}:</strong></td>
		<td>
			<input type="text" name="name" size="24" class="common" style="float: left;" value="{if isset($page.name)}{$page.name}{elseif isset($smarty.post.name)}{$smarty.post.name}{/if}" {if isset($smarty.get.do) && $smarty.get.do eq 'edit'}readonly="readonly"{/if} />
		</td>
	</tr>
	{foreach from=$langs key=code item=lang}
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;<strong>{$lang}&nbsp;{$esynI18N.title}:</strong></td>
		<td>
			<input type="text" name="titles[{$code}]" size="24" class="common" value="{if isset($page.titles)}{$page.titles.$code}{elseif isset($smarty.post.titles.$code)}{$smarty.post.titles.$code}{/if}" />
		</td>
	</tr>
	{/foreach}
	<tr>
		<td><strong>{$esynI18N.show_menus}:</strong></td>
		<td>
			<input type="checkbox" name="menus[]" value="inventory" id="p4" {if in_array("inventory", $menus)}checked="checked"{/if} />
				<label for="p4">{$esynI18N.inventory_menu}</label><br />
			<input type="checkbox" name="menus[]" value="main" id="p1" {if in_array("main", $menus)}checked="checked"{/if} />
				<label for="p1">{$esynI18N.top_menu}</label><br />
			<input type="checkbox" name="menus[]" value="bottom" id="p2" {if in_array("bottom", $menus)}checked="checked"{/if} />
				<label for="p2">{$esynI18N.bottom_menu}</label><br />
			<input type="checkbox" name="menus[]" value="account" id="p3" {if in_array("account", $menus)}checked="checked"{/if} />
				<label for="p3">{$esynI18N.account_menu}</label><br />
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.no_follow_url}:</strong></td>
		<td>
			<input type="radio" name="nofollow" id="nf1" value="1" {if isset($page.nofollow) && $page.nofollow eq '1'}checked="checked"{elseif isset($smarty.post.nofollow) && $smarty.post.nofollow eq '1'}checked="checked"{/if} />
				<label for="nf1">{$esynI18N.yes}</label>
			<input type="radio" name="nofollow" id="nf2" value="0" {if isset($page.nofollow) && $page.nofollow eq '0'}checked="checked"{elseif isset($smarty.post.nofollow) && $smarty.post.nofollow eq '0'}{elseif !$smarty.post && !isset($page)}checked="checked"{/if} />
				<label for="nf2">{$esynI18N.no}</label>
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.status}:</strong></td>
		<td>
			<select name="status">
				<option value="active" {if isset($page.status) && $page.status eq 'active'}selected="selected"{/if}>{$esynI18N.active}</option>	
				<option value="approval" {if isset($page.status) && $page.status eq 'inactive'}selected="selected"{/if}>{$esynI18N.inactive}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.external_url}:</strong></td>
		<td>
			<input type="radio" name="unique" value="1" id="uniqueyes" {if isset($page.unique_url) && $page.unique_url neq ''}checked="checked"{elseif isset($smarty.post.unique) && $smarty.post.unique eq '1'}checked="checked"{elseif !$smarty.post && !isset($page)}checked="checked"{/if} /><label for="uniqueyes">{$esynI18N.yes}</label>
			<input type="radio" name="unique" value="0" id="uniqueno" {if isset($page.unique_url) && $page.unique_url eq ''}checked="checked"{elseif isset($smarty.post.unique) && $smarty.post.unique eq '0'}checked="checked"{/if} /><label for="uniqueno">{$esynI18N.no}</label>
		</td>
	</tr>
	</table>
	<div id="url_field" style="display: none;">
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td width="200"><strong>{$esynI18N.page_external_url}:</strong></td>
			<td>
				<input type="text" name="unique_url" size="44" id="unique_url" class="common" value="{if isset($page.unique_url)}{$page.unique_url}{elseif isset($smarty.post.unique_url)}{$smarty.post.unique_url}{/if}" />
				<input type="checkbox" id="non_modrewrite_thesame" name="non_modrewrite_thesame" value="yes" {if $smarty.get.do eq 'edit' && strcasecmp($page.unique_url, $page.non_modrewrite_url) eq 0}checked="checked"{elseif isset($smarty.post.non_modrewrite_thesame) && $smarty.post.non_modrewrite_thesame eq 'yes'}checked="checked"{/if} />
				<label for="non_modrewrite_thesame">{$esynI18N.modrewrite_url_thesame}</label>
			</td>
		</tr>
		<tr>
			<td width="200"><strong>{$esynI18N.modrewrite_url}:</strong></td>
			<td><input type="text" name="non_modrewrite_url" size="44" id="non_modrewrite_url" class="common" value="{if isset($page.non_modrewrite_url)}{$page.non_modrewrite_url}{elseif isset($smarty.post.non_modrewrite_url)}{$smarty.post.non_modrewrite_url}{/if}" {if $smarty.get.do eq 'edit' && strcasecmp($page.unique_url, $page.non_modrewrite_url) eq 0}disabled="disabled"{/if} /></td>
		</tr>
		</table>
	</div>

	<div id="page_options" style="display: none;">
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td width="200"><strong>{$esynI18N.custom_url}:</strong></td>
			<td>
				<input type="text" name="custom_url" size="24" class="common" style="float: left;" value="{if isset($page.custom_url)}{$page.custom_url}{elseif isset($smarty.post.custom_url)}{$smarty.post.custom_url}{/if}" />
				<div style="float: left; display: none; margin-left: 3px; padding: 4px;" id="page_url_box"><span>{$esynI18N.page_url_will_be}:&nbsp;<span><span id="page_url" style="padding: 3px; margin: 0; background: #FFE269;"></span></div>
			</td>
		</tr>
		<tr>
			<td width="200"><strong>{$esynI18N.meta_description}:</strong></td>
			<td>
				<textarea name="meta_description" cols="43" rows="2" class="common">{if isset($page.meta_description)}{$page.meta_description}{elseif isset($smarty.post.meta_description)}{$smarty.post.meta_description|escape:"html"}{/if}</textarea>
			</td>
		</tr>
		<tr>
			<td width="200"><strong>{$esynI18N.meta_keywords}:</strong></td>
			<td>
				<input type="text" name="meta_keywords" class="common" value="{if isset($page.meta_keywords)}{$page.meta_keywords}{elseif isset($smarty.post.meta_keywords)}{$smarty.post.meta_keywords|escape:"html"}{/if}" size="42"/>
			</td>
		</tr>
		</table>
	</div>

	<div id="ckeditor" style="display: none; padding: 5px 0 10px 11px;">
		<div style="padding-bottom: 5px;"><b>{$esynI18N.page_content}:</b></div>
		<div id="editorToolbar"></div>
		<div id="languages_content"></div>
		{foreach from=$langs key=code item=pre_lang}
			<div id="div_content_{$code}" title="{$pre_lang}" class="pre_lang x-hide-display">
				<textarea id="contents[{$pre_lang}]" name="contents[{$code}]" class="ckeditor_textarea">{if isset($page.contents.$code)}{$page.contents.$code}{elseif isset($smarty.post.contents.$code)}{$smarty.post.contents.$code}{else}&nbsp;{/if}</textarea>
			</div>
		{/foreach}
	</div>

	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr class="all">
		<td style="padding: 0 0 0 11px; width: 0;">
			<input type="submit" name="save" class="common" value="{if $smarty.get.do eq 'add'}{$esynI18N.add}{else}{$esynI18N.save_changes}{/if}" />
		</td>
		<td style="padding: 0;">
			{if $smarty.get.do eq 'add'}
				<strong>&nbsp;{$esynI18N.and_then}&nbsp;</strong>
				<select name="goto">
					<option value="list" {if isset($smarty.post.goto) && $smarty.post.goto eq 'list'}selected="selected"{/if}>{$esynI18N.go_to_list}</option>
					<option value="add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'add'}selected="selected"{/if}>{$esynI18N.add_another_one}</option>
				</select>
			{/if}
			
			&nbsp;<input type="submit" value="{$esynI18N.preview} {$esynI18N.page}" class="common" name="preview" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="do" value="{if isset($smarty.get.do)}{$smarty.get.do}{/if}" />
	<input type="hidden" name="old_name" value="{if isset($page.name)}{$page.name}{/if}" />
	<input type="hidden" name="old_custom_url" value="{if isset($page.custom_url)}{$page.custom_url}{/if}" />
	<input type="hidden" name="id" value="{if isset($page.id)}{$page.id}{/if}" />
	</form>
	{include file="box-footer.tpl"}
{else}
	<div id="box_pages" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ckeditor/ckeditor, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/pages"}

{include file="footer.tpl"}
