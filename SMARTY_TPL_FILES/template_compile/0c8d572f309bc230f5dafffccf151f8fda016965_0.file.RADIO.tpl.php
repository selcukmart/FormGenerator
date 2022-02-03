<?php
/* Smarty version 3.1.44, created on 2022-02-03 11:43:46
  from '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Generic/RADIO.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.44',
  'unifunc' => 'content_61fbb1e2c886a4_59866377',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0c8d572f309bc230f5dafffccf151f8fda016965' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Generic/RADIO.tpl',
      1 => 1643811494,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_61fbb1e2c886a4_59866377 (Smarty_Internal_Template $_smarty_tpl) {
if (!(isset($_smarty_tpl->tpl_vars['checked']->value))) {?>
    <?php $_smarty_tpl->_assignInScope('checked', '');
}
if (!(isset($_smarty_tpl->tpl_vars['data_dependency']->value))) {?>
    <?php $_smarty_tpl->_assignInScope('data_dependency', '');
}?>
<label for="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"> <input type="radio" value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['checked']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['data_dependency']->value;?>
><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</label><br><?php }
}
