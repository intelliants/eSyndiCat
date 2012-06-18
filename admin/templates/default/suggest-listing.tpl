{include file="header.tpl" css=$smarty.const.ESYN_URL|cat:"js/jquery/plugins/lightbox/css/jquery.lightbox"}

{include file="box-header.tpl" title=$gTitle}

<form name="suggest_listing" action="controller.php?file=suggest-listing{if isset($smarty.get.do)}&amp;do={$smarty.get.do}{/if}{if isset($smarty.get.status)}&amp;status={$smarty.get.status}{/if}{if isset($smarty.get.id)}&amp;id={$smarty.get.id}{/if}" method="post" enctype="multipart/form-data">
{preventCsrf}
<table cellspacing="0" cellpadding="0" width="100%" class="striped">
<tr>
	<td width="200"><strong>{$esynI18N.listing_category}:</strong></td>
	<td>
		<span id="parent_category_title_container">
			<strong>{if isset($category.title)}<a href="controller.php?file=browse&amp;id={$parent.id}">{$category.title}</a>{else}ROOT{/if}</strong>
		</span>&nbsp;|&nbsp;
		<a href="#" id="change_category">{$esynI18N.change}...</a>

		<input type="hidden" id="category_id" name="category_id" value="{$category.id}" />
		<input type="hidden" id="category_parents" name="category_parents" value="{if isset($category.parents)}{$category.parents}{/if}" />
	</td>
</tr>

{esynHooker name="tplAdminSuggestListingForm"}

