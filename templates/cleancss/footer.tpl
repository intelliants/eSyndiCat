				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" style="width: 50%;">
						<div id="user1Blocks" class="groupWrapper">
							{include file="parse-blocks.tpl" pos=$user1Blocks|default:null}
						</div>
					</td>
					<td valign="top" style="width: 50%; padding-left: 10px;">
						<div id="user2Blocks" class="groupWrapper">
							{include file="parse-blocks.tpl" pos=$user2Blocks|default:null}
						</div>
					</td>
				</tr>
				</table>
				<div id="bottomBlocks" class="groupWrapper">
					{include file="parse-blocks.tpl" pos=$bottomBlocks|default:null}
				</div>
			</td>
			{if (isset($rightBlocks) && !empty($rightBlocks)) || $manageMode}
				<td class="right-column" valign="top">
					<div id="rightBlocks" class="groupWrapper">
						{include file="parse-blocks.tpl" pos=$rightBlocks|default:null}
					</div>
				</td>
			{/if}
		</tr>
		</table>

		<!-- verybottom block -->
		<div id="verybottomBlocks" class="groupWrapper">
			{include file="parse-blocks.tpl" pos=$verybottomBlocks|default:null}
		</div>
		<!-- verybottom block -->

	</div>
	<!-- content end -->
	
	<!-- footer start -->
	<div class="footer">
		{esynHooker name="beforeFooterLinks"}
		<a href="#">About Us</a> |
		<a href="#">Privacy Policy</a> |
		<a href="#">Terms of Use</a> |
		<a href="#">Help</a> |
		<a href="#">Advertise Us</a>
		{esynHooker name="afterFooterLinks"}
		<div class="copyright">&copy; {$smarty.server.REQUEST_TIME|date_format:"%Y"} Powered by <a href="http://www.esyndicat.com/">eSyndiCat Directory Software</a></div>
	</div>
	<!-- footer end -->

</div>
<!-- main page end -->

<noscript>
	<div class="js_notification">{$lang.error_javascript}</div>
</noscript>

<!-- thumbs preview start -->
<div class="thumb">
	<div class="loading" style="display: none;"></div>
</div>
<!-- thumbs preview end -->

{esynHooker name="footerBeforeIncludeJs"}

{if $manageMode}
	<div id="mod_box" class="mode">
		{$lang.youre_in_manage_mode}. <a href="?switchToNormalMode=y" style="font-weight: bold; color: #FFF;">{$lang.exit}</a>
	</div>
	{include_file js="js/frontend/visual-mode"}
{/if}

{if isset($smarty.get.preview) || isset($smarty.session.preview)}
	<div id="mod_box" class="mode">
		{$lang.youre_in_preview_mode} <a href="?switchToNormalMode=y" style="font-weight: bold; color: #FFF;">{$lang.exit}</a>
	</div>
{/if}
{if $config.cron}<div style="display:none"><img src="cron.php" width="1" height="1" alt="" /></div>{/if}

{esynHooker name="beforeCloseTag"}

</body>
</html>
