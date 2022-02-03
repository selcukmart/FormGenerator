<?php
/* Smarty version 3.1.44, created on 2022-02-03 11:43:46
  from '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Generic/TEXTAREA.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.44',
  'unifunc' => 'content_61fbb1e2c73462_60645290',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aa743168336ac1f46b914af1ec0c66245b81f80c' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Generic/TEXTAREA.tpl',
      1 => 1643811251,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_61fbb1e2c73462_60645290 (Smarty_Internal_Template $_smarty_tpl) {
?><textarea placeholder="<?php echo $_smarty_tpl->tpl_vars['placeholder']->value;?>
" rows="<?php echo $_smarty_tpl->tpl_vars['row']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" type="<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
" class="form-control"><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
</textarea><?php }
}
