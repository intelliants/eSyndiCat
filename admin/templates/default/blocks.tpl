{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && ($smarty.get.do eq 'add' || $smarty.get.do eq 'edit')}
	{include file="box-header.tpl" title=$gTitle}
	<form action="controller.php?file=blocks&amp;do={$smarty.get.do}{if $smarty.get.do eq 'edit'}&amp;id={$smarty.get.id}{/if}" method="post">
	{preventCsrf}
	<table class="striped" cellspacing="0" width="100%">
	<tr>
		<td class="caption" colspan="2"><strong>{$esynI18N.block_options}</strong></td>
	</tr>
	<tr>
		<td width="120"><strong>{$esynI18N.type}:</strong></td>
		<td>
			<select name="type" id="block_type">
				{foreach from=$types key=key item=type}
					<option value="{$type}" {if isset($block.type) && $block.type eq $type}selected="selected"{elseif isset($smarty.post.type) && $smarty.post.type eq $type}selected="selected"{/if}>{$type}</option>
				{/foreach}
			</select>
			<br />
			<div class="option_tip" id="type_tip_plain" style="display: none;"><i>{$esynI18N.block_type_tip_plain}</i></div>
			<div class="option_tip" id="type_tip_html" style="display: none;"><i>{$esynI18N.block_type_tip_html}</i></div>
			<div class="option_tip" id="type_tip_smarty" style="display: none;"><i>{$esynI18N.block_type_tip_smarty}</i></div>
			<div class="option_tip" id="type_tip_php" style="display: none;"><i>{$esynI18N.block_type_tip_php}</i></div>
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.position}:</strong></td>
		<td>
			<select name="position">
				{foreach from=$positions key=key item=position}
					<option value="{$position}" {if isset($block.position) && $block.position eq $position}selected="selected"{elseif isset($smarty.post.position) && $smarty.post.position eq $position}selected="selected"{/if}>{$position}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.show_header}:</strong></td>
		<td>
			<input type="checkbox" name="show_header" value="1" {if isset($block.show_header) && $block.show_header eq '1'}checked="checked"{elseif isset($smarty.post.show_header) && $smarty.post.show_header eq '1'}checked="checked"{elseif !isset($block) && !$smarty.post}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.collapsible}:</strong></td>
		<td>
			<input type="checkbox" name="collapsible" value="1" disabled="disabled" {if isset($block.collapsible) && $block.collapsible eq '1'}checked="checked"{elseif isset($smarty.post.collapsible) && $smarty.post.collapsible eq '1'}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.collapsed}:</strong></td>
		<td>
			<input type="checkbox" name="collapsed" value="1" disabled="disabled" {if isset($block.collapsed) && $block.collapsed eq '1'}checked="checked"{elseif isset($smarty.post.collapsed) && $smarty.post.collapsed eq '1'}checked="checked"{/if} />
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.multi_language}:</strong></td>
		<td>
			<input type="checkbox" id="multi_language" name="multi_language" value="1" {if isset($block.multi_language) && $block.multi_language eq '1'}checked="checked"{elseif isset($smarty.post.multi_language) && $smarty.post.multi_language eq '1'}checked="checked"{elseif !isset($block) && !$smarty.post}checked="checked"{/if} />
		</td>
	</tr>
	<tr id="languages" style="display: none;">
		<td><strong>{$esynI18N.language}:</strong></td>
		<td>
			<label><input type="checkbox" id="select_all_languages" name="select_all_languages" value="1" {if isset($smarty.post.select_all) && $smarty.post.select_all eq '1'}checked="checked"{/if} />&nbsp;{$esynI18N.select_all}</label>
			
			{foreach from=$langs key=code item=lang}
				<br /><label><input type="checkbox" class="block_languages" name="block_languages[]" value="{$code}" {if isset($block.block_languages) &&  !empty($block.block_languages) && in_array($code, $block.block_languages)}checked="checked"{elseif isset($smarty.post.block_languages) && in_array($code, $smarty.post.block_languges)}checked="checked"{/if} />&nbsp;{$lang}</label>
			{/foreach}
		</td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.sticky}:</strong></td>
		<td>
			<input type="checkbox" id="sticky" name="sticky" value="1" {if isset($block.sticky) && $block.sticky eq '1'}checked="checked"{elseif isset($smarty.post.sticky) && $smarty.post.sticky eq '1'}checked="checked"{elseif !isset($block) && !$smarty.post}checked="checked"{/if} />
		</td>
	</tr>
	</table>
	
	<div id="acos" style="display: none;">
	<table class="striped">
	<tr>
		<td width="120"><strong>{$esynI18N.visible_on_pages}:</strong></td>
		<td>
			{if isset($pages_group) && !empty($pages_group)}
				{if isset($pages) && !empty($pages)}
					<input type="checkbox" value="1" name="select_all" id="select_all" {if isset($smarty.post.select_all) && $smarty.post.select_all eq '1'}checked="checked"{/if} /><label for="select_all">&nbsp;{$esynI18N.select_all}</label>
						<div style="clear:both;"></div>
					{foreach from=$pages_group item=group}
						<fieldset class="list" style="float:left;">
							{assign var="post_key" value="select_all_"|cat:$group}
							<legend><input type="checkbox" value="1" class="{$group}" name="select_all_{$group}" id="select_all_{$group}" {if isset($smarty.post.$post_key) && $smarty.post.$post_key eq '1'}checked="checked"{/if} /><label for="select_all_{$group}">&nbsp;<strong>{$esynI18N.$group}</strong></label></legend>
							{foreach from=$pages key=key item=page}
								{if $page.group eq $group}
									<ul style="list-style-type: none; width:200px;">
										<li style="margin: 0 0 0 15px; padding-bottom: 3px; float: left; width: 200px;" >
											<input type="checkbox" name="visible_on_pages[]" class="{$group}" value="{$page.name}" id="page_{$key}" {if in_array($page.name, $visibleOn, true)}checked="checked"{/if} /><label for="page_{$key}"> {if empty($page.title)}{$page.name}{else}{$page.title}{/if}</label>
										</li>
									</ul>
								{/if}
							{/foreach}
						</fieldset>
					{/foreach}
				{/if}
			{/if}
		</td>
	</tr>
	</table>
	</div>

	<table class="striped" cellspacing="0" width="100%">
		<tr>
			<td class="caption" colspan="2"><strong>{$esynI18N.block_contents}</strong></td>
		</tr>
	</table>

	<div id="blocks_contents" style="display: none;">
		<table class="striped" cellspacing="0" width="100%">
		<tr>
			<td width="150"><strong>{$esynI18N.title}:</strong></td>
			<td><input type="text" name="multi_title" size="30" class="common" value="{if isset($block.title) && !is_array($block.title)}{$block.title|escape:"html"}{elseif isset($smarty.post.multi_title)}{$smarty.post.multi_title|escape:"html"}{/if}" /></td>
		</tr>
		<tr>
			<td><strong>{$esynI18N.contents}:</strong></td>
			<td><textarea name="multi_contents" id="multi_contents" cols="50" rows="8" class="cked common">{if isset($block.contents) && !is_array($block.contents)}{$block.contents}{elseif isset($smarty.post.multi_contents)}{$smarty.post.multi_contents}{/if}</textarea></td>
		</tr>
		</table>
	</div>
	
	{foreach from=$langs key=code item=lang}
		<div id="blocks_contents_{$code}" style="display: none;">
			<table class="striped" cellspacing="0" width="100%">
			<tr>
				<td width="150"><strong>{$esynI18N.title}&nbsp;[{$lang}]:</strong></td>
				<td><input type="text" name="title[{$code}]" size="30" class="common" value="{if is_array($block.title) && isset($block.title.$code)}{$block.title.$code|escape:"html"}{elseif isset($smarty.post.title) && is_array($smarty.post.title) && isset($block.post.title.$code)}{$smarty.post.title.$code|escape:"html"}{elseif isset($block.title) && !empty($block.title)}{$block.title}{/if}" /></td>
			</tr>
			<tr>
				<td><strong>{$esynI18N.contents}&nbsp;[{$lang}]:</strong></td>
				<td><textarea name="contents[{$code}]" id="contents_{$code}" cols="50" rows="8" class="cked common">{if is_array($block.contents) && isset($block.contents.$code)}{$block.contents.$code|escape:"html"}{elseif isset($smarty.post.contents) && is_array($smarty.post.contents) && isset($smarty.post.contents.$code)}{$smarty.post.contents.$code|escape:"html"}{elseif isset($block.contents) && !empty($block.contents)}{$block.contents}{/if}</textarea></td>
			</tr>
			</table>
		</div>
	{/foreach}
	
	<table class="striped">
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="save" class="common" value="{if $smarty.get.do eq 'edit'}{$esynI18N.save_changes}{else}{$esynI18N.add}{/if}" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="do" value="{if isset($smarty.get.do)}{$smarty.get.do}{/if}" />
	<input type="hidden" name="id" value="{if isset($block.id)}{$block.id}{/if}" />
	</form>
	{include file="box-footer.tpl"}
{else}
	<div id="box_blocks" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/ckeditor/ckeditor, js/admin/blocks"}

{include file="footer.tpl"}
