<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 16:55:38
         compiled from "3bdda2107a68fff9d3fe6f69f212e63796ffcf18" */ ?>
<?php /*%%SmartyHeaderCode:97862343950c8b70a4e14b2-00515648%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3bdda2107a68fff9d3fe6f69f212e63796ffcf18' => 
    array (
      0 => '3bdda2107a68fff9d3fe6f69f212e63796ffcf18',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '97862343950c8b70a4e14b2-00515648',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8b70a517107_06289435',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8b70a517107_06289435')) {function content_50c8b70a517107_06289435($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>