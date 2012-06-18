<?php /* Smarty version 2.6.26, created on 2011-12-15 11:06:54
         compiled from /home/vbezruchkin/www/v1700/plugins/comments/templates/comments.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/vbezruchkin/www/v1700/plugins/comments/templates/comments.tpl', 8, false),array('modifier', 'date_format', '/home/vbezruchkin/www/v1700/plugins/comments/templates/comments.tpl', 8, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/plugins/comments/templates/comments.tpl', 68, false),)), $this); ?>
<div id="comments_container">
<?php if ($this->_tpl_vars['comments']): ?>
	<?php $_from = $this->_tpl_vars['comments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['comments'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['comments']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['comment']):
        $this->_foreach['comments']['iteration']++;
?>
		<div class="posted">
			<?php if ($this->_tpl_vars['config']['listing_rating']): ?>
				<?php unset($this->_sections['star']);
$this->_sections['star']['name'] = 'star';
$this->_sections['star']['loop'] = is_array($_loop=$this->_tpl_vars['comment']['rating']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['star']['show'] = true;
$this->_sections['star']['max'] = $this->_sections['star']['loop'];
$this->_sections['star']['step'] = 1;
$this->_sections['star']['start'] = $this->_sections['star']['step'] > 0 ? 0 : $this->_sections['star']['loop']-1;
if ($this->_sections['star']['show']) {
    $this->_sections['star']['total'] = $this->_sections['star']['loop'];
    if ($this->_sections['star']['total'] == 0)
        $this->_sections['star']['show'] = false;
} else
    $this->_sections['star']['total'] = 0;
if ($this->_sections['star']['show']):

            for ($this->_sections['star']['index'] = $this->_sections['star']['start'], $this->_sections['star']['iteration'] = 1;
                 $this->_sections['star']['iteration'] <= $this->_sections['star']['total'];
                 $this->_sections['star']['index'] += $this->_sections['star']['step'], $this->_sections['star']['iteration']++):
$this->_sections['star']['rownum'] = $this->_sections['star']['iteration'];
$this->_sections['star']['index_prev'] = $this->_sections['star']['index'] - $this->_sections['star']['step'];
$this->_sections['star']['index_next'] = $this->_sections['star']['index'] + $this->_sections['star']['step'];
$this->_sections['star']['first']      = ($this->_sections['star']['iteration'] == 1);
$this->_sections['star']['last']       = ($this->_sections['star']['iteration'] == $this->_sections['star']['total']);
?><img src="plugins/comments/templates/img/gold.png" alt="" /><?php endfor; endif; ?>
			<?php endif; ?>
			<?php echo $this->_tpl_vars['lang']['comment_author']; ?>
 <strong><?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</strong> / <?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['date_format'])); ?>

		</div>
		<div class="comment">
			<?php if ($this->_tpl_vars['config']['html_comments']): ?>
				<?php echo $this->_tpl_vars['comment']['body']; ?>

			<?php else: ?>
				<?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['body'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

			<?php endif; ?>
		</div>
		<?php if (! ($this->_foreach['comments']['iteration'] == $this->_foreach['comments']['total'])): ?><hr /><?php else: ?><div style="height: 15px;">&nbsp;</div><?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
</div>

<div id="error" style="margin-bottom: 10px; display: none;"></div>

<?php if (! $this->_tpl_vars['config']['allow_listing_comments_submission']): ?>
	<div class="notification"><ul><li><?php echo $this->_tpl_vars['lang']['listing_comments_submission_disabled']; ?>
</li></ul></div>
<?php else: ?>
	<?php if (! $this->_tpl_vars['config']['listing_comments_accounts'] && ! $this->_tpl_vars['esynAccountInfo']): ?>
		<div class="notification"><ul><li><?php echo $this->_tpl_vars['lang']['error_comment_logged']; ?>
</li></ul></div>
	<?php else: ?>
		<?php if (isset ( $this->_tpl_vars['msg'] )): ?>
			<?php if (! $this->_tpl_vars['error']): ?>
				<script type="text/javascript">
					sessvars.$.clearMem();
				</script>
			<?php endif; ?>
		<?php endif; ?>
		<form action="" method="post" id="comment">
			<?php if ($this->_tpl_vars['esynAccountInfo']): ?>
				<input type="hidden" name="author" value="<?php echo $this->_tpl_vars['esynAccountInfo']['username']; ?>
" />
				<input type="hidden" name="email" value="<?php echo $this->_tpl_vars['esynAccountInfo']['email']; ?>
" />
			<?php else: ?>
				<p class="field">
					<label><?php echo $this->_tpl_vars['lang']['comment_author']; ?>
:</label><br /><input type="text" class="text" name="author" size="25" value="<?php if (isset ( $_POST['author'] )): ?><?php echo ((is_array($_tmp=$_POST['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
				</p>
				<p class="field">
					<label><?php echo $this->_tpl_vars['lang']['author_email']; ?>
:</label><br /><input type="text" class="text" name="email" size="25" value="<?php if (isset ( $_POST['email'] )): ?><?php echo ((is_array($_tmp=$_POST['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
				</p>
			<?php endif; ?>	
			
			<?php if ($this->_tpl_vars['config']['listing_rating']): ?>
				<div id="comment-rating" style="border-top: 1px solid #CCCCCC; margin: 20px 0; padding: 10px 0 0;"></div>
			<?php endif; ?>
			
			<div style="clear: both;"></div>
			
			<p class="field">
				<textarea name="comment" class="ckeditor_textarea" style="margin-top: 5px; width: 99%;" rows="6" cols="40" id="comment_form"><?php if (isset ( $this->_tpl_vars['body'] ) && ! empty ( $this->_tpl_vars['body'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['body'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?></textarea><br />
				<input type="text" class="text" id="comment_counter" />&nbsp;<?php echo $this->_tpl_vars['lang']['characters_left']; ?>
<br />
			</p>
			
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "captcha.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			
			<div>
				<input type="hidden" name="listing_id" value="<?php echo $this->_tpl_vars['listing']['id']; ?>
" />
				<input type="submit" id="add" name="add_comment" value="<?php echo $this->_tpl_vars['lang']['leave_comment']; ?>
" class="button"/>
			</div>
		</form>
		<?php echo smarty_function_include_file(array('js' => "js/ckeditor/ckeditor, js/intelli/intelli.textcounter, js/jquery/plugins/jquery.validate, plugins/comments/js/frontend/cache"), $this);?>

	<?php endif; ?>
<?php endif; ?>