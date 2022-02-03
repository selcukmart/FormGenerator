<?php
/* Smarty version 3.1.44, created on 2022-02-03 10:56:37
  from '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Bootstrapv4FormFrontend/TEMPLATE.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.44',
  'unifunc' => 'content_61fba6d54bb6e8_52693627',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fe92388e1a97cd5698a37ca509703c868e0f20bc' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/git-repositories/FormGenerator/SMARTY_TPL_FILES/Bootstrapv4FormFrontend/TEMPLATE.tpl',
      1 => 1643791138,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_61fba6d54bb6e8_52693627 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="form-group <?php echo $_smarty_tpl->tpl_vars['form_group_class']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['input_capsule_attributes']->value;?>
>
    <label style="max-height:200px; overflow:auto" class="control-label col-md-3" <?php echo $_smarty_tpl->tpl_vars['label_attributes']->value;?>
><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</label>
    <?php echo $_smarty_tpl->tpl_vars['label_desc']->value;?>

    <div class="col-md-9" style="max-height:600px; overflow:auto">
        <?php echo $_smarty_tpl->tpl_vars['input_above_desc']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['input']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['input_belove_desc']->value;?>

    </div>
</div><?php }
}
