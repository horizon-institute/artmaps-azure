<?php /* Smarty version Smarty-3.1.11, created on 2013-01-07 15:01:45
         compiled from "/var/www/artmaps/wp-content/plugins/artmaps/templates/import_admin_page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:96056601550eae05208f631-13087673%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1648d692ecd919ca3d84ca4d0390c2a1b4ddf99d' => 
    array (
      0 => '/var/www/artmaps/wp-content/plugins/artmaps/templates/import_admin_page.tpl',
      1 => 1357570902,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '96056601550eae05208f631-13087673',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50eae0521a6e18_38829556',
  'variables' => 
  array (
    'imported' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50eae0521a6e18_38829556')) {function content_50eae0521a6e18_38829556($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['imported']->value){?>
<div class="updated">
    <p>
        <strong>The file was successfully imported</strong>
    </p>
</div>
<?php }?>
<div class="wrap">
    <form method="post" action="" enctype="multipart/form-data">
        <h2>ArtMaps Import</h2>
        <input type="file" name="artmaps_import_file" />
        <div class="submit">
        <input
                class="button-primary"
                type="submit"
                name="artmaps_import_update"
                value="Import" />
        </div>
    </form>
</div><?php }} ?>