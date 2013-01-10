<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 13:55:12
         compiled from "fc6a3468b41c9850a14a165cf4e880e16ed84c43" */ ?>
<?php /*%%SmartyHeaderCode:96875421650c73b40955256-27496562%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fc6a3468b41c9850a14a165cf4e880e16ed84c43' => 
    array (
      0 => 'fc6a3468b41c9850a14a165cf4e880e16ed84c43',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '96875421650c73b40955256-27496562',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c73b409854f6_00016726',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c73b409854f6_00016726')) {function content_50c73b409854f6_00016726($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
if(typeof metadata.imageurl != "undefined")
    h += "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" target=\"_blank\">" 
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