<?php
/* Smarty version 3.1.44, created on 2022-02-03 09:56:12
  from '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Bootstrapv3Form/RADIO.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.44',
  'unifunc' => 'content_61fb98acec8375_15955321',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9361038e3ff520e2ad488d037a3357abe7d2cb5e' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Bootstrapv3Form/RADIO.tpl',
      1 => 1643811494,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_61fb98acec8375_15955321 (Smarty_Internal_Template $_smarty_tpl) {
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
