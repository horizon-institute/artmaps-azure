<?php /* Smarty version Smarty-3.1.11, created on 2012-12-04 13:29:44
         compiled from "/var/www/artmaps/wp-content/plugins/artmaps/templates/user_profile_fields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:112182106250abc1c2270902-33240721%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fa3184843deac2df86cfe8c8ca71595096d6d2fd' => 
    array (
      0 => '/var/www/artmaps/wp-content/plugins/artmaps/templates/user_profile_fields.tpl',
      1 => 1354627742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '112182106250abc1c2270902-33240721',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50abc1c2301a70_07431518',
  'variables' => 
  array (
    'redirect' => 0,
    'blog' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50abc1c2301a70_07431518')) {function content_50abc1c2301a70_07431518($_smarty_tpl) {?><a name="artmaps"></a>
<input type="hidden" name="artmaps_redirect" value="<?php echo $_smarty_tpl->tpl_vars['redirect']->value;?>
" />
<h3>My External Blog For Publishing</h3>
<table class="form-table">
    <tr>
        <td>
            <span class="description">
                ArtMaps can use your personal WordPress blog for publishing
                if you would like to share your ArtMaps activities with
                your subscribers.  If you wish to activate this functionality
                please enter your WordPress blog details below.
            </span>
        </td>
    </tr>
    <tr>
        <td>
            <table class="form-table">
                <tr>
                    <th><label for="artmaps_use_personal_blog_url">Blog URL</label></th>
                    <td>
                        <input 
                                type="text" 
                                id="artmaps_use_personal_blog_url" 
                                name="artmaps_use_personal_blog_url" 
                                value="<?php echo $_smarty_tpl->tpl_vars['blog']->value->url;?>
" />
                        <span class="description">Your blog URL</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="artmaps_use_personal_blog_username">Blog Username</label></th>
                    <td>
                        <input 
                                type="text"
                                id="artmaps_use_personal_blog_username"
                                name="artmaps_use_personal_blog_username"
                                value="<?php echo $_smarty_tpl->tpl_vars['blog']->value->username;?>
" />
                        <span class="description">Your blog username</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="artmaps_use_personal_blog_password">Blog Password</label></th>
                    <td>
                        <input 
                                type="password"
                                id="artmaps_use_personal_blog_password"
                                name="artmaps_use_personal_blog_password"
                                value="" />
                        <span class="description">Your blog password</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table><?php }} ?>