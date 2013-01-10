<?php /* Smarty version Smarty-3.1.11, created on 2012-12-14 14:38:15
         compiled from "97de2e24c0d2747289235487272da733c0dc6e74" */ ?>
<?php /*%%SmartyHeaderCode:155238970250cb39d7c053a3-21447808%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '97de2e24c0d2747289235487272da733c0dc6e74' => 
    array (
      0 => '97de2e24c0d2747289235487272da733c0dc6e74',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '155238970250cb39d7c053a3-21447808',
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
  'unifunc' => 'content_50cb39d8577644_80531683',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50cb39d8577644_80531683')) {function content_50cb39d8577644_80531683($_smarty_tpl) {?><div id="artmaps-post-body">
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
    <div id="comment-text">Enter your comment here</div>
</div><?php }} ?>