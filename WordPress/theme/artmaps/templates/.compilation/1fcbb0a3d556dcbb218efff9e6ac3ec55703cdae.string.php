<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:19:16
         compiled from "1fcbb0a3d556dcbb218efff9e6ac3ec55703cdae" */ ?>
<?php /*%%SmartyHeaderCode:10108788650c8bc947615a5-37791556%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fcbb0a3d556dcbb218efff9e6ac3ec55703cdae' => 
    array (
      0 => '1fcbb0a3d556dcbb218efff9e6ac3ec55703cdae',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '10108788650c8bc947615a5-37791556',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bc9479ba21_95341481',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bc9479ba21_95341481')) {function content_50c8bc9479ba21_95341481($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
jQuery(window).bind("hashchange", function() {});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;<?php }} ?>