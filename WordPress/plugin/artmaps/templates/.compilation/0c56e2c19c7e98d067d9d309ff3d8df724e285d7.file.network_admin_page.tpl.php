<?php /* Smarty version Smarty-3.1.11, created on 2012-11-27 16:48:41
         compiled from "/var/www/artmaps/wp-content/plugins/artmaps/templates/network_admin_page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:153451468650ab5e683a7122-97517600%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0c56e2c19c7e98d067d9d309ff3d8df724e285d7' => 
    array (
      0 => '/var/www/artmaps/wp-content/plugins/artmaps/templates/network_admin_page.tpl',
      1 => 1354034449,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '153451468650ab5e683a7122-97517600',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50ab5e683e1dd7_34555020',
  'variables' => 
  array (
    'updated' => 0,
    'coreServerUrl' => 0,
    'masterKeyIsSet' => 0,
    'mapKey' => 0,
    'ipInfoDbKey' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ab5e683e1dd7_34555020')) {function content_50ab5e683e1dd7_34555020($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['updated']->value){?>
<div class="updated">
    <p>
        <strong>Settings Updated</strong>
    </p>
</div>
<?php }?>
<div class="wrap">
    <form method="post" action="">
        <h2>ArtMaps Settings</h2>
        <h3>Core Server URL</h3>
        <p>
            <label for="artmaps_core_server_url">
                <input
                        style="width: 40%"
                        type="text"
                        id="artmaps_core_server_url"
                        name="artmaps_core_server_url"
                        value="<?php echo $_smarty_tpl->tpl_vars['coreServerUrl']->value;?>
" />
            </label>
        </p>
        <h3>Master Key</h3>
        <?php if ($_smarty_tpl->tpl_vars['masterKeyIsSet']->value){?>
        <h4>For security, the saved key is not displayed</h4>
        <?php }else{ ?>
        <h4>No key is currently assigned</h4>
        <?php }?>
        <textarea 
                name="artmaps_master_key" 
                style="width: 80%; height: 100px;"></textarea>
        <h3>Google Maps API Key</h3>
        <p>
            <label for="artmaps_google_maps_api_key">
                <input
                        style="width: 40%"
                        type="text"
                        id="artmaps_google_maps_api_key"
                        name="artmaps_google_maps_api_key"
                        value="<?php echo $_smarty_tpl->tpl_vars['mapKey']->value;?>
" />
            </label>
        </p>
        <h3>IP InfoDB API Key</h3>
        <p>
            <label for="artmaps_ipinfodb_api_key">
                <input
                        style="width: 40%"
                        type="text"
                        id="artmaps_ipinfodb_api_key"
                        name="artmaps_ipinfodb_api_key"
                        value="<?php echo $_smarty_tpl->tpl_vars['ipInfoDbKey']->value;?>
" />
            </label>
        </p>
        <div class="submit">
        <input
                class="button-primary"
                type="submit"
                name="artmaps_network_config_update"
                value="Save Changes" />
        </div>
    </form>
</div><?php }} ?>