{if isset($fields)}
	{foreach from=$fields key=key item=value}
		<tr>
			{assign var="lang_key" value="field_"|cat:$value.name}
			{assign var="value_name" value=$value.name}
			<td><strong>{$esynI18N.$lang_key}:</strong></td>
			<td>
			{if $value.type eq 'text' || $value.type eq 'number'}
				<input {if $value.length neq ''}maxlength="{$value.length}"{/if} type="text" name="{$value.name}" value="{if isset($listing.$value_name)}{$listing.$value_name|escape:"html"}{elseif isset($smarty.post.$value_name)}{$smarty.post.$value_name}{else}{$value.default}{/if}" class="common{if $value.type eq 'number'} numeric{/if}" size="45" />
			{elseif $value.type eq 'textarea'}
				{if $value.editor eq '1'}
					<textarea class="ckeditor_textarea" id="{$value.name}" name="{$value.name}" cols="53" rows="8">{if isset($listing.$value_name)}{$listing.$value_name}{elseif isset($smarty.post.$value_name)}{$smarty.post.$value_name}{else}{$value.default}{/if}</textarea>
				{else}
					<textarea name="{$value.name}" cols="53" rows="8" class="common">{if isset($listing.$value_name)}{$listing.$value_name}{elseif isset($smarty.post.$value_name)}{$smarty.post.$value_name}{else}{$value.default}{/if}</textarea><br />
				{/if}
			{elseif $value.type eq 'combo'}
				{if isset($listing.$value_name)}
					{assign var="temp" value=$listing.$value_name}
				{elseif isset($smarty.post.$value_name)}
					{assign var="temp" value=$smarty.post.$value_name}
				{else}
					{assign var="temp" value=$value.default}
				{/if}
				
				{assign var="values" value=','|explode:$value.values} 
				
				{if $values}
					<select name="{$value.name}">
					{foreach from=$values item=item}
						{assign var="key" value="field_"|cat:$value.name|cat:'_'|cat:$item}
						<option value="{$item}" {if $item eq $temp}selected="selected"{/if}>{$esynI18N.$key}</option>
					{/foreach}
					</select>
				{/if}
			{elseif $value.type eq 'radio'}
				{if isset($listing.$value_name)}
					{assign var="temp" value=$listing.$value_name}
				{elseif isset($smarty.post.$value_name)}
					{assign var="temp" value=$smarty.post.$value_name}
				{else}
					{assign var="temp" value=$value.default}
				{/if}
				
				{assign var="values" value=','|explode:$value.values} 
				
				{if $values}
					{foreach from=$values item=item}
						{assign var="key" value="field_"|cat:$value.name|cat:'_'|cat:$item}
						<input type="radio" name="{$value.name}" id="{$value.name}_{$item}" value="{$item}" {if $item eq $temp}checked="checked"{/if} />
						<label for="{$value.name}_{$item}">{$esynI18N.$key}</label>
					{/foreach}
				{/if}
			{elseif $value.type eq 'checkbox'}
				{if isset($listing.$value_name)}
					{assign var="default" value=','|explode:$listing.$value_name}
				{elseif isset($smarty.post.$value_name)}
					{assign var="default" value=$smarty.post.$value_name}
				{else}
					{assign var="default" value=','|explode:$value.default} 
				{/if}
				
				{assign var="checkboxes" value=','|explode:$value.values}			
				
				{if $checkboxes}
					{foreach from=$checkboxes key=index item=item}
						{assign var="key" value="field_"|cat:$value.name|cat:'_'|cat:$index}
						<input type="checkbox" name="{$value.name}[]" id="{$value.name}_{$item}" value="{$item}" {if in_array($item, $default)}checked="checked"{/if} />
						<label for="{$value.name|cat:"_"|cat:$item}">{$esynI18N.$key}</label>
					{/foreach}
				{/if}
			{elseif $value.type eq 'image' || $value.type eq 'storage'}
				{if !is_writeable($smarty.const.ESYN_HOME|cat:$smarty.const.ESYN_DS|cat:'uploads')}
					<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i>{$esynI18N.upload_writable_permission}</i></div>
				{else}
					<input type="file" name="{$value.name}" id="{$value.name}" size="40" style="float:left;" />
					{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}
						{assign var="file_path" value=$smarty.const.ESYN_HOME|cat:'uploads'|cat:$smarty.const.ESYN_DS|cat:$listing.$value_name}
						
						{if $file_path|is_file && $file_path|file_exists}
							<div id="file_manage" style="float:left;padding-left:10px;">
								<a href="../uploads/{$listing.$value_name}" target="_blank">{$esynI18N.view}</a>&nbsp;|&nbsp;
								<a href="{$value_name}/{$smarty.get.id}/{$listing.$value_name}/" class="clear">{$esynI18N.delete}</a>
							</div>
						{/if}
					{/if}
				{/if}
			{elseif $value.type eq 'pictures'}
				{if !is_writeable($smarty.const.ESYN_HOME|cat:$smarty.const.ESYN_DS|cat:'uploads')}
					<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i>{$esynI18N.upload_writable_permission}</i></div>
				{else}
					<div class="pictures">
						<input type="file" name="{$value.name}[]" size="35" />
						<input type="button" value="+" class="add_img" />
						<input type="button" value="-" class="remove_img" />
					</div>
					<input type="hidden" value="{$value.length}" name="num_images" id="{$value.name}_num_img" />
					{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}
						{if !empty($listing.$value_name)}
							{assign var="images" value=','|explode:$listing.$value_name}

							{foreach from=$images item=image}
								{assign var="file_path" value=$smarty.const.ESYN_HOME|cat:'uploads'|cat:$smarty.const.ESYN_DS|cat:$image}

								{if $file_path|is_file && $file_path|file_exists}
									<div class="image_box">
										<a href="../uploads/{$image}" target="_blank" class="lightbox"><img src="../uploads/small_{$image}" /></a>
										<a href="{$value_name}/{$smarty.get.id}/{$image}" class="clear">{$esynI18N.delete}</a><br />
									</div>
								{/if}
							{/foreach}
						{/if}
					{/if}
				{/if}
			{/if}
		</td>
		</tr>
	{/foreach}

	{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}
		<tr>
			<td><strong>{$esynI18N.date}</strong></td>
			<td><input type="text" name="date" id="date" class="common" value="{if isset($listing.date)}{$listing.date}{elseif isset($smarty.post.date)}{$smarty.post.date}{/if}" /></td>
		</tr>
	{/if}

	<tr>
		<td><strong>{$esynI18N.featured}</strong></td>
		<td>{html_radio_switcher value=$listing.featured|default:0 name="featured"}</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.partner}</strong></td>
		<td>{html_radio_switcher value=$listing.partner|default:0 name="partner"}</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.assign_account}</strong></td>
		<td>
			<input type="radio" name="assign_account" value="1" id="a1" {if isset($smarty.post.assign_account) && $smarty.post.assign_account eq '1'}checked="checked"{/if} /><label for="a1">&nbsp;{$esynI18N.new_account}</label>
			<input type="radio" name="assign_account" value="2" id="a2" {if isset($smarty.post.assign_account) && $smarty.post.assign_account eq '2'}checked="checked"{elseif isset($smarty.get.do) && $smarty.get.do eq 'edit' && isset($account) && !empty($account)}checked="checked"{/if} /><label for="a2">&nbsp;{$esynI18N.existing_account}</label>
			<input type="radio" name="assign_account" value="0" id="a0" {if isset($smarty.post.assign_account) && $smarty.post.assign_account eq '0'}checked="checked"{elseif !$smarty.post && !isset($account)}checked="checked"{/if} /><label for="a0">&nbsp;{$esynI18N.dont_assign}</label>
		
			<div id="exist_account" style="display:none;">
				<div id="accounts_list">{if isset($account) && !empty($account)}{$account.id}|{$account.username}{/if}</div>
			</div>			
			<div id="new_account" style="display:none;">
				<table border="0">
				<tr>
					<td>{$esynI18N.username}:</td>
					<td><input type="text" name="new_account" size="45" class="common" value="{if isset($smarty.post.new_account)}{$smarty.post.new_account}{/if}" /></td>
				</tr>
				<tr>
					<td>{$esynI18N.email}:</td>
					<td><input type="text" name="new_account_email" size="45" class="common" value="{if isset($smarty.post.new_account_email)}{$smarty.post.new_account_email}{/if}" /></td>
				</tr>
				</table>
			</div>
		</td>
	</tr>

	<tr>
		<td class="caption" colspan="2"><strong>{$esynI18N.additional_fields}</strong></td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.listing_status}:</strong></td>
		<td> 
			<select name="status">
				<option value="active" {if isset($listing.status) && $listing.status eq 'active'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'active'}selected="selected"{/if}>{$esynI18N.active}</option>
				<option value="approval" {if isset($listing.status) && $listing.status eq 'approval'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'approval'}selected="selected"{/if}>{$esynI18N.approval}</option>
				<option value="banned" {if isset($listing.status) && $listing.status eq 'banned'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'banned'}selected="selected"{/if}>{$esynI18N.banned}</option>
				<option value="banned" {if isset($listing.status) && $listing.status eq 'suspended'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'suspended'}selected="selected"{/if}>{$esynI18N.suspended}</option>
			</select>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.rank}:</strong></td>
		<td> 
			<select name="rank">
				{section name="listing_rank" loop="11"}
					<option value="{$smarty.section.listing_rank.index}" {if isset($listing.rank) && $listing.rank eq $smarty.section.listing_rank.index}selected="selected"{/if}>{$smarty.section.listing_rank.index}</option>
				{/section}
			</select>
		</td>
	</tr>

	{if $config.expiration_period > 0}
		<tr>
			<td><strong>{$esynI18N.expiration_period}:</strong></td>
			<td><input type="text" name="expire" class="common" value="{if isset($listing.expire) && $listing.expire > 0}{$listing.expire}{elseif isset($smarty.post.expire)}{$smarty.post.expire}{else}{$config.expiration_period}{/if}" /></td>
		</tr>
		<tr>
			<td><strong>{$esynI18N.cron_for_expiration}:</strong></td>
			<td>
				<select name="action_expire">
					<option value="" {if isset($listing.action_expire) && $listing.action_expire eq ''}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq ''}selected="selected"{elseif $config.expiration_action eq ''}selected="selected"{/if}>{$esynI18N.nothing}</option>
					<option value="remove" {if isset($listing.action_expire) && $listing.action_expire eq 'remove'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'remove'}selected="selected"{elseif $config.expiration_action eq 'remove'}selected="selected"{/if}>{$esynI18N.remove}</option>
					<optgroup label="Status">
						<option value="approval" {if isset($listing.action_expire) && $listing.action_expire eq 'approval'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'approval'}selected="selected"{elseif $config.expiration_action eq 'approval'}selected="selected"{/if}>{$esynI18N.approval}</option>
						<option value="banned" {if isset($listing.action_expire) && $listing.action_expire eq 'banned'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'banned'}selected="selected"{elseif $config.expiration_action eq 'banned'}selected="selected"{/if}>{$esynI18N.banned}</option>
						<option value="suspended" {if isset($listing.action_expire) && $listing.action_expire eq 'suspended'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'suspended'}selected="selected"{elseif $config.expiration_action eq 'suspended'}selected="selected"{/if}>{$esynI18N.suspended}</option>
					</optgroup>
					<optgroup label="Type">
						<option value="regular" {if isset($listing.action_expire) && $listing.action_expire eq 'regular'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'regular'}selected="selected"{elseif $config.expiration_action eq 'regular'}{/if}>{$esynI18N.regular}</option>
						<option value="featured" {if isset($listing.action_expire) && $listing.action_expire eq 'featured'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'featured'}selected="selected"{elseif $config.expiration_action eq 'featured'}selected="selected"{/if}>{$esynI18N.featured}</option>
						<option value="partner" {if isset($listing.action_expire) && $listing.action_expire eq 'partner'}selected="selected"{elseif isset($smarty.post.action_expire) && $smarty.post.action_expire eq 'partner'}selected="selected"{elseif $config.expiration_action eq 'partner'}selected="selected"{/if}>{$esynI18N.partner}</option>
					</optgroup>
				</select>
			</td>
		</tr>
	{/if}
	</table>
	
	<table cellspacing="0" width="100%" class="striped">
	<tr>
		<td style="padding: 0 0 0 11px; width: 1%">
			<input type="checkbox" name="send_email" id="send_email" {if $config.listing_admin_add}checked="checked"{/if} />&nbsp;<label for="send_email">{$esynI18N.email_notif}?</label>&nbsp;|&nbsp;
			<input type="submit" name="save" class="common" value="{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$esynI18N.save}{else}{$esynI18N.create_listing}{/if}" />

			{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}
				{if stristr($smarty.server.HTTP_REFERER, 'browse')}
					<input type="hidden" name="goto" value="browse" />
				{else}
					<input type="hidden" name="goto" value="list" />
				{/if}
			{else}
				<span><strong>{$esynI18N.and_then}</strong></span>
				<select name="goto">
					<option value="list" {if isset($smarty.post.goto) && $smarty.post.goto eq 'list'}selected="selected"{/if}>{$esynI18N.go_to_list}</option>
					<option value="add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'add'}selected="selected"{/if}>{$esynI18N.add_another_one}</option>
					<option value="addtosame" {if isset($smarty.post.goto) && $smarty.post.goto eq 'addtosame'}selected="selected"{/if}>{$esynI18N.add_another_one_to_same}</option>
				</select>
			{/if}
		</td>
	</tr>

	</table>
	<input type="hidden" name="do" value="{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}{$smarty.get.do}{/if}" />
	</form>
{/if}

{esynHooker name="tplAdminSuggestListingBeforeIncludeJs"}

{include_file js="js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/jquery/plugins/lightbox/jquery.lightbox, js/ckeditor/ckeditor, js/admin/suggest-listing"}

{esynHooker name="tplAdminSuggestListingAfterIncludeJs"}

{include file="box-footer.tpl"}

{include file="footer.tpl"}
