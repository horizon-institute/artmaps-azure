<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 16:36:34
         compiled from "e481a47ff0ce6138570ffbdb1cddda3a818a0d9f" */ ?>
<?php /*%%SmartyHeaderCode:107165732150c8b292a98334-47792720%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e481a47ff0ce6138570ffbdb1cddda3a818a0d9f' => 
    array (
      0 => 'e481a47ff0ce6138570ffbdb1cddda3a818a0d9f',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '107165732150c8b292a98334-47792720',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8b292b32014_04353064',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8b292b32014_04353064')) {function content_50c8b292b32014_04353064($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
        + object.ID
        + "\" target=\"_blank\">[View]</a>";
con.html(h);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>