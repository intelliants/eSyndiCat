{include file="header.tpl" css="js/jquery/plugins/prettyphoto/css/prettyPhoto"}

<h1>{$listing.title|escape:"html"}</h1>

{include file="notification.tpl"}

<div class="box">
	<table cellpadding="2" cellspacing="0" width="100%">
	<tr>
		{if $config.thumbshot}
			<td valign="top" style="padding-right: 5px; width: 125px;">
				<div class="preview">
				{if isset($listing.$instead_thumbnail) and $listing.$instead_thumbnail neq ''}
					<img src="{$smarty.const.ESYN_URL}uploads/{$listing.$instead_thumbnail}" />
				{else}
					<img src="http://open.thumbshots.org/image.pxf?url={$listing.url}" alt="{$listing.url}" />
				{/if}
			</div>
			</td>
		{/if}
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="width: 7.8em;"><strong>{$lang.title}:</strong></td>
				<td><a href="{$listing.url|lower}" id="l{$listing.id}" {if $config.new_window}target="_blank"{/if}>{$listing.title}</a></td>
			</tr>
			<tr>
				<td><strong>{$lang.category}:</strong></td>
				<td>
					{if is_array($category.path)}
						{foreach from=$category.path item=cat name="categpath"}
							<a href="{print_category_url cat=$cat}">{$cat.title}</a>
							{if not $smarty.foreach.categpath.last} / {/if}
						{/foreach}
					{else}
						<a href="{print_category_url cat=$category}">{$category.title}</a>
					{/if}
				</td>
			</tr>

			<!-- Display crossed categories modification -->
			{if isset($crossed_categories) && !empty($crossed_categories)}
				<tr>
					<td><strong>{$lang.crossed_to}:</strong></td>
					<td>
						{foreach from=$crossed_categories item="crossed_category" name="crossed_category"}
							<a href="{print_category_url cat=$crossed_category}">{$crossed_category.title}</a>
							{if not $smarty.foreach.crossed_category.last},{/if}
						{/foreach}
					</td>
				</tr>
			{/if}

			<tr>
				<td><strong>{$lang.clicks}:</strong></td>
				<td>{$listing.clicks}</td>
			</tr>
			<tr>
				<td><strong>{$lang.listing_added}:</strong></td>
				<td>{$listing.date|date_format:$config.date_format}</td>
			</tr>
			
			{if $config.pagerank}
			<tr>
				<td><strong>{$lang.pagerank}:</strong></td>
				<td>
					{print_pagerank pagerank=$listing.pagerank}
				</td>
			</tr>
			{/if}

			{esynHooker name="viewListingAfterMainFieldsDisplay"}

			</table>
		</td>
	</tr>
	</table>

	{$listing.description}

	{if $fields}
		{include file="box-header.tpl" caption=$lang.fields style="fixed"}

		{esynHooker name="viewListingBeforeFieldsDisplay"}

		<table cellpadding="2" cellspacing="0" width="100%">
		{foreach from=$fields item=field}
			{assign var="key" value=$field.name}
			{assign var="field_name" value='field_'|cat:$field.name}
			{if $listing.$key || ($listing.$key eq '0')}
			<tr>
				<td style="width: 20%;"><strong>{$lang.$field_name}:</strong></td>
				<td>
					{if ($field.type eq 'text') || ($field.type eq 'textarea') || ($field.type eq 'number')}
						{$listing.$key}
					{elseif $field.type eq 'checkbox'}
						{assign var="values" value=','|explode:$listing.$key} 
						{if $values}
							{foreach name="checkbox_iter" from=$values item=field_val}
								{assign var="lang_key" value="field_"|cat:$field.name|cat:"_"|cat:$field_val}
								{$lang.$lang_key}{if !$smarty.foreach.checkbox_iter.last},&nbsp;{/if}
							{/foreach}
						{/if}
					{elseif $field.type eq 'storage'}
						<a href="{$smarty.const.ESYN_URL}uploads/{$listing.$key}">{$lang.download}</a>
					{elseif $field.type eq 'image'}
						{assign var="image_name" value="small_"|cat:$listing.$key}
						{assign var="image_path" value=$smarty.const.ESYN_HOME|cat:"uploads"|cat:$smarty.const.ESYN_DS|cat:$image_name}

						{if $image_path|file_exists}
							<a href="{$smarty.const.ESYN_URL}uploads/{$listing.$key}" target="_blank" rel="prettyPhoto">{print_img ups=true full=true fl=$image_name alt=$listing.$key}</a>
						{else}
							<a href="{$smarty.const.ESYN_URL}uploads/{$listing.$key}" target="_blank" rel="prettyPhoto">{print_img ups=true full=true fl=$listing.$key alt=$listing.$key}</a>
						{/if}
					{elseif $field.type eq 'pictures'}
						{assign var="images" value=","|explode:$listing.$key} 

						{foreach from=$images item=image}
							{assign var="image_name" value="small_"|cat:$image}
							{assign var="image_path" value=$smarty.const.ESYN_HOME|cat:"uploads"|cat:$smarty.const.ESYN_DS|cat:$image_name}

							{if $image_path|file_exists}
								<a href="{$smarty.const.ESYN_URL}uploads/{$image}" rel="prettyPhoto[gal]">{print_img ups=true full=true fl=$image_name alt=$image}</a>
							{else}
								<a href="{$smarty.const.ESYN_URL}uploads/{$image}" rel="prettyPhoto[gal]">{print_img ups=true full=true fl=$image alt=$image}</a>
							{/if}
						{/foreach}
					{elseif $field.type eq 'combo'}
						{assign var="field_combo" value="field_"|cat:$field.name|cat:'_'|cat:$listing.$key}
						{$lang.$field_combo}
					{elseif $field.type eq 'radio'}
						{assign var="field_radio" value="field_"|cat:$field.name|cat:'_'|cat:$listing.$key}
						{$lang.$field_radio}
					{/if}
				</td>
			</tr>
			{/if}
		{/foreach}
		</table>

		{esynHooker name="viewListingAfterFieldsDisplay"}

		{include file="box-footer.tpl"}
	{/if}

	{esynHooker name="tplFrontViewListingBeforeDeepLinks"}
</div>
{include_file js="js/jquery/plugins/prettyphoto/jquery.prettyPhoto, js/frontend/view-listing"}

{esynHooker name="viewListingBeforeFooter"}

{include file="footer.tpl"}
