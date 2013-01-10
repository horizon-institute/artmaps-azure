<?php /* Smarty version Smarty-3.1.11, created on 2013-01-08 15:05:51
         compiled from "feca8dc5b71e53699f4fdb7254a11f516881879f" */ ?>
<?php /*%%SmartyHeaderCode:143923302350ec35cfb679e8-05887922%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'feca8dc5b71e53699f4fdb7254a11f516881879f' => 
    array (
      0 => 'feca8dc5b71e53699f4fdb7254a11f516881879f',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '143923302350ec35cfb679e8-05887922',
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
  'unifunc' => 'content_50ec35cfd71e50_90121491',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ec35cfd71e50_90121491')) {function content_50ec35cfd71e50_90121491($_smarty_tpl) {?><div id="artmaps-post-body">
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
    <div id="artmaps-comment-text">Enter your comment here</div>
</div><?php }} ?>