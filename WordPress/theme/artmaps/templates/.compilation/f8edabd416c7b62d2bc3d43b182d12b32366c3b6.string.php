<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:26:19
         compiled from "f8edabd416c7b62d2bc3d43b182d12b32366c3b6" */ ?>
<?php /*%%SmartyHeaderCode:33539401550c8be3bb623a6-10508992%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f8edabd416c7b62d2bc3d43b182d12b32366c3b6' => 
    array (
      0 => 'f8edabd416c7b62d2bc3d43b182d12b32366c3b6',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '33539401550c8be3bb623a6-10508992',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8be3bb99e04_98820898',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8be3bb99e04_98820898')) {function content_50c8be3bb99e04_98820898($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
if(typeof metadata.imageurl != "undefined")
    h += "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" style=\"padding:0px;\" target=\"_blank\">" 
        + "<img src=\"" + metadata.imageurl + "\" /></a>";
h +=
        "<b>" + metadata.title + "</b><br />"
        + "by <b>" + metadata.artist + "</b><br />"
        + "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID + "#maptype=" + map.getMapType()
        + "\" target=\"_blank\">View Artwork</a>";
con.html(h);
jQuery(window).bind("hashchange", function() {
    con.find("a").querystring();
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>