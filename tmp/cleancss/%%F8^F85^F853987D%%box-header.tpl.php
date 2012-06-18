<?php /* Smarty version 2.6.26, created on 2011-12-13 04:25:57
         compiled from /home/vbezruchkin/www/v1700/templates/common/box-header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'print_img', '/home/vbezruchkin/www/v1700/templates/common/box-header.tpl', 4, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/box-header.tpl', 5, false),)), $this); ?>
<div class="box" <?php if (isset ( $this->_tpl_vars['id'] )): ?>id="block_<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>>
	<div class="box-caption-<?php echo $this->_tpl_vars['style']; ?>
" <?php if (isset ( $this->_tpl_vars['id'] )): ?>id="caption_<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>>
		<?php if (isset ( $this->_tpl_vars['rss'] ) && ! empty ( $this->_tpl_vars['rss'] )): ?>
			<a href="<?php echo $this->_tpl_vars['rss']; ?>
"><?php echo smarty_function_print_img(array('fl' => "xml.gif",'full' => true,'style' => "vertical-align: middle;"), $this);?>
</a>
		<?php endif; ?> <?php echo $this->_tpl_vars['caption']; ?>
 <?php echo smarty_function_esynHooker(array('name' => 'blockHeader'), $this);?>

	</div>
	<div class="box-content-<?php echo $this->_tpl_vars['style']; ?>
<?php if (isset ( $this->_tpl_vars['collapsible'] ) && $this->_tpl_vars['collapsible'] == '1'): ?> collapsible<?php endif; ?> <?php if (isset ( $this->_tpl_vars['collapsed'] ) && $this->_tpl_vars['collapsed'] == '1'): ?>collapsed<?php endif; ?>" <?php if (isset ( $this->_tpl_vars['id'] )): ?>id="content_<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>>