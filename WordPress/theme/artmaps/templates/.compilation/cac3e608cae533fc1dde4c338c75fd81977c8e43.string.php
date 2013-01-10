<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:15:38
         compiled from "cac3e608cae533fc1dde4c338c75fd81977c8e43" */ ?>
<?php /*%%SmartyHeaderCode:88366939950c8bbba2a2cb2-06723251%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cac3e608cae533fc1dde4c338c75fd81977c8e43' => 
    array (
      0 => 'cac3e608cae533fc1dde4c338c75fd81977c8e43',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '88366939950c8bbba2a2cb2-06723251',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bbba2d68a5_29607610',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bbba2d68a5_29607610')) {function content_50c8bbba2d68a5_29607610($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
return con;<?php }} ?>