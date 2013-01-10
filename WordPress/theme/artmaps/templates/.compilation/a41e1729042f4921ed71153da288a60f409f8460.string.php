<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:18:39
         compiled from "a41e1729042f4921ed71153da288a60f409f8460" */ ?>
<?php /*%%SmartyHeaderCode:170502337750c8bc6fa90f61-30666290%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a41e1729042f4921ed71153da288a60f409f8460' => 
    array (
      0 => 'a41e1729042f4921ed71153da288a60f409f8460',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '170502337750c8bc6fa90f61-30666290',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bc6fac51b3_02174691',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bc6fac51b3_02174691')) {function content_50c8bc6fac51b3_02174691($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
jQuery(window);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;<?php }} ?>