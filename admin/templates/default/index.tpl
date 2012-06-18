{include file="header.tpl" css="js/jquery/plugins/tweet/css/jquery.tweet"}

<div id="box_panels_content" style="margin-top: 15px;"></div>

<div id="box_statistics" style="display: none;">
	<table width="99%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="49%" valign="top">
			<table cellspacing="0" class="striped common">
			<tr>
				<th width="90%" class="first">{$esynI18N.listings}</th>
				<th width="50">&nbsp;</th>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=suspended">{$esynI18N.suspended}</a>:
				</td>
				<td>
					<strong>{$listings[2].total}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=banned">{$esynI18N.banned}</a>:
				</td>
				<td>
					<strong>{$listings[1].total}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=approval">{$esynI18N.approval}</a>:
				</td>
				<td>
					<strong>{$listings[0].total}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=active">{$esynI18N.active}</a>:
				</td>
				<td>
					<strong>{$listings[3].total}</strong>
				</td>
			</tr>		
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;state=destbroken">{$esynI18N.broken}</a>:
				</td>
				<td>
					<strong>{$broken_listings}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;state=recipbroken">{$esynI18N.nonrecip}</a>:
				</td>
				<td>
					<strong>{$no_reciprocal_listings}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;state=recipvalid">{$esynI18N.reciprocal}</a>:
				</td>
				<td>
					<strong>{$reciprocal_listings}</strong>
				</td>
			</tr>
		
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;type=featured">{$esynI18N.featured}</a>:
				</td>
				<td>
					<strong>{$featured_listings}</strong>
				</td>
			</tr>
		
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;type=partner">{$esynI18N.partner}</a>:
				</td>
				<td>
					<strong>{$partner_listings}</strong>
				</td>
			</tr>
		
			<tr class="last">
				<td class="first">{$esynI18N.total}:</td>
				<td><strong>{$all_listings}</strong></td>
			</tr>
			</table>
			
			{esynHooker name="adminIndexStats1"}
			
		</td>
		<td style="padding-left: 15px; vertical-align: top;">
			<table cellspacing="0" class="common striped" width="99%">
			<tr>
				<th width="90%" class="first">{$esynI18N.categories}</th>
				<th width="50">&nbsp;</th>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=categories&amp;status=approval">{$esynI18N.approval}</a>:
				</td>
				<td>
					<strong>{$approval}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=categories&amp;status=active">{$esynI18N.active}</a>:
				</td>
				<td>
					<strong>{$active}</strong>
				</td>
			</tr>
			<tr class="last">
				<td class="first">{$esynI18N.total}:</td>
				<td><strong>{$summary}</strong></td>
			</tr>
			</table>
		
			{if $config.accounts && $currentAdmin.super}
			<table cellspacing="0" class="common striped" width="99%">
			<tr>
				<th width="90%" class="first">{$esynI18N.accounts}</th>
				<th width="50">&nbsp;</th>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=accounts&amp;status=approval">{$esynI18N.approval}</a>:
				</td>
		
				<td>
					<strong>{$approval_accounts}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=accounts&amp;status=active">{$esynI18N.active}</a>:
				</td>
				<td>
					<strong>{$active_accounts}</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=accounts&amp;status=unconfirmed">{$esynI18N.unconfirmed}</a>:
				</td>
				<td>
					<strong>{$unconfirmed_accounts}</strong>
				</td>
			</tr>
			<tr class="last">
				<td class="first">{$esynI18N.total}:</td>
				<td><strong>{$all_accounts}</strong></td>
			</tr>
			</table>
			{/if}
			
			{esynHooker name="adminIndexStats2"}
			
		</td>
	</tr>
	</table>
</div>

{esynHooker name="adminIndexPage"}

{if isset($esyndicat_news.items) && !empty($esyndicat_news.items)}
	<div id="box_news" style="display: none;">
		<table cellspacing="0" class="striped">
		{foreach from=$esyndicat_news.items item=news}
			<tr>
				<td><a href="{$news.link}" target="_blank">{$news.title}</a></td>
			</tr>
		{/foreach}
		</table>
	</div>
{/if}

{if $config.display_twitter}
	<div id="box_twitter" class="twitter" style="display: none;"></div>
{/if}

<div id="box_fdb" style="display: none;">

</div>

<script type="text/javascript" src="get-state.php"></script>

{include_file js="js/ext/plugins/portal/Portal, js/ext/plugins/portal/PortalColumn, js/ext/plugins/portal/Portlet, js/ext/plugins/portal/overrides, js/ext/plugins/httpprovider/httpprovider, js/jquery/plugins/tweet/jquery.tweet, js/admin/index"}

{esynHooker name="adminIndexAfterIncludeJs"}

{include file="footer.tpl"}
