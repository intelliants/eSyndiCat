<!-- simple box start -->
<div class="box" {if isset($id)}id="{$id}"{/if} {if isset($hidden)}style="display: none;"{/if}>
	<div class="inner">
		<div class="box-caption">{$title}</div>
		<div class="minmax {if isset($collapsed)}white-close{else}white-open{/if}"></div>
		<div class="box-content" {if isset($collapsed)}style="display: none;"{/if}{if isset($style) && !empty($style)}style="{$style}"{/if}>
