{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

<div id="box_add_phrase" style="margin-top: 15px;">
	{preventCsrf}
</div>

{if $smarty.get.view eq 'language'}
	{include file="box-header.tpl" title=$gTitle}
	
	<form action="controller.php?file=language&amp;view=language" method="post">
	{preventCsrf}
	<table cellspacing="0" cellpadding="10" width="100%" class="common">
	<tr>
		<th class="first">{$esynI18N.language}</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>{$esynI18N.default}</th>
	</tr>
		
	{foreach from=$langs key=code item=language}
	<tr>
		<td class="first">{$language}</td>
		<td><a href="controller.php?file=language&amp;view=phrase&amp;language={$code}">{$esynI18N.edit_translate|replace:"language":$language}</a></td>
		<td>
		{if $langs|count neq 1 && $code neq $config.lang}
			<a class="delete_language" href="controller.php?file=language&amp;view=language&amp;do=delete&amp;language={$code}">{$esynI18N.delete}</a>&nbsp;|&nbsp;
		{/if}
		<a href="controller.php?file=language&amp;view=language&amp;do=download&amp;language={$code}">{$esynI18N.download}</a></td>
		<td width="100">
			{if $code neq $config.lang}
				<a href="controller.php?file=language&amp;view=language&amp;do=default&amp;language={$code}">{$esynI18N.set_default}</a>
			{else}
				&nbsp;
			{/if}
		</td>
	</tr>
	{/foreach}
	</table>
	</form>
	{include file="box-footer.tpl"}
{elseif $smarty.get.view eq 'phrase'}
	<div id="box_phrases" style="margin-top: 15px;"></div>
{elseif $smarty.get.view eq 'download'}
	{include file="box-header.tpl" title=$gTitle}
	
	<form action="controller.php?file=language&amp;view=download" method="post">
	{preventCsrf}
	<input type="hidden" name="do" value="download" />
	<table cellspacing="0" cellpadding="10" width="100%" class="common">
	<tr>
		<th colspan="2" class="first caption">{$esynI18N.download}</td>
	</tr>
	<tr>
		<td class="first" width="200">{$esynI18N.language}</td>
		<td>
			<select name="lang" {if $langs|count eq 1}disabled="disabled"{/if}>
				{foreach from=$langs key=code item=lang}
					<option value="{$code}">{$lang}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td class="first">{$esynI18N.file_format}</td>
		<td>
			<select name="file_format">
				<option value="csv" {if isset($smarty.post.file_format) && $smarty.post.file_format eq 'csv'}selected="selected"{/if}>{$esynI18N.csv_format}</option>
				<option value="sql" {if isset($smarty.post.file_format) && $smarty.post.file_format eq 'sql'}selected="selected"{/if}>{$esynI18N.sql_format}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="first">{$esynI18N.filename}</td>
		<td><input type="text" size="40" name="filename" class="common" value="{if isset($smarty.post.filename) && !empty($smarty.post.filename)}{$smarty.post.filename|escape:"html"}{else}esc_language{/if}" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="first">
			<input type="submit" class="common" value="{$esynI18N.download}" />
		</td>
	</tr>
	</table>
	</form>
	
	<form action="controller.php?file=language&amp;view=download" method="post" enctype="multipart/form-data">
	{preventCsrf}
	<input type="hidden" name="do" value="import" />
	<table cellspacing="0" cellpadding="10" width="100%" class="common">
	<tr>
		<th colspan="2" class="first caption">{$esynI18N.import}</td>
	</tr>
	<tr>
		<td class="first">{$esynI18N.file_format}</td>
		<td>
			<select name="file_format">
				<option value="csv" {if isset($smarty.post.file_format) && $smarty.post.file_format eq 'csv'}selected="selected"{/if}>{$esynI18N.csv_format}</option>
				<option value="sql" {if isset($smarty.post.file_format) && $smarty.post.file_format eq 'sql'}selected="selected"{/if}>{$esynI18N.sql_format}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="first" width="200">{$esynI18N.import_from_pc}</td>
		<td><input type="file" name="language_file" size="40" /></td>
	</tr>
	<tr>
		<td class="first">{$esynI18N.import_from_server}</td>
		<td><input type="text" size="40" name="language_file2" class="common" value="../updates/" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="first">
			<input type="submit" class="common" value="{$esynI18N.import}" />
		</td>
	</tr>
	</table>
	</form>
	{include file="box-footer.tpl"}
{elseif $smarty.get.view eq 'add_lang'}
	{include file="box-header.tpl" title=$esynI18N.copy_language}
	
	<form action="controller.php?file=language&amp;view=add_lang" method="post">
	{preventCsrf}
	<input type="hidden" name="do" value="add_lang" />
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="250">{$esynI18N.copy_default_language_to|replace:"[lang]":$langs[$config.lang]}</td>
		<td>
			<label for="new_code">{$esynI18N.iso_code}</label>
			<input id="new_code" size="2" maxlength="2" type="text" name="new_code" class="common" value="{if isset($smarty.post.new_code)}{$smarty.post.new_code|escape:"html"}{/if}" />
			<label for="new_lang">{$esynI18N.title}</label>
			<input id="new_lang" size="10" maxlength="40" type="text" name="new_lang" class="common" value="{if isset($smarty.post.new_lang)}{$smarty.post.new_lang|escape:"html"}{/if}" />
			<input type="submit" class="common" value="{$esynI18N.copy_language}" />
		</td>
	</tr>
	
	<tr>
		<td width="200">{$esynI18N.all_languages}</td>
		<td>
		{foreach from=$langs key=code item=language}
			<b>{$language}</b>&nbsp;{if $code eq $config.lang}[ {$esynI18N.default} ]{/if}<br />
		{/foreach}
		</td>
	</tr>

	</table>
	</form>
	{include file="box-footer.tpl"}
{elseif $smarty.get.view eq 'compare'}
	<div id="box_compare" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/language"}

{include file="footer.tpl"}