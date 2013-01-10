<?php /* Smarty version Smarty-3.1.11, created on 2012-12-05 10:35:50
         compiled from "/var/www/artmaps/wp-content/themes/artmaps/templates/metadata_title_template.tpl" */ ?>
<?php /*%%SmartyHeaderCode:41015036350bf23864e1446-35372118%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f08397fbf4bf479945558d997dabf3b29566ce42' => 
    array (
      0 => '/var/www/artmaps/wp-content/themes/artmaps/templates/metadata_title_template.tpl',
      1 => 1354703736,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41015036350bf23864e1446-35372118',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'metadata' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50bf2386588e09_65837754',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50bf2386588e09_65837754')) {function content_50bf2386588e09_65837754($_smarty_tpl) {?><?php echo $_smarty_tpl->tpl_vars['metadata']->value->title;?>
 by <?php echo $_smarty_tpl->tpl_vars['metadata']->value->artist;?>
<?php }} ?>