</div>
<!-- right column end -->

<div style="clear:both;"></div>

</div>
<!-- content end -->

<!-- footer start -->
<div class="footer">
	<div>
		Powered by <a href="http://www.esyndicat.com/" target="_blank">eSyndiCat Free v{$config.version}</a><br />
		Copyright &copy; 2005-{$smarty.now|date_format:"%Y"} <a href="http://www.intelliants.com/" target="_blank">Intelliants LLC</a>
	</div>
</div>
<!-- footer end -->

{if isset($esyn_tips) && !empty($esyn_tips)}
	{foreach from=$esyn_tips key="key" item="tip"}
		<div style="display: none;"><div id="tip-content-{$tip.key}">{$tip.value}</div></div>
	{/foreach}
{/if}

<div id="ajax-loader">{$esynI18N.loading}</div>

{include_file js="js/admin/footer"}

</body>
</html>
