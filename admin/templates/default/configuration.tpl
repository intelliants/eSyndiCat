{include file="header.tpl" css="js/ext/plugins/fileuploadfield/css/file-upload"}

<a name="top"></a>

{include file="box-header.tpl" title=$esynI18N.htaccess_file id="htaccess" hidden="true"}
{if isset($htaccess_code) && !empty($htaccess_code)}
	<br />
	<a class="button" id="close" href="#">{$esynI18N.close}</a>&nbsp;
	<a class="button" id="rebuild" href="#">{$esynI18N.rebuild_htaccess}</a>&nbsp;
	<a class="button copybutton" id="copybutton" href="#">{$esynI18N.copy_to_clipboard}</a>
	{$htaccess_code}
{/if}
{include file="box-footer.tpl"}

{include file="box-header.tpl" title=$esynI18N.config_groups id="options"}

<div class="config-col-left">
	<ul class="groups">
	{foreach from=$groups key=key item=group_item name=groups}
		{if isset($group) && $group eq $key}
			<li><div>{$group_item}</div></li>
		{else}
			<li><a href="controller.php?file=configuration&amp;group={$key}">{$group_item}</a></li>
		{/if}
	{/foreach}
	</ul>
</div>

<div class="config-col-right">
{if isset($params)}
		<form action="controller.php?file=configuration&amp;group={$group}" enctype="multipart/form-data" method="post">
		{preventCsrf}
		<table cellspacing="0" class="striped" width="100%">
		
		{if isset($group) && $group eq 'email_templates'}
		<tr>
			<td colspan="2" style="padding:0;">
				<ul class="config-tabs">
				{if isset($smarty.get.show) && $smarty.get.show eq 'plaintext'}
					<li><div>{$esynI18N.plain_text_templates}</div></li>
				{else}
					<li><a id="plaintext" href="controller.php?file=configuration&amp;group=email_templates&amp;show=plaintext">{$esynI18N.plain_text_templates}</a></li>
				{/if}
				{if not (isset($smarty.get.show) && $smarty.get.show eq 'html')}
					<li><a href="controller.php?file=configuration&amp;group=email_templates&amp;show=html">{$esynI18N.html_templates}</a></li>
				{else}
					<li><div>{$esynI18N.html_templates}</div></li>
				{/if}
				</ul>
		{/if}
		
		{if $group neq 'email_templates' || (isset($smarty.get.show) && in_array($smarty.get.show, array("plaintext", "html")))}
			{foreach from=$params key=key item=value}
				{if $value.type eq "password"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}">{$value.description|escape:"html"}</td>
						<td><input type="password" class="common" size="45" name="param[{$value.name}]" id="{$value.name}" value="{$value.value|escape:"html"}" /></td>
					</tr>
				{elseif $value.type eq "text"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}" width="25%">{$value.description|escape:"html"}</td>
						{if $value.name eq 'expiration_action'}
							<td>
								<select name="param[expiration_action]" class="common">
									<option value="" {if $value.value eq ''}selected="selected"{/if}>{$esynI18N.nothing}</option>
									<option value="remove" {if $value.value eq 'remove'}selected="selected"{/if}>{$esynI18N.remove}</option>
									<optgroup label="Status">
										<option value="approval" {if $value.value eq 'approval'}selected="selected"{/if}>{$esynI18N.approval}</option>
										<option value="banned" {if $value.value eq 'banned'}selected="selected"{/if}>{$esynI18N.banned}</option>
										<option value="suspended" {if $value.value eq 'suspended'}selected="selected"{/if}>{$esynI18N.suspended}</option>
									</optgroup>
									<optgroup label="Type">
										<option value="regular" {if $value.value eq 'regular'}selected="selected"{/if}>{$esynI18N.regular}</option>
										<option value="featured" {if $value.value eq 'featured'}selected="selected"{/if}>{$esynI18N.featured}</option>
										<option value="partner" {if $value.value eq 'partner'}selected="selected"{/if}>{$esynI18N.partner}</option>
									</optgroup>
								</select>
							</td>
						{elseif $value.name eq 'captcha_preview'}
							{if isset($captcha_preview) && !empty($captcha_preview)}
								<td>{$captcha_preview}</td>
							{else}
								<td>{$esynI18N.no_captcha_preview}</td>
							{/if}
						{else}
							<td><input type="text" size="45" name="param[{$value.name}]" class="common" id="{$value.name}" value="{$value.value|escape:"html"}" /></td>
						{/if}
					</tr>
				{elseif $value.type eq "textarea"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}">{$value.description|escape:"html"}</td>
						<td><textarea name="param[{$value.name}]" id="{$value.name}" class="{if $value.editor eq '1'}cked {/if}common" cols="45" rows="7">{$value.value|escape:"html"}</textarea></td>
					</tr>
				{elseif $value.type eq "image"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}">{$value.description|escape:"html"}</td>
						<td>
							{if !is_writeable($smarty.const.ESYN_HOME|cat:$smarty.const.ESYN_DS|cat:'uploads')}
								<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i>{$esynI18N.upload_writable_permission}</i></div>							
							{else}
								<input type="hidden" name="param[{$value.name}]" />
								<input type="file" name="{$value.name}" id="conf_{$value.name}" class="common" size="42" />
							{/if}

							{if $value.value neq ''}
								<a href="#" class="view_image">{$esynI18N.view_image}</a>&nbsp;
								<a href="#" class="remove_image">{$esynI18N.remove} {$esynI18N.image}</a>
							{/if}
						</td>
					</tr>
				{elseif $value.type eq "checkbox"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}">{$value.description|escape:"html"}</td>
						<td><input type="checkbox" name="param[{$value.name}]" id="{$value.name}" /></td>
					</tr>
				{elseif $value.type eq "radio"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}" width="250">{$value.description|escape:"html"}</td>
						<td>{html_radio_switcher value=$value.value name=$value.name conf=true}</td>
					</tr>
				{elseif $value.type eq "select"}
					<tr>
						<td class="tip-header" id="tip-header-{$value.name}">{$value.description|escape:"html"}</td>
					
						{if $value.name eq 'tmpl'}
							{assign var="array_res" value=$templates}
						{elseif $value.name eq 'admin_tmpl'}
							{assign var="array_res" value=$admin_templates}
						{elseif $value.name eq 'lang'}
							{assign var="array_res" value=$langs}
						{else}
							{assign var="array_res" value=","|explode:$value.multiple_values}
						{/if}

						<td>
							<select name="param[{$value.name}]" class="common" {if $array_res|@count eq 1}disabled="disabled"{/if}>
								{foreach from=$array_res key=key item=value2}
									<option value="{if $value.name eq 'lang'}{$key}{else}{$value2|trim:"'"}{/if}" {if ($value.name eq 'lang' && $key eq $value.value) || $value2|trim:"'" eq $value.value}selected="selected"{/if}>{$value2|trim:"'"}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				{elseif $value.type eq "divider"}
					<tr>
						<td colspan="2" class="caption"><strong>{$value.value|escape:"html"}</strong>{if !empty($value.name)}<a name="{$value.name}"></a>{/if}
							{if $group eq 'email_templates'}
								&nbsp;<a href="{$smarty.server.REQUEST_URI|replace:"&":"&amp;"}#top" style="vertical-align:middle;"><img src="templates/default/img/icons/arrow_up.png" alt="" /></a>
								&nbsp;<a href="{$smarty.server.REQUEST_URI|replace:"&":"&amp;"}#bottom" style="vertical-align:middle;"><img src="templates/default/img/icons/arrow_down.png" alt="" /></a>
							{/if}
						</td>
					</tr>
				{/if}
			{/foreach}
		{/if}
		
		<tr class="all">
			{if $group eq 'email_templates' && !isset($smarty.get.show)}
			{else}
				<td colspan="2"><input type="submit" name="save" id="save" class="common" value="{$esynI18N.save_changes}" /></td>
			{/if}
		</tr>
		</table>
		</form>
{/if}
</div>

{include file="box-footer.tpl"}

<a name="bottom"></a>

{include_file js="js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/ext/plugins/fileuploadfield/FileUploadField, js/ckeditor/ckeditor, js/utils/zeroclipboard/ZeroClipboard, js/admin/configuration"}

{include file="footer.tpl"}
