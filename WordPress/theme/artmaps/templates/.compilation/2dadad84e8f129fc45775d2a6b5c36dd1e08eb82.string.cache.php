<?php /* Smarty version Smarty-3.1.11, created on 2012-12-04 14:09:41
         compiled from "2dadad84e8f129fc45775d2a6b5c36dd1e08eb82" */ ?>
<?php /*%%SmartyHeaderCode:130374589950be0425cf4184-02100186%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2dadad84e8f129fc45775d2a6b5c36dd1e08eb82' => 
    array (
      0 => '2dadad84e8f129fc45775d2a6b5c36dd1e08eb82',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '130374589950be0425cf4184-02100186',
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
  'unifunc' => 'content_50be0425d82600_10559527',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50be0425d82600_10559527')) {function content_50be0425d82600_10559527($_smarty_tpl) {?><div id="artmaps-post-body">
    <div id="artmaps-data-section">
        { if isset($metadata->imageurl) }
        <img class="artmaps-data" src="<?php echo $_smarty_tpl->tpl_vars['metadata']->value->imageurl;?>
" 
                alt="<?php echo $_smarty_tpl->tpl_vars['metadata']->value->title;?>
" style="max-width: 200px; max-height: 200px;" />
        <br />
        { /if }
        <span class="artmaps-data">Artist: <?php echo $_smarty_tpl->tpl_vars['metadata']->value->artist;?>
 $metadata->artistdate</span>
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
    <div></div>
</div>
<?php }} ?>