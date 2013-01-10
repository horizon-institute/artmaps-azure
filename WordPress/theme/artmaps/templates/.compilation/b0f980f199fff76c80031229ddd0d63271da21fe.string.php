<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 13:58:06
         compiled from "b0f980f199fff76c80031229ddd0d63271da21fe" */ ?>
<?php /*%%SmartyHeaderCode:212277506750c73bee697070-87755411%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b0f980f199fff76c80031229ddd0d63271da21fe' => 
    array (
      0 => 'b0f980f199fff76c80031229ddd0d63271da21fe',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '212277506750c73bee697070-87755411',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c73bee6d6c78_52077582',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c73bee6d6c78_52077582')) {function content_50c73bee6d6c78_52077582($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
if(typeof metadata.imageurl != "undefined")
    h += "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" style=\"padding:0px;\" target=\"_blank\">" 
        + "<img src=\"" + metadata.imageurl + "\" /></a><br />";
h +=
        "<b>" + metadata.title + "</b><br />"
        + "by <b>" + metadata.artist + "</b><br />"
        + "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" target=\"_blank\">[View]</a>";
con.html(h);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>