<?php /* Smarty version Smarty-3.1.11, created on 2012-12-03 10:59:30
         compiled from "8706a494e19bfe6845e50732b62a47f0894d57b0" */ ?>
<?php /*%%SmartyHeaderCode:9675277850bc8612b6dc71-71940192%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8706a494e19bfe6845e50732b62a47f0894d57b0' => 
    array (
      0 => '8706a494e19bfe6845e50732b62a47f0894d57b0',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '9675277850bc8612b6dc71-71940192',
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
  'unifunc' => 'content_50bc8612c795b2_13552651',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50bc8612c795b2_13552651')) {function content_50bc8612c795b2_13552651($_smarty_tpl) {?><div id="artmaps-post-body">
    <div id="artmaps-data-section">
        { if isset($metadata.imageurl) }
        <img class="artmaps-data" src="<?php echo $_smarty_tpl->tpl_vars['metadata']->value['imageurl'];?>
" 
                alt="<?php echo $_smarty_tpl->tpl_vars['metadata']->value['title'];?>
" style="max-width: 200px; max-height: 200px;" />
        <br />
        { /if }
        <span class="artmaps-data">Artist: <?php echo $_smarty_tpl->tpl_vars['metadata']->value['artist'];?>
 $metadata->artistdate</span>
        <br />
        <span class="artmaps-data">Title: <?php echo $_smarty_tpl->tpl_vars['metadata']->value['title'];?>
</span>
        <br />
        <span class="artmaps-data">Date: <?php echo $_smarty_tpl->tpl_vars['metadata']->value['artworkdate'];?>
</span>
        <br />
        <span class="artmaps-data">Reference: <?php echo $_smarty_tpl->tpl_vars['metadata']->value['reference'];?>
</span>
        <br />
        <a class="artmaps-data" href="<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
">View the artwork on ArtMaps</a>
    </div>
    <div></div>
</div><?php }} ?>