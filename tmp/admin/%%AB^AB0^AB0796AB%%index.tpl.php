<?php /* Smarty version 2.6.26, created on 2011-12-13 10:53:30
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/admin/templates/default/index.tpl', 95, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/index.tpl', 195, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/jquery/plugins/tweet/css/jquery.tweet")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="box_panels_content" style="margin-top: 15px;"></div>

<div id="box_statistics" style="display: none;">
	<table width="99%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="49%" valign="top">
			<table cellspacing="0" class="striped common">
			<tr>
				<th width="90%" class="first"><?php echo $this->_tpl_vars['esynI18N']['listings']; ?>
</th>
				<th width="50">&nbsp;</th>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=suspended"><?php echo $this->_tpl_vars['esynI18N']['suspended']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['listings'][2]['total']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=banned"><?php echo $this->_tpl_vars['esynI18N']['banned']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['listings'][1]['total']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=approval"><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['listings'][0]['total']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;status=active"><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['listings'][3]['total']; ?>
</strong>
				</td>
			</tr>		
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;state=destbroken"><?php echo $this->_tpl_vars['esynI18N']['broken']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['broken_listings']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;state=recipbroken"><?php echo $this->_tpl_vars['esynI18N']['nonrecip']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['no_reciprocal_listings']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;state=recipvalid"><?php echo $this->_tpl_vars['esynI18N']['reciprocal']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['reciprocal_listings']; ?>
</strong>
				</td>
			</tr>
		
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;type=featured"><?php echo $this->_tpl_vars['esynI18N']['featured']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['featured_listings']; ?>
</strong>
				</td>
			</tr>
		
			<tr>
				<td class="first">
					<a href="controller.php?file=listings&amp;type=partner"><?php echo $this->_tpl_vars['esynI18N']['partner']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['partner_listings']; ?>
</strong>
				</td>
			</tr>
		
			<tr class="last">
				<td class="first"><?php echo $this->_tpl_vars['esynI18N']['total']; ?>
:</td>
				<td><strong><?php echo $this->_tpl_vars['all_listings']; ?>
</strong></td>
			</tr>
			</table>
			
			<?php echo smarty_function_esynHooker(array('name' => 'adminIndexStats1'), $this);?>

			
		</td>
		<td style="padding-left: 15px; vertical-align: top;">
			<table cellspacing="0" class="common striped" width="99%">
			<tr>
				<th width="90%" class="first"><?php echo $this->_tpl_vars['esynI18N']['categories']; ?>
</th>
				<th width="50">&nbsp;</th>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=categories&amp;status=approval"><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['approval']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=categories&amp;status=active"><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['active']; ?>
</strong>
				</td>
			</tr>
			<tr class="last">
				<td class="first"><?php echo $this->_tpl_vars['esynI18N']['total']; ?>
:</td>
				<td><strong><?php echo $this->_tpl_vars['summary']; ?>
</strong></td>
			</tr>
			</table>
		
			<?php if ($this->_tpl_vars['config']['accounts'] && $this->_tpl_vars['currentAdmin']['super']): ?>
			<table cellspacing="0" class="common striped" width="99%">
			<tr>
				<th width="90%" class="first"><?php echo $this->_tpl_vars['esynI18N']['accounts']; ?>
</th>
				<th width="50">&nbsp;</th>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=accounts&amp;status=approval"><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</a>:
				</td>
		
				<td>
					<strong><?php echo $this->_tpl_vars['approval_accounts']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=accounts&amp;status=active"><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['active_accounts']; ?>
</strong>
				</td>
			</tr>
			<tr>
				<td class="first">
					<a href="controller.php?file=accounts&amp;status=unconfirmed"><?php echo $this->_tpl_vars['esynI18N']['unconfirmed']; ?>
</a>:
				</td>
				<td>
					<strong><?php echo $this->_tpl_vars['unconfirmed_accounts']; ?>
</strong>
				</td>
			</tr>
			<tr class="last">
				<td class="first"><?php echo $this->_tpl_vars['esynI18N']['total']; ?>
:</td>
				<td><strong><?php echo $this->_tpl_vars['all_accounts']; ?>
</strong></td>
			</tr>
			</table>
			<?php endif; ?>
			
			<?php echo smarty_function_esynHooker(array('name' => 'adminIndexStats2'), $this);?>

			
		</td>
	</tr>
	</table>
</div>

<?php echo smarty_function_esynHooker(array('name' => 'adminIndexPage'), $this);?>


<?php if (isset ( $this->_tpl_vars['esyndicat_news']['items'] ) && ! empty ( $this->_tpl_vars['esyndicat_news']['items'] )): ?>
	<div id="box_news" style="display: none;">
		<table cellspacing="0" class="striped">
		<?php $_from = $this->_tpl_vars['esyndicat_news']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['news']):
?>
			<tr>
				<td><a href="<?php echo $this->_tpl_vars['news']['link']; ?>
" target="_blank"><?php echo $this->_tpl_vars['news']['title']; ?>
</a></td>
			</tr>
		<?php endforeach; endif; unset($_from); ?>
		</table>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['config']['display_twitter']): ?>
	<div id="box_twitter" class="twitter" style="display: none;"></div>
<?php endif; ?>

<div id="box_fdb" style="display: none;">

</div>

<script type="text/javascript" src="get-state.php"></script>

<?php echo smarty_function_include_file(array('js' => "js/ext/plugins/portal/Portal, js/ext/plugins/portal/PortalColumn, js/ext/plugins/portal/Portlet, js/ext/plugins/portal/overrides, js/ext/plugins/httpprovider/httpprovider, js/jquery/plugins/tweet/jquery.tweet, js/admin/index"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'adminIndexAfterIncludeJs'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>