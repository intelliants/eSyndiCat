{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && ($smarty.get.do eq 'add' || $smarty.get.do eq 'edit')}
	{include file="box-header.tpl" title=$gTitle}
	
	<form action="controller.php?file=plans&amp;do={$smarty.get.do}{if $smarty.get.do eq 'edit'}&amp;id={$smarty.get.id}{/if}" method="post">
	{preventCsrf}
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong>{$esynI18N.language}:</strong></td>
		<td>
			<select name="lang" {if $langs|@count eq 1}disabled="disabled"{/if}>
				{foreach from=$langs key=code item=lang}
					<option value="{$code}" {if (isset($plan.lang) && $plan.lang eq $code) || (isset($smarty.post.lang) && $smarty.post.lang eq $code)}selected="selected"{elseif $config.lang eq $code}selected="selected"{/if}>{$lang}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.title}:</strong></td>
		<td><input type="text" name="title" size="30" class="common" value="{if isset($plan.title)}{$plan.title|escape:"html"}{elseif isset($smarty.post.title)}{$smarty.post.title|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.description}:</strong></td>
		<td><textarea name="description" cols="5" rows="4" class="common">{if isset($plan.description)}{$plan.description|escape:"html"}{elseif isset($smarty.post.description)}{$smarty.post.description|escape:"html"}{/if}</textarea></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.deep_links}:</strong></td>
		<td><input type="text" class="common numeric" name="deep_links" size="30" value="{if isset($plan.deep_links)}{$plan.deep_links|escape:"html"}{elseif isset($smarty.post.deep_links)}{$smarty.post.deep_links|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.multicross}:</strong></td>
		<td><input type="text" class="common numeric" name="multicross" size="30" value="{if isset($plan.multicross)}{$plan.multicross|escape:"html"}{elseif isset($smarty.post.multicross)}{$smarty.post.multicross|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.cost}:</strong></td>
		<td><input type="text" class="common numeric" name="cost" size="30" value="{if isset($plan.cost)}{$plan.cost|escape:"html"}{elseif isset($smarty.post.cost)}{$smarty.post.cost|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.days}:</strong></td>
		<td><input type="text" class="common numeric" name="period" size="30" value="{if isset($plan.period)}{$plan.period|escape:"html"}{elseif isset($smarty.post.period)}{$smarty.post.period|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td width="200"><strong>{$esynI18N.send_expiration_email}:</strong></td>
		<td><input type="text" name="email_expire" size="30" class="common" value="{if isset($plan.email_expire)}{$plan.email_expire|escape:"html"}{elseif isset($smarty.post.email_expire)}{$smarty.post.email_expire|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.mark_after_submit}:</strong></td>
		<td>
			<select name="markas">
				<option value="sponsored" {if isset($plan.mark_as) && $plan.mark_as eq 'sponsored'}selected="selected"{elseif isset($smarty.post.markas) && $smarty.post.markas eq 'sponsored'}selected="selected"{/if}>{$esynI18N.sponsored}</option>
				<option value="featured" {if isset($plan.mark_as) && $plan.mark_as eq 'featured'}selected="selected"{elseif isset($smarty.post.markas) && $smarty.post.markas eq 'featured'}selected="selected"{/if}>{$esynI18N.featured}</option>
				<option value="partner" {if isset($plan.mark_as) && $plan.mark_as eq 'partner'}selected="selected"{elseif isset($smarty.post.markas) && $smarty.post.markas eq 'partner'}selected="selected"{/if}>{$esynI18N.partner}</option>
				<option value="regular" {if isset($plan.mark_as) && $plan.mark_as eq 'regular'}selected="selected"{elseif isset($smarty.post.markas) && $smarty.post.markas eq 'regular'}selected="selected"{/if}>{$esynI18N.regular}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td width="200"><strong>{$esynI18N.cron_for_expiration}:</strong></td>
		<td>
			<select name="action_expire">
				<option value="" {if isset($plan.action_expire) && $plan.action_expire eq ''}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq ''}selected="selected"{/if}>{$esynI18N.nothing}</option>
				<option value="remove" {if isset($plan.action_expire) && $plan.action_expire eq 'remove'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'remove'}selected="selected"{/if}>{$esynI18N.remove}</option>
				<optgroup label="Status">
					<option value="approval" {if isset($plan.action_expire) && $plan.action_expire eq 'approval'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'approval'}selected="selected"{/if}>{$esynI18N.approval}</option>
					<option value="banned" {if isset($plan.action_expire) && $plan.action_expire eq 'banned'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'banned'}selected="selected"{/if}>{$esynI18N.banned}</option>
					<option value="suspended" {if isset($plan.action_expire) && $plan.action_expire eq 'suspended'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'suspended'}selected="selected"{/if}>{$esynI18N.suspended}</option>
				</optgroup>
				<optgroup label="Type">
					<option value="regular" {if isset($plan.action_expire) && $plan.action_expire eq 'regular'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'regular'}selected="selected"{/if}>{$esynI18N.regular}</option>
					<option value="featured" {if isset($plan.action_expire) && $plan.action_expire eq 'featured'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'featured'}selected="selected"{/if}>{$esynI18N.featured}</option>
					<option value="partner" {if isset($plan.action_expire) && $plan.action_expire eq 'partner'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'partner'}selected="selected"{/if}>{$esynI18N.partner}</option>
				</optgroup>
			</select>
		</td>
	</tr>

	{if $fields}
		<tr>
			<td><strong>{$esynI18N.assign_fields}:</strong></td>
			<td>
				<input type="checkbox" value="Check all" id="check_all_fields" />&nbsp;<label for="check_all_fields">{$esynI18N.select_all}</label><br />
				{foreach from=$fields item=field}
					<input type="checkbox" name="fields[]" value="{$field.id}" id="field_{$field.id}" {if isset($plan) && $plan.fields}{if in_array($field.id, $plan.fields)}checked="checked"{/if}{/if} />&nbsp;<label for="field_{$field.id}">{$field.name}</label><br />
				{/foreach}
			</td>
		</tr>
	{/if}
	<tr>
		<td><strong>{$esynI18N.category}:</strong></td>
		<td>
			<div id="tree"></div>
			<label><input type="checkbox" name="recursive" value="1" {if isset($plan.recursive) && $plan.recursive eq '1'}checked="checked"{elseif isset($smarty.post.recursive) && $smarty.post.recursive eq '1'}checked="checked"{elseif !isset($plan) && !$smarty.post}checked="checked"{/if} />&nbsp;{$esynI18N.include_subcats}</label>
		</td>
	</tr>

	{esynHooker name="plansBeforeSubmitButton"}

	<tr class="all">
		<td colspan="2">
			<input type="submit" name="save" class="common" value="{if $smarty.get.do eq 'edit'}{$esynI18N.save_changes}{else}{$esynI18N.add}{/if}" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="id" value="{if isset($plan.id)}{$plan.id}{/if}" />
	<input type="hidden" name="old_name" value="{if isset($plan.name)}{$plan.name}{/if}" />
	<input type="hidden" name="do" value="{if isset($smarty.get.do)}{$smarty.get.do}{/if}" />
	<input type="hidden" name="categories_parents" id="categories_parents" value="{if isset($plan_categories_parents)}{$plan_categories_parents}{elseif isset($smarty.post.categories_parents)}{$smarty.post.categories_parents}{/if}" />
	<input type="hidden" name="categories" id="categories" value="{if isset($plan_categories)}{$plan_categories}{elseif isset($smarty.post.categories)}{$smarty.post.categories}{/if}" />
	</form>
	{include file="box-footer.tpl"}
{else}
	<div id="box_plans" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/plans"}

{esynHooker name="plansAfterJsInclude"}

{include file="footer.tpl"}
