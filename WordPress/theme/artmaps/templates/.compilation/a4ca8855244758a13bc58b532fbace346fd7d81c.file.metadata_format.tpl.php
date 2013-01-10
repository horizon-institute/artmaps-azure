<?php /* Smarty version Smarty-3.1.11, created on 2012-12-06 16:24:35
         compiled from "/var/www/artmaps/wp-content/themes/artmaps/templates/metadata_format.tpl" */ ?>
<?php /*%%SmartyHeaderCode:196859065550b5f4555c7e09-37770361%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a4ca8855244758a13bc58b532fbace346fd7d81c' => 
    array (
      0 => '/var/www/artmaps/wp-content/themes/artmaps/templates/metadata_format.tpl',
      1 => 1354541191,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '196859065550b5f4555c7e09-37770361',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50b5f4555fcfa7_74810107',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50b5f4555fcfa7_74810107')) {function content_50b5f4555fcfa7_74810107($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
for(var name in metadata) {
    h += "<b>" + name + "</b>: " + metadata[name] + "<br />";
}
h +=
        "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" target=\"_blank\">[View]</a>";
con.html(h);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>