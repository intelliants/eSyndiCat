{include file="header.tpl" css=$smarty.const.ESYN_URL|cat:"js/jquery/plugins/lightbox/css/jquery.lightbox"}

{include file="box-header.tpl" title=$gTitle}
	{if isset($templates) && !empty($templates)}
		<table cellspacing="0" class="striped common">
		<tr>
			<td colspan="3" style="border-left: 0px; text-align: center;">
				{navigation aTotal=$total_templates aTemplate=$url aItemsPerPage=$smarty.const.ESYN_NUM_TEMPLATES aNumPageItems=5}
			</td>
		</tr>
		<tr>
			<th width="10%" class="first">{$esynI18N.screenshot}</th>
			<th width="79%">{$esynI18N.details}</th>
			<th width="10%">{$esynI18N.operation}</th>
		</tr>
		{foreach from=$templates item=template}
			<tr>
				<td class="first">
					{if file_exists($smarty.const.ESYN_TEMPLATES|cat:$template.name|cat:$smarty.const.ESYN_DS|cat:"info"|cat:$smarty.const.ESYN_DS|cat:"preview.jpg")}
						{assign var="template_img" value=$smarty.const.ESYN_URL|cat:"templates/"|cat:$template.name|cat:"/info/"|cat:"preview.jpg"}
					{else}
						{assign var="template_img" value=$smarty.const.ESYN_URL|cat:"admin/templates/default/img/not_available.gif"}
					{/if}
					<a href="#" class="screenshots"><img src="{$template_img}" title="{$template.title}" alt="{$template.title}" /></a>
						{if isset($template.screenshots) && !empty($template.screenshots)}
							{foreach from=$template.screenshots item=screenshot}
								<a class="lb" href="{$smarty.const.ESYN_URL|cat:"templates/"|cat:$template.name|cat:"/info/screenshots/"|cat:$screenshot}" style="display: none;"><img src="{$smarty.const.ESYN_URL|cat:"templates/"|cat:$template.name|cat:"/info/screenshots/"|cat:$screenshot}" alt="{$template.title}" /></a>
							{/foreach}
						{/if}
				</td>
				
				<td style="vertical-align:top;">
					{$esynI18N.name}:&nbsp;<strong>{$template.title}</strong><br />
					{$esynI18N.author}:&nbsp;<strong>{$template.author}</strong><br />
					{$esynI18N.contributor}:&nbsp;<strong>{$template.contributor}</strong><br />
					{$esynI18N.release_date}:&nbsp;<strong>{$template.date}</strong><br />
					{$esynI18N.esyndicat_version}:&nbsp;<strong>{$template.compatibility}</strong><br />
				</td>
				
				<td>&nbsp;
					<form method="post" action="">
					{preventCsrf}
					<input type="hidden" name="template" value="{$template.name}" />
					{if $template.name neq $tmpl}
						<input type="submit" name="set_template" value="{$esynI18N.set_default}" class="common" /><br /><br />
					{/if}
					<a href="{$smarty.const.ESYN_URL}?preview={$template.name}" target="_blank">{$esynI18N.preview}</a>
					</form>
				</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="3" style="border-left: 0px; text-align: center;">
				{navigation aTotal=$total_templates aTemplate=$url aItemsPerPage=$smarty.const.ESYN_NUM_TEMPLATES aNumPageItems=5}
			</td>
		</tr>
		</table>
	{/if}
{include file="box-footer.tpl"}

{include_file js="js/jquery/plugins/lightbox/jquery.lightbox, js/admin/templates"}

{include file="footer.tpl"}
