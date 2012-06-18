{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{if isset($smarty.get.do) && $smarty.get.do eq 'edit'}
	
	{include file="box-header.tpl" title=$gTitle}
	
	<form action="controller.php?plugin=comments&amp;do={$smarty.get.do}{if $smarty.get.do eq 'edit'}&amp;id={$smarty.get.id}{/if}" method="post">
	{preventCsrf}
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong>{$esynI18N.author}:</strong></td>
		<td><input type="text" size="40" name="author" class="common" value="{if isset($comment.author)}{$comment.author|escape:"html"}{/if}" /></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.email}:</strong></td>
		<td><input type="text" size="40" name="email" class="common" value="{if isset($comment.email)}{$comment.email|escape:"html"}{/if}" /></td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.url}:</strong></td>
		<td><input type="text" size="40" name="url" class="common" value="{if isset($comment.url)}{$comment.url}{/if}" /></td>
	</tr>
	
	<tr>
		<td><strong>{$esynI18N.body}:</strong></td>
		<td><textarea name="body" cols="53" rows="8" class="common" id="commentbody">{if isset($comment.body)}{$comment.body|escape:"html"}{/if}</textarea></td>
	</tr>
	<tr>
		<td><strong>{$esynI18N.status}:</strong></td>
		<td>
			<select name="status">
				<option value="inactive" {if isset($comment.status) && $comment.status eq 'inactive'}selected="selected"{/if}>{$esynI18N.inactive}</option>
				<option value="active" {if isset($comment.status) && $comment.status eq 'active'}selected="selected"{/if}>{$esynI18N.active}</option>
			</select>
		</td>
	</tr>
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="edit_comments" value="{$esynI18N.save_changes}" class="common" />
			<input type="hidden" name="id" value="{$comment.id}" />
		</td>
	</tr>
	</table>
	</form>
	{include file="box-footer.tpl" class="box"}
{else}
	<div id="box_comments" style="margin-top: 15px;"></div>
{/if}

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ckeditor/ckeditor, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, plugins/comments/js/admin/comments"}

{include file="footer.tpl"}