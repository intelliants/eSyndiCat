<?php /* Smarty version 2.6.26, created on 2011-12-15 11:02:57
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'header.tpl', 11, false),array('modifier', 'escape', 'header.tpl', 18, false),array('modifier', 'default', 'header.tpl', 199, false),array('function', 'include_file', 'header.tpl', 12, false),array('function', 'esynHooker', 'header.tpl', 57, false),array('function', 'print_img', 'header.tpl', 131, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $this->_tpl_vars['title']; ?>
&nbsp;<?php echo $this->_tpl_vars['config']['suffix']; ?>
</title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $this->_tpl_vars['config']['charset']; ?>
" />
	<meta name="generator" content="eSyndiCat Web Directory Software <?php echo @ESYN_VERSION; ?>
" />
	<base href="<?php echo @ESYN_URL; ?>
" />
	<link rel="shortcut icon" href="<?php echo @ESYN_URL; ?>
favicon.ico" />

	<?php $this->assign('tpl_css', ((is_array($_tmp=((is_array($_tmp="templates/")) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['config']['tmpl']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['config']['tmpl'])))) ? $this->_run_mod_handler('cat', true, $_tmp, "/css/style") : smarty_modifier_cat($_tmp, "/css/style"))); ?>
	<?php echo smarty_function_include_file(array('css' => "templates/common/css/style, ".($this->_tpl_vars['tpl_css'])), $this);?>

	<?php if (isset ( $this->_tpl_vars['css'] )): ?>
		<?php echo smarty_function_include_file(array('css' => $this->_tpl_vars['css']), $this);?>

	<?php endif; ?>

	<?php if (isset ( $this->_tpl_vars['category'] ) && $this->_tpl_vars['category']['id'] >= 0 && isset ( $this->_tpl_vars['listings'] )): ?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['category']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" href="<?php echo @ESYN_URL; ?>
feed.php?from=category&amp;id=<?php echo $this->_tpl_vars['category']['id']; ?>
" />
	<?php endif; ?>
	
	<?php if (! isset ( $this->_tpl_vars['category'] ) && isset ( $this->_tpl_vars['view'] ) && $this->_tpl_vars['view'] != 'random' && isset ( $this->_tpl_vars['listings'] )): ?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['config']['site'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" href="<?php echo @ESYN_URL; ?>
feed.php?from=<?php echo $this->_tpl_vars['view']; ?>
" />
	<?php endif; ?>

	<meta name="description" content="<?php if (isset ( $this->_tpl_vars['description'] ) && ! empty ( $this->_tpl_vars['description'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
	<meta name="keywords" content="<?php if (isset ( $this->_tpl_vars['keywords'] ) && ! empty ( $this->_tpl_vars['keywords'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['keywords'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />

	<?php $this->assign('lang_file', ((is_array($_tmp="tmp/cache/intelli.lang.")) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['config']['lang']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['config']['lang']))); ?>
	<?php echo smarty_function_include_file(array('js' => "templates/cleancss/jquery.sessvars.intelli.resize.minmax.thumbs.search.common.footer, tmp/cache/intelli.config, ".($this->_tpl_vars['lang_file'])), $this);?>

	<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.minmax"), $this);?>

	<?php if (isset ( $this->_tpl_vars['js'] )): ?>
		<?php echo smarty_function_include_file(array('js' => $this->_tpl_vars['js']), $this);?>

	<?php endif; ?>
	
	<?php if ($this->_tpl_vars['manageMode']): ?>
		<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/jquery.interface, js/jquery/plugins/jquery.dimensions"), $this);?>

		<style type="text/css">
		<?php echo '
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

		'; ?>

		</style>
	<?php endif; ?>
	<?php echo smarty_function_esynHooker(array('name' => 'headSection'), $this);?>

	<!--[if lt IE 7]>
	<script defer type="text/javascript" src="<?php echo @ESYN_URL; ?>
js/pngfix.js"></script>
	<![endif]-->

	<script type="text/javascript">
	<?php if (isset ( $this->_tpl_vars['phpVariables'] )): ?><?php echo $this->_tpl_vars['phpVariables']; ?>
<?php endif; ?>
	intelli.lang = intelli.lang['<?php echo $this->_tpl_vars['config']['lang']; ?>
'];
	</script>

</head>

<body>

<!-- main page start -->
<div class="page" style="<?php if (isset ( $_COOKIE['cookiePageWidth'] )): ?> width: <?php echo $_COOKIE['cookiePageWidth']; ?>
;<?php endif; ?> <?php if (isset ( $_COOKIE['cookieLetterSize'] )): ?>font-size: <?php echo $_COOKIE['cookieLetterSize']; ?>
;<?php endif; ?>">

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

		<?php if ($this->_tpl_vars['config']['language_switch']): ?>
		<!-- language switch start -->
		<div class="lang-switch">
			<form name="language_form" id="language_form" action="index.php" method="get">
			<?php echo $this->_tpl_vars['lang']['language']; ?>
:
			<select name="language" id="language_select">
				<?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['select_lang'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['select_lang']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['language']):
        $this->_foreach['select_lang']['iteration']++;
?>
					<option value="<?php echo $this->_tpl_vars['code']; ?>
" <?php if ($this->_tpl_vars['code'] == @ESYN_LANGUAGE): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['language']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
			</form>
		</div>
		<!-- language switch end -->
		<?php endif; ?>

		<!-- inventory menu start -->
		<ul class="inv">
		<?php $_from = $this->_tpl_vars['menus']['inventory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['menu']):
?>
			<?php if ($this->_tpl_vars['menu']['name'] == @ESYN_REALM): ?>
				<li class="active"><?php echo $this->_tpl_vars['menu']['title']; ?>
</li>
			<?php else: ?>
				<li><a href="<?php echo $this->_tpl_vars['menu']['url']; ?>
" <?php if ($this->_tpl_vars['menu']['nofollow'] == '1'): ?>rel="nofollow"<?php endif; ?>><?php echo $this->_tpl_vars['menu']['title']; ?>
</a></li>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		<?php if ($this->_tpl_vars['config']['accounts']): ?>
			<li class="login">
			<?php if (isset ( $this->_tpl_vars['esynAccountInfo'] )): ?>
				<a href="<?php echo @ESYN_URL; ?>
logout.php?action=logout"><?php echo $this->_tpl_vars['lang']['logout']; ?>
&nbsp;[<?php echo $this->_tpl_vars['esynAccountInfo']['username']; ?>
]</a>
			<?php else: ?>
				<?php if (@ESYN_REALM == 'account_login'): ?>
					<?php echo $this->_tpl_vars['lang']['login']; ?>

				<?php else: ?>
					<a href="<?php echo @ESYN_URL; ?>
login.php"><?php echo $this->_tpl_vars['lang']['login']; ?>
</a>
				<?php endif; ?>
			<?php endif; ?>
			</li>
		<?php endif; ?>
		<?php if (isset ( $this->_tpl_vars['listings'] ) && isset ( $this->_tpl_vars['view'] )): ?>
			<li class="xml-button">
			<?php if (isset ( $this->_tpl_vars['category']['id'] )): ?>
				<a href="<?php echo @ESYN_URL; ?>
feed.php?from=category&amp;id=<?php echo $this->_tpl_vars['category']['id']; ?>
"><?php echo smarty_function_print_img(array('fl' => "xml.gif",'full' => true,'alt' => $this->_tpl_vars['lang']['xml_syndication']), $this);?>
</a>
			<?php endif; ?>
			<?php if (in_array ( $this->_tpl_vars['view'] , array ( 'popular' , 'new' , 'top' ) )): ?>
				<a href="<?php echo @ESYN_URL; ?>
feed.php?from=<?php echo $this->_tpl_vars['view']; ?>
"><?php echo smarty_function_print_img(array('fl' => "xml.gif",'full' => true,'alt' => $this->_tpl_vars['lang']['xml_syndication']), $this);?>
</a>
			<?php endif; ?>
			</li>
		<?php endif; ?>
		<?php echo smarty_function_esynHooker(array('name' => 'tplFrontHeaderAfterRSS'), $this);?>

		
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
				<a href="<?php echo @ESYN_URL; ?>
">
					<?php if ($this->_tpl_vars['config']['site_logo'] != ''): ?>
						<?php echo smarty_function_print_img(array('ups' => true,'fl' => $this->_tpl_vars['config']['site_logo'],'full' => 'true','title' => $this->_tpl_vars['config']['site'],'alt' => $this->_tpl_vars['config']['site']), $this);?>

					<?php else: ?>
						<?php echo smarty_function_print_img(array('fl' => "logo.png",'full' => true,'title' => $this->_tpl_vars['config']['site'],'alt' => $this->_tpl_vars['config']['site']), $this);?>

					<?php endif; ?>
				</a>
			</div>
			<!-- logo end -->
		</td>
		<td>
			<div class="slogan"><?php echo $this->_tpl_vars['lang']['slogan']; ?>
</div>
		</td>
		<td>
			<!-- search form start -->
			<div class="search-form">
				<form action="<?php echo @ESYN_URL; ?>
search.php" method="get" id="searchForm">
					<input type="text" class="what" name="what" size="28" id="search_input" autocomplete="off" />
					<div id="quickSearch" class="quickSearch"></div>
					<input type="submit" name="search_top" id="searchTop" value="<?php echo $this->_tpl_vars['lang']['search']; ?>
" class="button" /></td>
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
		<?php $_from = $this->_tpl_vars['menus']['main']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['menu']):
?>
			<?php if ($this->_tpl_vars['menu']['name'] == @ESYN_REALM): ?>
				<li class="active"><span><?php echo $this->_tpl_vars['menu']['title']; ?>
</span></li>
			<?php else: ?>
				<li><a href="<?php echo $this->_tpl_vars['menu']['url']; ?>
" <?php if ($this->_tpl_vars['menu']['nofollow'] == '1'): ?>rel="nofollow"<?php endif; ?>><?php echo $this->_tpl_vars['menu']['title']; ?>
</a></li>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>	
		</ul>
	</div>
	<!-- menu end -->

<div class="content">

<div id="verytopBlocks" class="groupWrapper">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['verytopBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<table class="main">
<tr>
<?php if (( isset ( $this->_tpl_vars['leftBlocks'] ) && ! empty ( $this->_tpl_vars['leftBlocks'] ) ) || $this->_tpl_vars['manageMode']): ?>
	<td class="left-column">
		<div id="leftBlocks" class="groupWrapper">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['leftBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</td>
<?php endif; ?>
<td class="center-column">

	<?php if (isset ( $this->_tpl_vars['breadcrumb'] )): ?>
		<?php echo $this->_tpl_vars['breadcrumb']; ?>

	<?php endif; ?>

	<?php echo smarty_function_esynHooker(array('name' => 'afterBreadcrumb'), $this);?>


	<?php echo smarty_function_esynHooker(array('name' => 'beforeMainContent'), $this);?>


	<div id="topBlocks" class="groupWrapper">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['topBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>