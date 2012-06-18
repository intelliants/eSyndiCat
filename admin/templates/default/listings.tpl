{include file="header.tpl" css="js/ext/plugins/panelresizer/css/PanelResizer"}

<div id="box_listings" style="margin-top: 15px;"></div>

<div id="remove_reason" style="display: none;">
	{$esynI18N.listing_remove_reason}<br />
	<textarea cols="40" rows="5" name="body" id="remove_reason_text" class="common" style="width: 99%;"></textarea>
</div>

{include_file js="js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/rowexpander/rowExpander, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/listings"}

{include file="footer.tpl"}
