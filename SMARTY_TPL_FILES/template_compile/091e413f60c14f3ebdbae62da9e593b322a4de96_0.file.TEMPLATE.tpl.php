<?php
/* Smarty version 3.1.44, created on 2022-02-03 10:56:34
  from '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Bootstrapv3FormWizard/TEMPLATE.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.44',
  'unifunc' => 'content_61fba6d2b26174_81604335',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '091e413f60c14f3ebdbae62da9e593b322a4de96' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Bootstrapv3FormWizard/TEMPLATE.tpl',
      1 => 1643807443,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_61fba6d2b26174_81604335 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="form-group <?php echo $_smarty_tpl->tpl_vars['form_group_class']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['input_capsule_attributes']->value;?>
>
    <label class="control-label" <?php echo $_smarty_tpl->tpl_vars['label_attributes']->value;?>
><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</label><br>
    <?php echo $_smarty_tpl->tpl_vars['label_desc']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['input_above_desc']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['input']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['input_belove_desc']->value;?>


</div><?php }
}
