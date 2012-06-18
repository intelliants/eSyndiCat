<tr>
	<td id="tdlisting{$listing.id}">

	<div class="listing featured {$listing.status}">

	{if $config.thumbshot}
		<div class="preview"><img src="http://open.thumbshots.org/image.pxf?url={$listing.url}" alt="{$listing.url}" /></div>
	{/if}
	<div class="badge"><div class="type featured">{$lang.featured}</div></div>

	<div class="title">
		<a href="{print_listing_url listing=$listing}" {if $config.new_window}target="_blank"{/if} class="countable listings title" id="lnk_{$listing.id}">{$listing.title}</a>
		
		{if isset($listing.interval) && (1 eq $listing.interval)}
			{print_img fl="new.gif" alt=$lang.new full=true}
		{/if}

		{if isset($listing.rank)}
			{section name=star loop=$listing.rank}{print_img fl="star.png" full=true}{/section}
		{/if}
	</div>

	<div class="description">{$listing.description|truncate:"300"}</div>

	<div class="url">{$listing.url}</div>
	<input type="hidden" value="{if isset($instead_thumbnail) && ($listing.$instead_thumbnail neq '')}{$smarty.const.ESYN_URL}uploads/{$listing.$instead_thumbnail}{/if}" />

	{if $config.pagerank}
		{print_pagerank pagerank=$listing.pagerank label=true}
	{/if}

	{esynHooker name="listingDisplayBeforeStats"}

	<div class="stat">({$lang.clicks}: {$listing.clicks};
		{esynHooker name="listingDisplayFieldsArea"}

		{$lang.listing_added}: {$listing.date|date_format:$config.date_format})

		<a href="{print_listing_url listing=$listing details=true}"><span class="info16" title="{$lang.listing_details}">&nbsp;</span></a>

		{if $esynAccountInfo.id eq $listing.account_id}
			<a href="{$smarty.const.ESYN_URL}suggest-listing.php?edit={$listing.id}">{print_img fl="edit_16.png" full=true alt=$lang.edit_listing title=$lang.edit_listing}</a>
		{/if}

		{if $config.broken_listings_report && not ($esynAccountInfo.id eq $listing.account_id)}
			<a href="#" class="actions_broken_{$listing.id}" rel="nofollow"><span class="report16" title="{$lang.report_broken_listing}">&nbsp;</span></a>
		{/if}

		{if $esynAccountInfo}
			{if $esynAccountInfo.id neq $listing.account_id}
				<span id="af_{$listing.id}">
				{if isset($listing.favorite) && !$listing.favorite}
					<a href="#" class="actions_add-favorite_{$listing.id}_{$esynAccountInfo.id}" rel="nofollow">{print_img fl="favorites-add_16.png" full=true alt=$lang.add_to_favorites title=$lang.add_to_favorites}</a>
				{else}
					<a href="#" class="actions_remove-favorite_{$listing.id}_{$esynAccountInfo.id}" rel="nofollow">{print_img fl="favorites-remove_16.png" full=true alt=$lang.remove_from_favorites title=$lang.remove_from_favorites}</a>
				{/if}
				</span>
			{/if}
		{/if}

		{if $esynAccountInfo.id eq $listing.account_id}
			<a href="#" class="actions_move_{$listing.id}_{$listing.category_id}">{print_img fl="move_16.png" full=true alt=$lang.move_listing title=$lang.move_listing}</a><br />
			{if $smarty.const.ESYN_REALM eq 'account_listings'}{$lang.category}: <a href="{$smarty.const.ESYN_URL}{if $config.mod_rewrite}{if $config.use_html_path}{$listing.path|cat:".html"}{else}{$listing.path}{/if}{else}index.php?category={$listing.category_id}{/if}">{$listing.category_title|escape:"html"}</a><br />{/if}
		{/if}

		{esynHooker name="listingDisplayLinksArea"}

	</div>

	</td>
</tr>
