{include file="header.tpl" css="js/jquery/plugins/lightbox/css/jquery.lightbox, js/jquery/plugins/mcdropdown/jquery.mcdropdown"}

<h1>{$title}</h1>
<div id="msg"></div>

{include file="notification.tpl"}

<div class="box">
	<form action="{$smarty.const.ESYN_URL}suggest-listing.php" method="post" id="form_listing"{* enctype="multipart/form-data"*}>
	<fieldset style="collapsible">
		<legend>
			<span id="categoryTitle">
				<strong>{$category.title}</strong>
			</span> 
		</legend>

		<div id="treeContainer">
			<div id="tree" class="tree"></div>
		</div>
	</fieldset>

	{esynHooker name="editListingForm"}

	<fieldset class="collapsible">
		<legend><strong>{$lang.fields}</strong></legend>
		<div id="fields" class="fields"></div>
	</fieldset>

	{if $config.reciprocal_check}
		<div id="reciprocal">
			<fieldset class="collapsible">
				<legend><strong>{$lang.reciprocal}</strong></legend>
				{$config.reciprocal_label}<br />
				<textarea cols="50" rows="2" readonly="readonly">{$config.reciprocal_code|escape:"html"}</textarea>
			</fieldset>
		</div>
	{/if}

	<div id="gateways" style="display: none;">
		<fieldset class="collapsible">
			<legend><strong>{$lang.payment_gateway}</strong></legend>
			{esynHooker name="paymentButtons"}
		</fieldset>
	</div>

	{include file="captcha.tpl"}

	<div class="categories-tree">
		<input type="hidden" id="category_id" name="category_id" value="{$listing.category_id}" />
		<input type="hidden" name="listing_id" value="{$listing.id}" />
		<input type="submit" name="save_changes" value="{$lang.submit}" id="submit_btn" class="button" />
	</div>
	</form>
</div>

{esynHooker name="editListingBeforeIncludeJs"}

{include_file js="js/jquery/plugins/lightbox/jquery.lightbox, js/intelli/intelli.tree"}
{include_file js="js/intelli/intelli.deeplinks, js/intelli/intelli.fields, js/intelli/intelli.textcounter"}
{include_file js="js/jquery/plugins/mcdropdown/jquery.mcdropdown, js/jquery/plugins/jquery.tooltip, js/frontend/suggest-listing"}
{include_file js="js/jquery/plugins/jquery.form.ajaxLoader"}

{esynHooker name="editListingBeforeFooter"}

{include file="footer.tpl"}
