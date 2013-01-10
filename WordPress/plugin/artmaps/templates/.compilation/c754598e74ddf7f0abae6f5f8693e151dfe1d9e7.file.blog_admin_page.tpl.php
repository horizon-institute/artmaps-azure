<?php /* Smarty version Smarty-3.1.11, created on 2012-12-05 11:22:25
         compiled from "/var/www/artmaps/wp-content/plugins/artmaps/templates/blog_admin_page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:66863262550b5f8096d9848-54847161%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c754598e74ddf7f0abae6f5f8693e151dfe1d9e7' => 
    array (
      0 => '/var/www/artmaps/wp-content/plugins/artmaps/templates/blog_admin_page.tpl',
      1 => 1354706405,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '66863262550b5f8096d9848-54847161',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50b5f80971fae1_65094960',
  'variables' => 
  array (
    'updated' => 0,
    'searchSource' => 0,
    'commentTemplate' => 0,
    'metadataTitleTemplate' => 0,
    'metadataTitleJS' => 0,
    'metadataFormatJS' => 0,
    'searchResultTitleJS' => 0,
    'objectMetadataFormatJS' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50b5f80971fae1_65094960')) {function content_50b5f80971fae1_65094960($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['updated']->value){?>
<div class="updated">
    <p>
        <strong>Settings Updated</strong>
    </p>
</div>
<?php }?>
<div class="wrap">
    <form method="post" action="">
        <h2>ArtMaps Settings</h2>
        <h3>Object Search Source<h3>
        <select name="artmaps_blog_config_search_source">
            <option value="artmaps"<?php if ($_smarty_tpl->tpl_vars['searchSource']->value=='artmaps'){?> selected="selected"<?php }?>>ArtMaps</option>
            <option value="tateartwork"<?php if ($_smarty_tpl->tpl_vars['searchSource']->value=='tateartwork'){?> selected="selected"<?php }?>>Tate Collection</option>
        </select>
        <h3>Comment Template</h3>
        <textarea 
                name="artmaps_blog_option_comment_template" 
                style="width: 80%; height: 100px;"><?php echo $_smarty_tpl->tpl_vars['commentTemplate']->value;?>
</textarea>
        <h3>Metadata Title Template</h3>
        <textarea 
                name="artmaps_blog_option_metadata_title_template" 
                style="width: 80%; height: 100px;"><?php echo $_smarty_tpl->tpl_vars['metadataTitleTemplate']->value;?>
</textarea>
        <h3>Metadata Title Javascript</h3>
        <textarea 
                name="artmaps_blog_option_metadata_title_js" 
                style="width: 80%; height: 100px;"><?php echo $_smarty_tpl->tpl_vars['metadataTitleJS']->value;?>
</textarea>
        <h3>Metadata Format Javascript</h3>
        <textarea 
                name="artmaps_blog_option_metadata_format_js" 
                style="width: 80%; height: 100px;"><?php echo $_smarty_tpl->tpl_vars['metadataFormatJS']->value;?>
</textarea>
        <h3>Search Result Title Javascript</h3>
        <textarea 
                name="artmaps_blog_option_search_result_title_js" 
                style="width: 80%; height: 100px;"><?php echo $_smarty_tpl->tpl_vars['searchResultTitleJS']->value;?>
</textarea>
        <h3>Object Metadata Format Javascript</h3>
        <textarea 
                name="artmaps_blog_option_object_metadata_format_js" 
                style="width: 80%; height: 100px;"><?php echo $_smarty_tpl->tpl_vars['objectMetadataFormatJS']->value;?>
</textarea>
        <div class="submit">
        <input
                class="button-primary"
                type="submit"
                name="artmaps_blog_config_update"
                value="Save Changes" />
        </div>
    </form>
</div><?php }} ?>