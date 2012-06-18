{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

{include file="box-header.tpl" title=$esynI18N.browse_categories}
{if $categories}
	<div class="categories">

	{assign var="cnt" value="0"}
	{assign var="row" value="1"}
	
	{foreach from=$categories key=key item=value}
		{assign var="cnt" value=$cnt+1}
		{if !($cnt % 3) || $cnt == $categories|@count}
			<div class="last"><div class="category">
				{if $value.crossed}@&nbsp;{/if}<a href="controller.php?file=browse&amp;id={$value.id}" class="{$value.status}">{$value.title|escape:"html"}</a>&nbsp;{if $config.num_listings_display}({$value.num_all_listings}){/if}{if $value.crossed}&nbsp;<a href="#" class="actions_edt-crossed_{$value.id}">{print_img full=true fl="icons/edit-grid-ico.png" admin=true style="vertical-align: middle;"}</a>&nbsp;<a href="#" class="actions_rmv-crossed_{$value.id}"><img style="vertical-align: middle;" src="{print_img fl="remove-grid-ico.png" folder="icons/" admin="true"}" alt="{$esynI18N.remove} {$value.title|escape:"html"}"></a>{/if}
				{if $config.subcats_display}
					{if isset($value.subcategories) && !empty($value.subcategories)}
						<div class="subcategories">
						{assign var="cnt2" value="1"}
						{foreach from=$value.subcategories key=key2 item=value2}
							{if $value.subcategories|@count < $config.subcats_display}
								{assign var="min" value=$value.subcategories|@count}
							{else}
								{assign var="min" value=$config.subcats_display}
							{/if}
							
							<a href="controller.php?file=browse&amp;id={$value2.id}" class="{$value2.status}">{$value2.title|escape:"html"}</a>{if $cnt2 < $min},{/if}
							{assign var="cnt2" value=$cnt2+1}
						{/foreach}
						</div>
					{/if}
				{/if}	
			</div></div>
			{if $row < $categories|@count / 3}
				<div class="divider clearfix" style="clear: left;"></div>
			{/if}
			{assign var="row" value=$row+1}
		{else}
			<div class="col"><div class="category">
				{if $value.crossed}@&nbsp;{/if}<a href="controller.php?file=browse&amp;id={$value.id}" class="{$value.status}">{$value.title|escape:"html"}</a>&nbsp;{if $config.num_listings_display}({$value.num_all_listings}){/if}{if $value.crossed}&nbsp;<a href="#" class="actions_edt-crossed_{$value.id}">{print_img full=true fl="icons/edit-grid-ico.png" admin=true style="vertical-align: middle;"}</a>&nbsp;<a href="#" class="actions_rmv-crossed_{$value.id}"><img style="vertical-align: middle;" src="{print_img fl="remove-grid-ico.png" folder="icons/" admin="true"}" alt="{$esynI18N.remove} {$value.title|escape:"html"}"></a>{/if}
				{if $config.subcats_display}
					{if isset($value.subcategories) && !empty($value.subcategories)}
						<div class="subcategories">
						{assign var="cnt2" value="1"}
						{foreach from=$value.subcategories key=key2 item=value2}
							{if $value.subcategories|@count < $config.subcats_display}
								{assign var="min" value=$value.subcategories|@count}
							{else}
								{assign var="min" value=$config.subcats_display}
							{/if}
							
							<a href="controller.php?file=browse&amp;id={$value2.id}" class="{$value2.status}">{$value2.title|escape:"html"}</a>{if $cnt2 < $min},{/if}
							{assign var="cnt2" value=$cnt2+1}
						{/foreach}
						</div>
					{/if}
				{/if}
			</div></div>
		{/if}
	{/foreach}
	</div>

	<div style="clear:both;">&nbsp;</div>
{else}
	{if isset($smarty.get.id)}
		{assign var="category_id" value=$smarty.get.id}
	{else}
		{assign var="category_id" value=0}
	{/if}

	{$esynI18N.no_categories|replace:"[category_id]":$category_id}
{/if}
{include file="box-footer.tpl"}


{if isset($related_categories) && !empty($related_categories)}

	{include file="box-header.tpl" title=$esynI18N.related_categories}

	<div class="categories">

		{assign var="cnt" value="0"}
		{assign var="row" value="1"}
		
		{foreach from=$related_categories key=key item=value}
			{assign var="cnt" value=$cnt+1}
			{if !($cnt % 3) || $cnt == $related_categories|@count}
				<div class="last"><div class="category">
					<a href="controller.php?file=browse&amp;id={$value.id}" class="{$value.status}">{$value.title|escape:"html"}</a>&nbsp;{if $config.num_listings_display}({$value.num_all_listings}){/if}&nbsp;<a href="#" class="actions_rmv-related_{$value.id}"><img style="vertical-align: middle;" src="{print_img fl="remove-grid-ico.png" folder="icons/" admin="true"}" alt="{$esynI18N.remove} {$value.title|escape:"html"}"></a>
					{if $config.subcats_display}
						{if isset($value.subcategories) && !empty($value.subcategories)}
							<div class="subcategories">
							{assign var="cnt2" value="1"}
							{foreach from=$value.subcategories key=key2 item=value2}
								{if $value.subcategories|@count < $config.subcats_display}
									{assign var="min" value=$value.subcategories|@count}
								{else}
									{assign var="min" value=$config.subcats_display}
								{/if}
								
								<a href="controller.php?file=browse&amp;id={$value2.id}" class="{$value2.status}">{$value2.title|escape:"html"}</a>{if $cnt2 < $min},{/if}
								{assign var="cnt2" value=$cnt2+1}
							{/foreach}
							</div>
						{/if}
					{/if}	
				</div></div>
				{if $row < $related_categories|@count / 3}
					<div class="divider clearfix" style="clear: left;"></div>
				{/if}
				{assign var="row" value=$row+1}
			{else}
				<div class="col"><div class="category">
					<a href="controller.php?file=browse&amp;id={$value.id}" class="{$value.status}">{$value.title|escape:"html"}</a>&nbsp;{if $config.num_listings_display}({$value.num_all_listings}){/if}&nbsp;<a href="#" class="actions_rmv-related_{$value.id}"><img style="vertical-align: middle;" src="{print_img fl="remove-grid-ico.png" folder="icons/" admin="true"}" alt="{$esynI18N.remove} {$value.title|escape:"html"}"></a>
					{if $config.subcats_display}
						{if isset($value.subcategories) && !empty($value.subcategories)}
							<div class="subcategories">
							{assign var="cnt2" value="1"}
							{foreach from=$value.subcategories key=key2 item=value2}
								{if $value.subcategories|@count < $config.subcats_display}
									{assign var="min" value=$value.subcategories|@count}
								{else}
									{assign var="min" value=$config.subcats_display}
								{/if}
								
								<a href="controller.php?file=browse&amp;id={$value2.id}" class="{$value2.status}">{$value2.title|escape:"html"}</a>{if $cnt2 < $min},{/if}
								{assign var="cnt2" value=$cnt2+1}
							{/foreach}
							</div>
						{/if}
					{/if}
				</div></div>
			{/if}
		{/foreach}
	</div>

	<div style="clear:both;">&nbsp;</div>
	{include file="box-footer.tpl"}
{/if}

<div id="box_listings" style="margin-top: 15px;"></div>

<div id="remove_reason" style="display: none;">
	{$esynI18N.listing_remove_reason}<br />
	<textarea cols="40" rows="5" name="body" id="remove_reason_text" class="common" style="width: 99%;"></textarea>
</div>

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/rowexpander/rowExpander, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/browse, js/utils/dutil"}

{include file="footer.tpl"}
