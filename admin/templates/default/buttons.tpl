{if isset($actions)}
	<div class="buttons">
	{foreach from=$actions item=action}
		<a href="{if isset($action.url) && $action.url neq ''}{$action.url}{else}#{/if}" {if isset($action.attributes) && $action.attributes neq ''}{$action.attributes}{/if}><img src="{if $smarty.const.ESYN_CURRENT_PLUGIN && $smarty.const.ESYN_PLUGIN_TEMPLATE|cat:'/img/'|cat:$action.icon|file_exists}{$smarty.const.ESYN_URL}plugins/{$smarty.const.ESYN_CURRENT_PLUGIN}/admin/templates/img/{else}templates/{$config.admin_tmpl}/img/icons/{/if}{if isset($action.icon) && $action.icon neq ''}{$action.icon}{else}default-ico.png{/if}" title="{if isset($action.label) && $action.label neq ''}{$action.label}{/if}" alt="{if isset($action.label) && $action.label neq ''}{$action.label}{/if}" /></a>
	{/foreach}
	</div>

	<div style="clear:right; overflow:hidden;"></div>
{/if}
