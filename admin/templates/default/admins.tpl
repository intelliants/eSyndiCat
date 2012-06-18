{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && ($smarty.get.do eq 'add' || $smarty.get.do eq 'edit')}
	{include file="box-header.tpl" title=$gTitle}
	<form action="controller.php?file=admins{if $smarty.get.do eq 'add'}&amp;do=add{else $smarty.get.do eq 'edit'}&amp;do=edit&amp;id={$smarty.get.id}{/if}" method="post">
	{preventCsrf}
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong>{$esynI18N.username}:</strong></td>
		<td><input type="text" name="username" class="common" size="22" value="{if isset($admin.username)}{$admin.username|escape:"html"}{elseif isset($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" /></td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.fullname}:</strong></td>
		<td><input type="text" name="fullname" class="common" size="22" value="{if isset($admin.fullname)}{$admin.fullname|escape:"html"}{elseif isset($smarty.post.fullname)}{$smarty.post.fullname|escape:"html"}{/if}" /></td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.email}:</strong></td>
		<td><input type="text" name="email" class="common" size="22" value="{if isset($admin.email)}{$admin.email|escape:"html"}{elseif isset($smarty.post.email)}{$smarty.post.email|escape:"html"}{/if}" /></td>
	</tr>
		
	<tr>
		<td><strong>{$esynI18N.password}:</strong></td>
		<td><input type="password" name="new_pass" class="common" size="22" /></td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.password_confirm}:</strong></td>
		<td><input type="password" name="new_pass2" class="common" size="22" /></td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.status}:</strong></td>
		<td>
			<select name="status">
				<option value="active" {if isset($admin.status) && $admin.status eq 'active'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'active'}selected="selected"{/if}>{$esynI18N.active}</option>
				<option value="inactive" {if isset($admin.status) && $admin.status eq 'inactive'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'inactive'}selected="selected"{/if}>{$esynI18N.inactive}</option>
			</select>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.submission_notif}:</strong></td>
		<td>{html_radio_switcher value=$admin.submit_notif|default:0 name="submit_notif"}</td>
	</tr>
	
{if $config.sponsored_listings}
	<tr>
		<td><strong>{$esynI18N.payment_notif}:</strong></td>
		<td>{html_radio_switcher value=$admin.payment_notif|default:0 name="payment_notif"}</td>
	</tr>
{/if}

	<tr>
		<td class="caption" colspan="2"><strong>{$esynI18N.admin_permissions}</strong></td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.super_admin}:&nbsp;</strong></td>
		<td>
			<input type="radio" name="super" value="1" id="type1" {if isset($admin.super) && $admin.super eq '1'}checked="checked"{elseif isset($smarty.post.super) && $smarty.post.super eq '1'}checked="checked"{/if} /><label for="type1">&nbsp;{$esynI18N.enabled}</label>
			<input type="radio" name="super" value="0" id="type0" {if isset($admin.super) && $admin.super eq '0'}checked="checked"{elseif isset($smarty.post.super) && $smarty.post.super eq '0'}checked="checked"{elseif !$smarty.post && !isset($admin)}checked="checked"{/if} /><label for="type0">&nbsp;{$esynI18N.disabled}</label>
		</td>
	</tr>
	</table>

	<div id="permissions" style="display: none;">
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td>
				<ul style="list-style-type: none;">
					<li class="caption" style="padding-bottom: 3px;">
						<input type="checkbox" value="1" name="select_all_permis" id="select_all_permis" {if isset($smarty.post.select_all_permis) && $smarty.post.select_all_permis eq '1'}checked="checked"{/if} /><label for="select_all_permis">&nbsp;{$esynI18N.select_all}</label>
					</li>
					{foreach from=$esynAcos key=key item=aco}
						<li style="margin: 0 0 0 15px; padding-bottom: 3px; float: left; width: 150px;" >
							<input type="checkbox" name="permissions[]" value="{$key}" id="ts{$key}" {if (isset($admin.permissions) && in_array($key, $admin.permissions)) || (isset($smarty.post.permissions) && in_array($key, $smarty.post.permissions))}checked="checked"{/if} /><label for="ts{$key}">&nbsp;{$aco}</label>
						</li>
					{/foreach}
				</ul>
			</td>
		</tr>
		</table>
	</div>
	
	<table cellspacing="0" width="100%" class="striped">
	<tr>
		<td style="padding: 0 0 0 11px; width: 0;">
			<input type="submit" name="save" class="common" value="{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$esynI18N.save_changes}{else}{$esynI18N.add}{/if}" />
		</td>
		<td style="padding: 0;">
			{if $smarty.get.do eq 'add'}
				<strong>&nbsp;{$esynI18N.and_then}&nbsp;</strong>
				<select name="goto">
					<option value="list" {if isset($smarty.post.goto) && $smarty.post.goto eq 'list'}selected="selected"{/if}>{$esynI18N.go_to_list}</option>
					<option value="add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'add'}selected="selected"{/if}>{$esynI18N.add_another_one}</option>
				</select>
			{/if}
		</td>
	</tr>
	</table>
	<input type="hidden" name="id" value="{if isset($admin.id)}{$admin.id}{/if}" />
	<input type="hidden" name="do" value="{if isset($smarty.get.do)}{$smarty.get.do}{/if}" />
	</form>
	{include file="box-footer.tpl"}
{else}
	<div id="box_admins" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/admins"}

{include file="footer.tpl"}