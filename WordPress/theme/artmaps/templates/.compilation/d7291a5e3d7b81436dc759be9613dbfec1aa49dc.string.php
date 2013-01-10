<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:21:58
         compiled from "d7291a5e3d7b81436dc759be9613dbfec1aa49dc" */ ?>
<?php /*%%SmartyHeaderCode:36473684050c8bd3649f370-06226086%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd7291a5e3d7b81436dc759be9613dbfec1aa49dc' => 
    array (
      0 => 'd7291a5e3d7b81436dc759be9613dbfec1aa49dc',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '36473684050c8bd3649f370-06226086',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bd364de0a8_64552459',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bd364de0a8_64552459')) {function content_50c8bd364de0a8_64552459($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
    console.log(con.find("a"));
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;<?php }} ?>