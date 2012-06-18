<!-- dynamic bocks -->
{if isset($pos) && !empty($pos)}
	{foreach from=$pos item=block}
		<!--__b_{$block.id}-->
		{if $block.show_header or $manageMode}
			{include file="box-header.tpl" caption=$block.title style="movable" id=$block.id collapsible=$block.collapsible collapsed=$block.collapsed rss=$block.rss}
		{else}
			<div class="box" id="block_{$block.id}">
		{/if}
		<!--__b_c_{$block.id}-->
			{if $block.type eq 'smarty'}
				{insert name="dynamic" content=$block.contents}
			{elseif $block.type eq 'plain'}
				{$block.contents|escape:"html"}
			{elseif $block.type eq 'php'}
				{php}
					eval($this->_tpl_vars['block']['contents']);
				{/php}
			{else}
				{$block.contents}
			{/if}
		<!--__e_c_{$block.id}-->
		{if $block.show_header or $manageMode}
			{include file="box-footer.tpl"}
		{else}
			</div>
		{/if}
		<!--__e_{$block.id}-->
	{/foreach}
{/if}
<!-- end dynamic bocks -->
