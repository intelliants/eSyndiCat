<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>{$title}&nbsp;{$config.suffix}</title>
	<meta http-equiv="Content-Type" content="text/html;charset={$config.charset}" />
	<meta name="generator" content="eSyndiCat Web Directory Software {$smarty.const.ESYN_VERSION}" />
	<base href="{$smarty.const.ESYN_URL}" />
	<link rel="shortcut icon" href="{$smarty.const.ESYN_URL}favicon.ico" />

	{assign var=tpl_css value="templates/"|cat:$config.tmpl|cat:"/css/style"}
	{include_file css="templates/common/css/style, $tpl_css"}
	{if isset($css)}
		{include_file css=$css}
	{/if}

	{if isset($category) && $category.id >= 0 && isset($listings)}
		<link rel="alternate" type="application/rss+xml" title="{$category.title|escape:"html"}" href="{$smarty.const.ESYN_URL}feed.php?from=category&amp;id={$category.id}" />
	{/if}
	
	{if !isset($category) && isset($view) && $view neq 'random' && isset($listings)}
		<link rel="alternate" type="application/rss+xml" title="{$config.site|escape:"html"}" href="{$smarty.const.ESYN_URL}feed.php?from={$view}" />
	{/if}

	<meta name="description" content="{if isset($description) && !empty($description)}{$description|escape:"html"}{/if}" />
	<meta name="keywords" content="{if isset($keywords) && !empty($keywords)}{$keywords|escape:"html"}{/if}" />

	{assign var="lang_file" value=$smarty.const.ESYN_TMP_NAME|cat:"/cache/intelli.lang."|cat:$config.lang}

	{include_file js="js/jquery/jquery, js/utils/sessvars, js/intelli/intelli, js/intelli/intelli.resize"}
		{include_file js=$smarty.const.ESYN_TMP_NAME|cat:"/cache/intelli.config"}
	{include_file js=$lang_file}

	{if isset($js)}
		{include_file js=$js}
	{/if}

	{include_file js="js/intelli/intelli.minmax, js/intelli/intelli.thumbs, js/intelli/intelli.search, js/intelli/intelli.common, js/frontend/footer"}

	{if $manageMode}
		{include_file js="js/jquery/plugins/jquery.interface, js/jquery/plugins/jquery.dimensions"}
		<style type="text/css">
		{literal}
			div.groupWrapper
			{
				background-color:lightgreen;
				border:1px dotted whitesmoke;
			}
			.dropActive
			{
				padding:5px;
			}
			.dropHover
			{
				background: lightgreen;
				padding:0;
			}

		{/literal}
		</style>
	{/if}
	{esynHooker name="headSection"}
	<!--[if lt IE 7]>
	<script defer type="text/javascript" src="{$smarty.const.ESYN_URL}js/pngfix.js"></script>
	<![endif]-->

	<script type="text/javascript">
	{if isset($phpVariables)}{$phpVariables}{/if}
	intelli.lang = intelli.lang['{$config.lang}'];
	</script>

</head>

<body>

