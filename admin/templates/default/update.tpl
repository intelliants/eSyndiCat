{include file="header.tpl"}

{if isset($success) && !$success}
	{include file="box-header.tpl" title=$esynI18N.connection_information}
		<form action="controller.php?file=update&amp;do=update" id="update_form" method="post">
		{preventCsrf}
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td width="200"><strong>{$esynI18N.hostname}:</strong></td>
			<td><input type="text" name="hostname" size="26" class="common" value="{if isset($smarty.post.hostname)}{$smarty.post.hostname|escape:"html"}{/if}" /></td>
		</tr>
		<tr>
			<td width="200"><strong>{$esynI18N.username}:</strong></td>
			<td><input type="text" name="username" size="26" class="common" value="{if isset($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" /></td>
		</tr>
		<tr>
			<td width="200"><strong>{$esynI18N.password}:</strong></td>
			<td><input type="password" name="password" size="26" class="common" value="{if isset($smarty.post.password)}{$smarty.post.password|escape:"html"}{/if}" /></td>
		</tr>
		<tr class="all">
			<td style="padding: 0 0 0 11px; width: 0;">
				<input type="hidden" name="update" value="1" />
				<input type="submit" id="update" class="common" value="{$esynI18N.update}" />
			</td>
		</tr>
		</table>
		</form>
	{include file="box-footer.tpl"}
{/if}

{include_file js="js/admin/update"}

{include file="footer.tpl"}
