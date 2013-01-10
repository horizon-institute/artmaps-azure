<?php /* Smarty version Smarty-3.1.11, created on 2012-12-04 17:35:43
         compiled from "58d668eef99d3d39eb74e7f8ca912b742ac7a3b6" */ ?>
<?php /*%%SmartyHeaderCode:178562039850be346fa35672-09539021%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58d668eef99d3d39eb74e7f8ca912b742ac7a3b6' => 
    array (
      0 => '58d668eef99d3d39eb74e7f8ca912b742ac7a3b6',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '178562039850be346fa35672-09539021',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'metadata' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50be346fb77263_62267452',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50be346fb77263_62267452')) {function content_50be346fb77263_62267452($_smarty_tpl) {?><div id="artmaps-post-body">
    <div id="artmaps-data-section">
        <?php if (isset($_smarty_tpl->tpl_vars['metadata']->value->imageurl)){?>
        <img class="artmaps-data" src="<?php echo $_smarty_tpl->tpl_vars['metadata']->value->imageurl;?>
" 
                alt="<?php echo $_smarty_tpl->tpl_vars['metadata']->value->title;?>
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