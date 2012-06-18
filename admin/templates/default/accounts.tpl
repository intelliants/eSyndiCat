{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && ($smarty.get.do eq 'add' || $smarty.get.do eq 'edit')}
	{include file="box-header.tpl" title=$gTitle}
	<form action="controller.php?file=accounts&amp;do={$smarty.get.do}{if $smarty.get.do eq 'edit'}&amp;id={$smarty.get.id}{/if}" method="post">
	{preventCsrf}
	<table cellspacing="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong>{$esynI18N.username}:</strong></td>
		<td><input type="text" name="username" size="26" class="common" value="{if isset($account.username)}{$account.username|escape:"html"}{elseif isset($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.password}:</strong></td>
		<td><input type="password" name="password" size="26" class="common" value="{if isset($smarty.post.password)}{$smarty.post.password|escape:"html"}{/if}"/></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.password_confirm}:</strong></td>
		<td><input type="password" name="password2" size="26" class="common" value="{if isset($smarty.post.password2)}{$smarty.post.password2|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.email}:</strong></td>
		<td><input type="text" name="email" size="26" class="common" value="{if isset($account.email)}{$account.email|escape:"html"}{elseif isset($smarty.post.email)}{$smarty.post.email|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.status}:</strong></td>
		<td>
			<select name="status">
				<option value="active" {if isset($account.status) && $account.status eq 'active'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'active'}selected="selected"{/if}>{$esynI18N.active}</option>
				<option value="approval" {if isset($account.status) && $account.status eq 'approval'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'approval'}selected="selected"{/if}>{$esynI18N.approval}</option>
				<option value="banned" {if isset($account.status) && $account.status eq 'banned'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'banned'}selected="selected"{/if}>{$esynI18N.banned}</option>
			</select>
		</td>
	</tr>
	</table>

	<table>
	<tr class="all">
		<td style="padding: 0 0 0 11px; width: 0;">
			<input type="submit" name="save" class="common" value="{if $smarty.get.do eq 'add'}{$esynI18N.add}{else}{$esynI18N.save_changes}{/if}" />
		</td>
		<td style="padding: 0;">
		{if isset($smarty.get.do) && $smarty.get.do eq 'add'}
			<span><strong>&nbsp;{$esynI18N.and_then}&nbsp;</strong></span>
			<select name="goto">
				<option value="list" {if isset($smarty.post.goto) && $smarty.post.goto eq 'list'}selected="selected"{/if}>{$esynI18N.go_to_list}</option>
				<option value="add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'add'}selected="selected"{/if}>{$esynI18N.add_another_one}</option>
			</select>
		{/if}
		</td>
	</tr>
	</table>
	<input type="hidden" name="do" value="{if isset($smarty.get.do)}{$smarty.get.do}{/if}" />
	<input type="hidden" name="old_name" value="{if isset($account.username)}{$account.username|escape:"html"}{/if}" />
	<input type="hidden" name="id" value="{if isset($smarty.get.id)}{$smarty.get.id}{/if}" />
	</form>
	{include file="box-footer.tpl"}
{else}
	<div id="box_accounts" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/accounts"}

{esynHooker name="smartyAdminAccountsAfterJSInclude"}

{include file="footer.tpl"}
