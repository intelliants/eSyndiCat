{include file="header.tpl"}

<h1>{$lang.search}</h1>

<div class="box">
	<form action="{$smarty.const.ESYN_URL}search.php" method="get">
		<table style="width:auto;">
			<tr>
				<td>{$lang.search}:</td>
				<td>
					<input type="text" class="text" name="what" id="what" size="22" value="{if isset($smarty.get.what)}{$smarty.get.what|escape:"html"}{/if}" />
				</td>
				<td>
					<input type="submit" value="{$lang.search}" class="button" />
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;">
					<input type="radio" name="type" value="1" id="any" {if isset($smarty.get.type) && $smarty.get.type eq '1'}checked="checked"{elseif !isset($smarty.get.type)}checked="checked"{/if}/><label for="any">{$lang.any_word}</label> |
					<input type="radio" name="type" value="2" id="all" {if isset($smarty.get.type) && $smarty.get.type eq '2'}checked="checked"{/if}/><label for="all">{$lang.all_words}</label> |
					<input type="radio" name="type" value="3" id="exact" {if isset($smarty.get.type) && $smarty.get.type eq '3'}checked="checked"{/if}/><label for="exact">{$lang.exact_match}</label>
				</td>
			</tr>
		</table>
	</form>
</div>

{if isset($categories) && !empty($categories)}
	{include file="box-header.tpl" caption=$lang.categories_found|cat:$total_categories style="fixed"}
		{print_categories aCategories=$categories aCols=1 aSubcategories=false display_type=vertical path_title=true truncate_path_title=100}
		{if $total_categories > $config.num_cats_for_search && !isset($smarty.get.cats) && !isset($smarty.post.cats_only)}
			{if isset($smarty.get.adv)}
				<form action="{$smarty.const.ESYN_URL}search.php?adv" method="post" id="adv_cat_search_form">
					{if isset($smarty.post.queryFilterCat)}
						{foreach from=$smarty.post.queryFilterCat item=filter}
							<input type="hidden" name="queryFilterCat[]" value="{$filter}" />
						{/foreach}
						<input type="hidden" name="cats_only" value="1" />
						<input type="hidden" name="searchquery" value="{$smarty.post.searchquery}"/>
						<input type="hidden" name="match" value="{$smarty.post.match}" />
						<input type="hidden" name="_settings[sort]" value="{$smarty.post._settings.sort}" />
					{/if}
				</form> 
				<div><a href="#" id="adv_cat_search_submit">{$lang.more}</a></div>
			{else}
				<div><a href="{$smarty.const.ESYN_URL}search.php?what={$smarty.get.what}&cats=true">{$lang.more}</a></div>
			{/if}
		{/if}
	{include file="box-footer.tpl"}
{/if}

{esynHooker name="tplFrontSearchBeforeListings"}

{if (isset($listings) && !empty($listings) || isset($categories) && !empty($categories)) && (isset($smarty.get.what) || isset($smarty.post.searchquery))}
	<script type="text/javascript">
		var pWhat = '{if isset($smarty.post.searchquery)}{$smarty.post.searchquery|replace:"'":""}{else}{$smarty.get.what|replace:"'":""}{/if}';
	</script>

	{include_file js="js/frontend/search_highlight"}
{/if}

{if isset($listings) && !empty($listings)}
	{include file="box-header.tpl" caption=$lang.listings_found|cat:$total_listings style="fixed"}
		<div class="listings">
			{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=1}

			<table cellspacing="0" cellpadding="0" width="100%">
			{foreach from=$listings item=listing}
				{include file="listing-display.tpl"}
			{/foreach}
			</table>

			{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=1}
		</div>
	{include file="box-footer.tpl"}
	
	{include_file js="js/frontend/listing-display"}
{elseif empty($listings) && ($adv && !$showForm) || isset($smarty.get.what) and !isset($smarty.get.cats)}
	{include file="box-header.tpl" caption=$lang.listings_found|cat:$total_listings style="fixed"}
		{$lang.not_found_listings}
	{include file="box-footer.tpl"}
{/if}

{esynHooker name="searchBeforeFooter"}

{include file="footer.tpl"}
