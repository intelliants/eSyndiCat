<?php /* Smarty version 2.6.26, created on 2011-12-13 10:58:07
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/database.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/database.tpl', 26, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/admin/templates/default/database.tpl', 238, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/database.tpl', 306, false),array('modifier', 'upper', '/home/vbezruchkin/www/v1700/admin/templates/default/database.tpl', 218, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($_GET['page'] == 'export'): ?>
	<?php if (isset ( $this->_tpl_vars['backup_is_not_writeable'] )): ?>
		<div class="message alert" id="backup_message">
			<div class="inner">
				<div class="icon"></div>
				<ul>
					<li><?php echo $this->_tpl_vars['backup_is_not_writeable']; ?>
</li>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if (isset ( $this->_tpl_vars['out_sql'] ) && ! empty ( $this->_tpl_vars['out_sql'] )): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['export'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<textarea class="common" style="margin-top: 10px;" rows="24" cols="15" readonly="readonly">
				<?php echo $this->_tpl_vars['out_sql']; ?>

			</textarea>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['export'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<form action="controller.php?file=database&amp;page=export" method="post" name="dump" id="dump">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table width="100%" cellspacing="0" cellpadding="0" class="striped">
	<tr class="tr">
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['export']; ?>
:</strong></td>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['mysql_options']; ?>
:</strong></td>
	</tr>
	<tr>
		<td valign="top">
			<select name="tbl[]" id="tbl" size="7" multiple="multiple" style="font-size: 12px; font-family: Verdana;">
			
			<?php $_from = $this->_tpl_vars['tables']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['table']):
?>
				<option value="<?php echo $this->_tpl_vars['table']; ?>
"><?php echo $this->_tpl_vars['table']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>

			</select>
			
			<div style="margin-top: 5px; text-align: center;" class="selecting">
				<a href="#" class="select"><?php echo $this->_tpl_vars['esynI18N']['select_all']; ?>
</a>&nbsp;/&nbsp;
				<a href="#" class="deselect"><?php echo $this->_tpl_vars['esynI18N']['select_none']; ?>
</a>
			</div>
		</td>
		<td align="left" width="100%">
			<table cellspacing="1" width="100%" class="striped">
			<tr>
				<td style="background-color: #E5E5E5;">
					<input type="checkbox" name="sql_structure" value="structure" id="sql_structure" <?php if (isset ( $_POST['sql_structure'] ) || ! $_POST): ?>checked="checked"<?php endif; ?> style="vertical-align: middle" />
					<label for="sql_structure"><b><?php echo $this->_tpl_vars['esynI18N']['structure']; ?>
:</b></label><br />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" name="drop" value="1" <?php if (isset ( $_POST['drop'] ) && $_POST['drop'] == '1'): ?>checked="checked"<?php endif; ?> id="dump_drop" style="vertical-align: middle" />
					<label for="dump_drop"><?php echo $this->_tpl_vars['esynI18N']['add_drop_table']; ?>
</label>
				</td>
			</tr>
			<tr>
				<td style="background-color: #E5E5E5;">
					<input type="checkbox" name="sql_data" value="data" id="sql_data" <?php if (isset ( $_POST['sql_data'] ) || ! $_POST): ?>checked="checked"<?php endif; ?> style="vertical-align: middle" />
					<label for="sql_data"><b>Data:</b></label><br />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" name="showcolumns" value="1" <?php if (isset ( $_POST['showcolumns'] ) && $_POST['showcolumns'] == '1'): ?>checked="checked"<?php endif; ?> id="dump_showcolumns" style="vertical-align: middle" />
					<label for="dump_showcolumns"><?php echo $this->_tpl_vars['esynI18N']['complete_inserts']; ?>
</label>
				</td>
			</tr>
			<tr>
				<td style="background-color: #E5E5E5;">
					<input type="checkbox" name="real_prefix" id="real_prefix" <?php if (isset ( $_POST['real_prefix'] ) || ! $_POST): ?>checked="checked"<?php endif; ?> style="vertical-align: middle" />
					<label for="real_prefix"><b><?php echo $this->_tpl_vars['esynI18N']['use_real_prefix']; ?>
</b></label><br />
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="tr">
			<input type="checkbox" name="save_file" id="save_file" style="vertical-align: middle" />
			<label for="save_file"><b><?php echo $this->_tpl_vars['esynI18N']['save_as_file']; ?>
</b></label><br />
		</td>
	</tr>
	</table>

	<div id="save_to" style="display: none;">
		<table width="100%" cellpadding="0" cellspacing="0" class="striped">
		<tr class="tr">
			<td width="50%" style="padding-left: 10px;">
				<input type="radio" name="savetype" value="server" id="server" /><label for="server"><?php echo $this->_tpl_vars['esynI18N']['save_to_server']; ?>
</label>&nbsp;
				<input type="radio" name="savetype" value="client" id="client" <?php if (isset ( $_POST['savetype'] ) && $_POST['savetype'] == 'client' || ! $_POST): ?>checked="checked"<?php endif; ?> /><label for="client"><?php echo $this->_tpl_vars['esynI18N']['save_to_pc']; ?>
</label>&nbsp;
			</td>
			<td style="padding-right: 20px; text-align: right;">
				<input type="checkbox" name="gzip_compress" id="gzip_compress" <?php if (isset ( $_POST['gzip_compress'] ) || ! $_POST): ?>checked="checked"<?php endif; ?> style="vertical-align: middle" /> 
				<label for="gzip_compress"><?php echo $this->_tpl_vars['esynI18N']['gzip_compress']; ?>
</label>
			</td>
		</tr>
		</table>
	</div>

	<table width="100%" cellpadding="0" cellspacing="0" class="striped">
		<tr class="all">
			<td colspan="2" align="right">
				<input type="button" id="exportAction" value="<?php echo $this->_tpl_vars['esynI18N']['go']; ?>
" class="common" />
				<input type="hidden" name="export" id="export" />
			</td>
		</tr>
	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($_GET['page'] == 'import'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['import'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<form action="controller.php?file=database&amp;page=import" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table width="100%" cellspacing="0" class="striped">
	
	<?php if ($this->_tpl_vars['upgrades']): ?>
		<tr class="tr">
			<td><strong><?php echo $this->_tpl_vars['esynI18N']['choose_import_file']; ?>
:</strong></td>
		</tr>
		<tr>
			<td width="50%">
				<select name="sqlfile">
					<?php $_from = $this->_tpl_vars['upgrades']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['value']):
?>
						<option value="<?php echo $this->_tpl_vars['value']; ?>
" <?php if (isset ( $_POST['sqlfile'] ) && $_POST['sqlfile'] == $this->_tpl_vars['value']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['value']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>
			</td>
		</tr>
		<tr class="all tr">
			<td align="right"><input type="submit" name="run_update" value="<?php echo $this->_tpl_vars['esynI18N']['go']; ?>
" class="common" /></td>
		</tr>
	<?php else: ?>
		<tr class="tr">
			<td><strong><?php echo $this->_tpl_vars['esynI18N']['no_upgrades']; ?>
</strong></td>
		</tr>
	<?php endif; ?>
	
	</table>
	</form>

	<form enctype="multipart/form-data" action="controller.php?file=database&amp;page=import" method="post" name="update" id="update">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table cellpadding="0" cellspacing="0" width="100%" class="striped">
	<tr class="tr">
		<td class="caption"><strong><?php echo $this->_tpl_vars['esynI18N']['choose_import_file']; ?>
</strong></td>
	</tr>
	<tr class="tr">
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['location_sql_file']; ?>
:</strong></td>
	</tr>
	<tr>
		<td>
			<input type="file" name="sql_file" id="sql_file" class="textfield" />&nbsp;(Max: 2,048KB)<br />
			<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
		</td>
	</tr>
	<tr class="all tr">
		<td align="right">
			<input type="button" id="importAction" value="<?php echo $this->_tpl_vars['esynI18N']['go']; ?>
" class="common" />
			<input type="hidden" name="run_update" id="run_update" />
		</td>
	</tr>
	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($_GET['page'] == 'sql'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<form action="controller.php?file=database&amp;page=sql" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table width="100%" cellspacing="0" cellpadding="0" class="striped">
	<tr style="font-weight: bold;" class="tr">
		<td><?php echo $this->_tpl_vars['esynI18N']['run_sql_queries']; ?>
:</td>
		<td>&nbsp;</td>
		<td><?php echo $this->_tpl_vars['esynI18N']['tables_fields']; ?>
:</td>
	</tr>
	<tr>
		<td width="99%" valign="top" rowspan="2">
			<textarea class="noresize" rows="4" cols="4" name="query" id="query" style="height: 200px; width: 100%; font-size: 12px; font-family: Verdana;"><?php if (isset ( $_POST['show_query'] ) && $_POST['show_query'] == '1' && isset ( $this->_tpl_vars['sql_query'] ) && $this->_tpl_vars['sql_query'] != ''): ?><?php echo $this->_tpl_vars['sql_query']; ?>
<?php else: ?>SELECT * FROM <?php endif; ?></textarea>
		</td>
		<td width="50" height="20"><input type="button" value="&#171;" id="addTableButton" />&nbsp;</td>
		<td width="30" valign="top">
			<select name="table" id="table" size="10" style="font-size: 12px; font-family: Verdana;">
				<?php $_from = $this->_tpl_vars['tables']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['table']):
?>
					<option value="<?php echo $this->_tpl_vars['table']; ?>
"><?php echo $this->_tpl_vars['table']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</td>
	</tr>
	<tr style="background-image: none;">
		<td height="80"><input type="button" value="&#171;" id="addFieldButton" style="display: none;"/>&nbsp;</td>
		<td>
			<select name="field" id="field" size="5" style="font-size: 12px; font-family: Verdana; display: none;"><option>&nbsp;</option></select>
		</td>
	</tr>
	<tr class="all tr">
	<td>
		<input type="checkbox" name="show_query" value="1" id="sh1" style="vertical-align: middle" <?php if (isset ( $_POST['show_query'] ) && $_POST['show_query'] == '1' || ! $_POST): ?>checked="checked"<?php endif; ?> />
		<label for="sh1"><?php echo $this->_tpl_vars['esynI18N']['show_query_again']; ?>
</label>
	</td>
	<td colspan="2" align="right">
		<input type="submit" value="<?php echo $this->_tpl_vars['esynI18N']['go']; ?>
" name="exec_query" class="common small" />
		<input type="button" value="<?php echo $this->_tpl_vars['esynI18N']['clear']; ?>
" id="clearButton" class="common small" />
	</td>
	</tr>
	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<?php if (isset ( $this->_tpl_vars['queryOut'] ) && $this->_tpl_vars['queryOut'] != ''): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['import'],'style' => "overflow: auto;")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php echo $this->_tpl_vars['queryOut']; ?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php elseif ($_GET['page'] == 'consistency'): ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<ul style="font-size:14px;">
	<li style="margin:5px">
		<span style="display:block; float:left; width:210px; margin-right:10px;"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['active_listings_count'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</b>:</span>
		<a href="controller.php?file=database&amp;page=consistency&amp;type=num_all_listings"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['recount'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</a>
	</li>
	<li style="margin:5px">
		<span style="display:block; float:left; width:210px; margin-right:10px;"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['categories_relation'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</b>:</span>
		<a href="controller.php?file=database&amp;page=consistency&amp;type=categories_relation"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['repair'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</a>
	</li>
	<li style="margin:5px">
		<span style="display:block; float:left; width:210px; margin-right:10px;"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['listings_and_categories'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</b>:</span>
		<a href="controller.php?file=database&amp;page=consistency&amp;type=listing_categories"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['find_and_delete'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</a>
	</li>
	<li style="margin:5px">
		<span style="display:block; float:left; width:210px; margin-right:10px;"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['repair_tables'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</b>:</span>
		<a href="controller.php?file=database&amp;page=consistency&amp;type=repair_tables"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['repair'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</a>
	</li>
	<li style="margin:5px">
		<span style="display:block; float:left; width:210px; margin-right:10px;"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['optimize_tables'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</b>:</span>
		<a href="controller.php?file=database&amp;page=consistency&amp;type=optimize_tables"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['optimize_tables'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</a>
	</li>
	
	<?php echo smarty_function_esynHooker(array('name' => 'adminDatabaseConsistency'), $this);?>

	
	</ul>
	
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
<?php elseif ($_GET['page'] == 'reset'): ?>
	<?php if (isset ( $this->_tpl_vars['reset_options'] ) && ! empty ( $this->_tpl_vars['reset_options'] )): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<form action="controller.php?file=database&amp;page=reset" method="post">
			<?php echo esynUtil::preventCsrf(array(), $this);?>

			<table width="100%" cellspacing="0" cellpadding="0" class="striped">
			<tr>
				<td width="100"><label for="all_options"><?php echo $this->_tpl_vars['esynI18N']['reset_all']; ?>
</label></td>
				<td><input type="checkbox" value="all" name="all_options" id="all_options" /></td>
			</tr>
			
			<?php $_from = $this->_tpl_vars['reset_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['option']):
?>
				<tr>
					<td><label for="option_<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['option']; ?>
<label></td>
					<td><input type="checkbox" id="option_<?php echo $this->_tpl_vars['key']; ?>
" name="options[]" value="<?php echo $this->_tpl_vars['key']; ?>
" /></td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
			
			<tr>
				<td rowspan="2">
					<input type="submit" name="reset" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['reset']; ?>
" />
				</td>
			</tr>
			</table>
			</form>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php elseif ($_GET['page'] == 'hook_editor'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['hook_editor'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<table class="striped" width="98%" cellpadding="4" cellspacing="0">
	<tr>
		<td width="10%">
			<select id="hook">
			<?php $_from = $this->_tpl_vars['hooks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['hook']):
?>
				<option value="<?php echo $this->_tpl_vars['hook']['id']; ?>
"><?php echo $this->_tpl_vars['hook']['name']; ?>
&nbsp;|&nbsp;<?php echo $this->_tpl_vars['hook']['plugin']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
			</select>
		</td>

		<td>
			<input type="button" class="common" id="show" value="Show Code" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="code" id="codeContainer" class="common codepress php" cols="10" rows="20"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" class="common" name="save" id="save" value="Save" />
			<input type="submit" class="common" id="close_all" value="Close All" />
		</td>
	</tr>
	</table>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplAdminDatabaseBeforeFooter'), $this);?>


<?php echo smarty_function_include_file(array('js' => "js/admin/database"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>