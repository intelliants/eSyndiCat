{include file="header.tpl"}

<h1>{$header}</h1>

{assign var="confirm_key" value="confirm_"|cat:$category.id}

{if $category.confirmation && !isset($smarty.cookies.$confirm_key)}
	{$category.confirmation_text|escape:"html"}&nbsp;
	<div style="text-align: center; margin-top: 20px;">
		<input type="button" class="button" name="confirm_answer" id="continue" value="{$lang.yes}" />
		<input type="button" class="button" name="confirm_answer" id="back" value="{$lang.no}" />
		<input type="hidden" name="category_id" id="category_id" value="{$category.id}" />
	</div>
{else}
	{if $category.description}
		<div class="box">
			{$category.description}
		</div>
	{/if}
	
	{esynHooker name="tplFrontIndexBeforeCategories"}

	{if $categories}
		{include file="box-header.tpl" caption=$lang.categories style="fixed"}
			{print_categories aCategories=$categories aCols=$category.num_cols aSubcategories=$config.subcats_display display_type=$config.categories_display_type}
		{include file="box-footer.tpl"}
	{/if}

	<div id="centerBlocks" class="groupWrapper">
		{include file="parse-blocks.tpl" pos=$centerBlocks|default:null}
	</div>
	
	{esynHooker name="indexBeforeListings"}

	{if $listings}
		<!-- listings box start -->
		<div class="listings">
			{if $config.mod_rewrite}{assign var="type" value=2}{else}{assign var="type" value=1}{/if}
			{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=$config.use_html_path}

			<table cellspacing="0" cellpadding="0" width="100%">
			{foreach from=$listings item=listing}
				{include file="listing-display.tpl"}
			{/foreach}
			</table>
		</div>
		<!-- listings box end -->

		<!-- visitor sorting start -->
		{if $config.visitor_sorting}
			<div class="listing-sorting">{$lang.sort_listings_by}
				{foreach from=$sortings item=order}
					{if $order eq $config.listings_sorting}
						{$lang.$order}
					{else}
						<a href="{$smarty.server.REQUEST_URI|add_url_param:'order':$order}" rel="nofollow">{$lang.$order}</a>
					{/if}
				{/foreach}&nbsp;&nbsp;

				{if $config.listings_sorting_type eq 'ascending'}
					{$lang.ascending} | 
					<a href="{$smarty.server.REQUEST_URI|add_url_param:'order_type':descending}" rel="nofollow">{$lang.descending}</a>
				{else}
					<a href="{$smarty.server.REQUEST_URI|add_url_param:'order_type':ascending}" rel="nofollow">{$lang.ascending}</a> | 
					{$lang.descending}
				{/if}
			</div>
		{/if}
		<!-- visitor sorting end -->
			
		{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=$config.use_html_path}

		{if $esynAccountInfo.id}
			<hr /><div class="waiting">&nbsp;</div><div class="admin-approve"> - {$lang.listings_legend}</div>
			{include_file js="js/intelli/intelli.tree"}
		{/if}

		{include_file js="js/frontend/listing-display"}
	{/if}

	{if isset($related_categories) && !empty($related_categories)}
		<!-- related categories box start -->
		{include file="box-header.tpl" caption=$lang.related_categories style="fixed"}
			{print_categories aCategories=$related_categories}
		{include file="box-footer.tpl"}
		<!-- related categories box end -->
	{/if}

	{if isset($neighbour_categories) && !empty($neighbour_categories)}
		<!-- neighbour categories box start -->
		{include file="box-header.tpl" caption=$lang.neighbour_categories style="fixed"}
			{print_categories aCategories=$neighbour_categories}
		{include file="box-footer.tpl"}
		<!-- neighbour categories box end -->
	{/if}
{/if}

{include_file js="js/frontend/index"}

{esynHooker name="indexBeforeFooter"}

{include file="footer.tpl"}