<!-- main page start -->
<div class="page" style="{if isset($smarty.cookies.cookiePageWidth)} width: {$smarty.cookies.cookiePageWidth};{/if} {if isset($smarty.cookies.cookieLetterSize)}font-size: {$smarty.cookies.cookieLetterSize};{/if}">

	<!-- page resize start -->
	<div id="page_setup">
		<ul class="page_setup">
			<li id="small">&nbsp;</li>
			<li id="normal">&nbsp;</li>
			<li id="large">&nbsp;</li>
			<li class="space">&nbsp;</li>
			<li id="w800">&nbsp;</li>
			<li id="w1024">&nbsp;</li>
			<li id="wLiquid">&nbsp;</li>
		</ul>
	</div>
	<!-- page resize end -->

	<!-- inventory line start -->
	<div class="inventory">

		{if $config.language_switch}
		<!-- language switch start -->
		<div class="lang-switch">
			<form name="language_form" id="language_form" action="index.php" method="get">
			{$lang.language}:
			<select name="language" id="language_select">
				{foreach from=$languages key=code item=language name=select_lang}
					<option value="{$code}" {if $code eq $smarty.const.ESYN_LANGUAGE}selected="selected"{/if}>{$language}</option>
				{/foreach}
			</select>
			</form>
		</div>
		<!-- language switch end -->
		{/if}

		<!-- inventory menu start -->
		<ul class="inv">
		{foreach from=$menus.inventory item=menu}
			{if $menu.name eq $smarty.const.ESYN_REALM}
				<li class="active">{$menu.title}</li>
			{else}
				<li><a href="{$menu.url}" {if $menu.nofollow eq '1'}rel="nofollow"{/if}>{$menu.title}</a></li>
			{/if}
		{/foreach}
		{if $config.accounts}
			<li class="login">
			{if isset($esynAccountInfo)}
				<a href="{$smarty.const.ESYN_URL}logout.php?action=logout">{$lang.logout}&nbsp;[{$esynAccountInfo.username}]</a>
			{else}
				{if $smarty.const.ESYN_REALM eq 'account_login'}
					{$lang.login}
				{else}
					<a href="{$smarty.const.ESYN_URL}login.php">{$lang.login}</a>
				{/if}
			{/if}
			</li>
		{/if}
		{if isset($listings) && isset($view)}
			<li class="xml-button">
			{if isset($category.id)}
				<a href="{$smarty.const.ESYN_URL}feed.php?from=category&amp;id={$category.id}">{print_img fl="xml.gif" full=true alt=$lang.xml_syndication}</a>
			{/if}
			{if in_array($view, array('popular','new','top'))}
				<a href="{$smarty.const.ESYN_URL}feed.php?from={$view}">{print_img fl="xml.gif" full=true alt=$lang.xml_syndication}</a>
			{/if}
			</li>
		{/if}
		{esynHooker name="tplFrontHeaderAfterRSS"}
		
		</ul>
		<!-- inventory menu end -->

	</div>
	<!-- invenotory line end -->

	<div class="header">

	<table>
	<tr>
		<td>
			<!-- logo start -->
			<div class="logo">
				<a href="{$smarty.const.ESYN_URL}">
					{if $config.site_logo neq ''}
						{print_img ups=true fl=$config.site_logo full="true" title=$config.site alt=$config.site}
					{else}
						{print_img fl="logo.png" full=true title=$config.site alt=$config.site}
					{/if}
				</a>
			</div>
			<!-- logo end -->
		</td>
		<td>
			<div class="slogan">{$lang.slogan}</div>
		</td>
		<td>
			<!-- search form start -->
			<div class="search-form">
				<form action="{$smarty.const.ESYN_URL}search.php" method="get" id="searchForm">
					<input type="text" class="what" name="what" size="28" id="search_input" autocomplete="off" />
					<div id="quickSearch" class="quickSearch"></div>
					<input type="submit" name="search_top" id="searchTop" value="{$lang.search}" class="button" /></td>
				</form>
			</div>
			<!-- search form end -->
		</td>
	</tr>
	</table>

	</div>

	<!-- menu start -->
	<div class="top-menu">
		<ul class="menu">
		{foreach from=$menus.main item=menu}
			{if $menu.name eq $smarty.const.ESYN_REALM}
				<li class="active"><span>{$menu.title}</span></li>
			{else}
				<li><a href="{$menu.url}" {if $menu.nofollow eq '1'}rel="nofollow"{/if}>{$menu.title}</a></li>
			{/if}
		{/foreach}	
		</ul>
	</div>
	<!-- menu end -->

<div class="content">

<div id="verytopBlocks" class="groupWrapper">
	{include file="parse-blocks.tpl" pos=$verytopBlocks|default:null}
</div>

<table class="main">
<tr>
{if (isset($leftBlocks) && !empty($leftBlocks)) || $manageMode}
	<td class="left-column">
		<div id="leftBlocks" class="groupWrapper">
			{include file="parse-blocks.tpl" pos=$leftBlocks|default:null}
		</div>
	</td>
{/if}
<td class="center-column">

	{if isset($breadcrumb)}
		{$breadcrumb}
	{/if}

	{esynHooker name="afterBreadcrumb"}

	{esynHooker name="beforeMainContent"}

	<div id="topBlocks" class="groupWrapper">
		{include file="parse-blocks.tpl" pos=$topBlocks|default:null}
	</div>
