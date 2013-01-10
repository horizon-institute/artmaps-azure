<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:29:22
         compiled from "692e37a2fc8dc8b2c35dec3a80e883734c7c29ff" */ ?>
<?php /*%%SmartyHeaderCode:18951191150c8bef28831d9-34206470%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '692e37a2fc8dc8b2c35dec3a80e883734c7c29ff' => 
    array (
      0 => '692e37a2fc8dc8b2c35dec3a80e883734c7c29ff',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '18951191150c8bef28831d9-34206470',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bef28e7f56_80236005',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bef28e7f56_80236005')) {function content_50c8bef28e7f56_80236005($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
    console.log(con.find("a").attr("href").fragment());
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>