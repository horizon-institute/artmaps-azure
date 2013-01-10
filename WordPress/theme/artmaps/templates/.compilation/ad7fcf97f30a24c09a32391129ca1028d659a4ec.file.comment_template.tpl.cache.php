<?php /* Smarty version Smarty-3.1.11, created on 2012-12-06 14:01:16
         compiled from "/var/www/artmaps/wp-content/themes/artmaps/templates/comment_template.tpl" */ ?>
<?php /*%%SmartyHeaderCode:189216792250bdf092c4b195-31745137%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ad7fcf97f30a24c09a32391129ca1028d659a4ec' => 
    array (
      0 => '/var/www/artmaps/wp-content/themes/artmaps/templates/comment_template.tpl',
      1 => 1354705080,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '189216792250bdf092c4b195-31745137',
  'function' => 
  array (
  ),
  'cache_lifetime' => 3600,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50bdf092d55293_34045569',
  'variables' => 
  array (
    'metadata' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50bdf092d55293_34045569')) {function content_50bdf092d55293_34045569($_smarty_tpl) {?><div id="artmaps-post-body">
    <div id="artmaps-data-section">
        <?php if (isset($_smarty_tpl->tpl_vars['metadata']->value->imageurl)){?>
        <img class="artmaps-data" src="<?php echo $_smarty_tpl->tpl_vars['metadata']->value->imageurl;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['metadata']->value->title;?>
" style="max-width: 200px; max-height: 200px;" />
        <br />
        <?php }?>
        <span class="artmaps-data">Artist: <?php echo $_smarty_tpl->tpl_vars['metadata']->value->artist;?>
 <?php echo $_smarty_tpl->tpl_vars['metadata']->value->artistdate;?>
</span>
        <br />
        <span class="artmaps-data">Title: <?php echo $_smarty_tpl->tpl_vars['metadata']->value->title;?>
</span>
        <br />
        <span class="artmaps-data">Date: <?php echo $_smarty_tpl->tpl_vars['metadata']->value->artworkdate;?>
</span>
        <br />
        <span class="artmaps-data">Reference: <?php echo $_smarty_tpl->tpl_vars['metadata']->value->reference;?>
</span>
        <br />
        <a class="artmaps-data" href="<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
">View the artwork on ArtMaps</a>
    </div>
    <div>Enter your comment here</div>
</div><?php }} ?>