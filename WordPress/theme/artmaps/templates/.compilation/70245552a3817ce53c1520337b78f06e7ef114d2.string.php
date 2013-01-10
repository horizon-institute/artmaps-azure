<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:21:12
         compiled from "70245552a3817ce53c1520337b78f06e7ef114d2" */ ?>
<?php /*%%SmartyHeaderCode:72349659050c8bd0820a737-20436716%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '70245552a3817ce53c1520337b78f06e7ef114d2' => 
    array (
      0 => '70245552a3817ce53c1520337b78f06e7ef114d2',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '72349659050c8bd0820a737-20436716',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bd08241bc4_17358397',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bd08241bc4_17358397')) {function content_50c8bd08241bc4_17358397($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
    con.find("a");
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;<?php }} ?>