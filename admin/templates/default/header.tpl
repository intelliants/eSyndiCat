<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
	<title>{if isset($gTitle)}{$gTitle}{/if}&nbsp;{$config.suffix}</title>
	<meta http-equiv="Content-Type" content="text/html;charset={$config.charset}" />
	<base href="{$smarty.const.ESYN_URL}{$smarty.const.ESYN_ADMIN_FOLDER}/" />

	<link rel="shortcut icon" href="{$smarty.const.ESYN_URL}favicon.ico" />

	{include_file css="js/ext/resources/css/ext-all"}

	{assign var="style_file" value=$smarty.const.ESYN_ADMIN_FOLDER|cat:'/templates/'|cat:$config.admin_tmpl|cat:'/css/style'}

	{include_file css=$style_file}

	{if isset($css)}
		{include_file css=$css}
	{/if}

	{include_file js="js/ext/ext-base, js/ext/ext-all"}
	{include_file js="js/jquery/jquery, js/jquery/plugins/jquery.interface, js/jquery/plugins/jquery.corner"}
	{include_file js="js/intelli/intelli, js/intelli/intelli.admin"}

	{assign var="js_admin_lang_file" value="tmp/cache/intelli.admin.lang."|cat:$config.lang}

	{include_file js="tmp/cache/intelli.config"}
	{include_file js=$js_admin_lang_file}

	{esynHooker name="adminHeadSection"}
</head>

<body>

<noscript>
	<div class="js_notification">{$esynI18N.error_javascript}</div>
</noscript>

<script type="text/javascript">
intelli.admin.lang = intelli.admin.lang['{$config.lang}'];
</script>

<!-- header start -->
<div class="header">

	<!-- logo start -->
	<div class="logo"><a href="http://www.esyndicat.com/"><img src="templates/{$config.admin_tmpl}/img/logos/logo.png" alt="eSyndiCat Directory Software" /></a></div>
	<!-- logo end -->

	<!-- header buttons start -->
	<div class="header-buttons">
		<ul>
			<li><a class="inner" href="{$smarty.const.ESYN_BASE_URL}" target="_blank">{$esynI18N.site_home}</a></li>
			<li><a class="inner" href="{$smarty.const.ESYN_URL}" target="_blank">{$esynI18N.directory_home}</a></li>
			<li style="padding: 0;"><a class="inner" style="background: none;" href="http://www.esyndicat.com/order.html" target="_blank"><img src="http://tools.esyndicat.com/img/purchase.png" alt="Purchase eSyndiCat Directory Script" /></a></li>
		</ul>
	</div>
	<!-- header buttons end -->

	<!-- login info start -->
	<div class="login-info">
		<ul>
			<li>{$esynI18N.howdy}, <a href="controller.php?file=admins&amp;do=edit&amp;id={$currentAdmin.id}">{$currentAdmin.username}</a></li>
			<li><a class="logout" href="logout.php" id="admin_logout">{$esynI18N.logout}</a></li>
		</ul>
	</div>
	<!-- login info end -->

	{if isset($adminHeaderMenu) && !empty($adminHeaderMenu)}
		<!-- header menu start -->

		<div class="header-menus">
			<div class="h-menu">
				<div class="h-menu-inner">
					<div class="jump-to">
						<span style="float:left;"><a>{$esynI18N.quick_jump_to}</a></span>
						<span class="h-arrow">&nbsp;</span>
						<div style="clear:both;"></div>
					</div>
					<div class="h-submenu">
					{foreach from=$adminHeaderMenu item="item"}
						{if $item.text eq 'divider'}
							<div class="h-divider"></div>
						{else}
							<a href="{$item.href}" {if isset($item.attr) && !empty($item.attr)}{$item.attr}{/if}>{$item.text}</a>
						{/if}
					{/foreach}
					</div>
				</div>
			</div>
		</div>
		<!-- header menu end -->
	{/if}
</div>
<!-- header end -->

<div class="top-menu" id="top_menu">{if isset($update_msg) && !empty($update_msg)}{$update_msg}{/if}</div>

<!-- content start -->
<div class="content" id="mainCon">

	<!-- left column start -->
	<div class="left-column" id="left-column">
		{if isset($adminMenu) && !empty($adminMenu)}
			{foreach from=$adminMenu key=key item="menu"}
				<!-- menu start -->
				<div class="menu dragGroup" id="menu_box_{$menu.name}" {if !isset($menu.items)}style="display:none;"{/if}>
					<div class="inner">
						<div class="menu-caption">{$menu.text}</div>
						<div class="minmax white-{if isset($menu.open)}{$menu.open}{else}open{/if}" id="amenu_{$menu.name}"></div>
						<div class="box-content" style="padding: 0; {if isset($menu.open) && $menu.open eq 'close'}display:none;{/if}" >
							<ul class="menu" id="menu_{$menu.name}">
							{if isset($menu.items) && !empty($menu.items)}
								{foreach from=$menu.items item="item"}
									<li{if $smarty.const.ESYN_REALM eq $item.aco} class="active"{/if}><a {print_icon_url realm=$item.aco path='menu'} href="{$item.href}" {if isset($item.attr) && !empty($item.attr)}{$item.attr}{/if} class="submenu">{$item.text}</a></li>
								{/foreach}
							{/if}
							</ul>
						</div>
					</div>
				</div>
				<!-- menu end -->
			{/foreach}
		{/if}
	</div>
	<!-- left column end -->

	<!-- right column start -->
	<div class="right-column">

		{if isset($esyn_tabs) && !empty($esyn_tabs)}
			<div class="empty-div"></div>
			{foreach from=$esyn_tabs item=esyn_tab}
				<div class="tab-content {$esyn_tab.name}" id="esyntab-content-{$esyn_tab.name}">
					<div class="tab-content-inner">
						{assign var="esyn_tab_content_key" value="tab_content_"|cat:$esyn_tab.name}
						{$esynI18N.$esyn_tab_content_key}
					</div>
				</div>
			{/foreach}

			{foreach from=$esyn_tabs item=esyn_tab}
				<div class="tab-shortcut" id="esyntab-shortcut-{$esyn_tab.name}">
					<div class="tab-shortcut-inner">
						{assign var="esyn_tab_title_key" value="tab_title_"|cat:$esyn_tab.name}
						<div class="tab-icon tab-icon-{$esyn_tab.name}">{$esynI18N.$esyn_tab_title_key}</div>
					</div>
				</div>
			{/foreach}
		{/if}

{if isset($breadcrumb)}
	{$breadcrumb}
{/if}

{if isset($gTitle)}<h1 class="common"{print_icon_url realm=$smarty.const.ESYN_REALM header=true}>{$gTitle}</h1>{/if}

{include file="buttons.tpl"}

{if isset($notifications) && !empty($notifications)}
	{include file="notification.tpl" msg=$notifications id="notif"}
{/if}

{if isset($esyndicat_messages) && !empty($esyndicat_messages)}
	{include file="messages.tpl" esyndicat_messages=$esyndicat_messages}
{/if}

{include file="notification.tpl" msg=$messages}
