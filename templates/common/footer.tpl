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
			{if isset($rightBlocks) && !empty($rightBlocks)}
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
		{foreach from=$menus.bottom item=menu name="bottom_menu"}
			{if $menu.name eq $smarty.const.ESYN_REALM}
				{$menu.title}
			{else}
				<a href="{$menu.url}" {if $menu.nofollow eq '1'}rel="nofollow"{/if}>{$menu.title}</a>
			{/if}

			{if not $smarty.foreach.bottom_menu.last} | {/if}
		{/foreach}
		{esynHooker name="afterFooterLinks"}
		<div class="copyright">&copy; {$smarty.server.REQUEST_TIME|date_format:"%Y"} Powered by <a href="http://www.esyndicat.com/">eSyndiCat Directory Software</a></div>
	</div>
	<!-- footer end -->

</div>
<!-- main page end -->

<noscript>
	<div class="js_notification">{$lang.error_javascript}</div>
</noscript>

{print_img full=true fl="ajax-loader.gif" id="spinner" style="display:none;"}

<!-- thumbs preview start -->
<div class="thumb">
	<div class="loading" style="display: none;">{print_img fl="spinner.gif" full=true class="spinner"}</div>
</div>
<!-- thumbs preview end -->

{esynHooker name="footerBeforeIncludeJs"}

{include_file js="js/intelli/intelli.minmax, js/intelli/intelli.thumbs, js/intelli/intelli.search, js/intelli/intelli.common, js/frontend/footer"}

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

{esynHooker name="beforeCloseTag"}

</body>
</html>
