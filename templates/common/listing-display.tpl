{esynHooker name="beforeListingDisplay"}

{if $listing.partner}
	{include file=$printpartner}
{elseif $listing.featured}
	{include file=$printfeatured}
{else}
	{include file=$printregular}
{/if}
