<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:31:37
         compiled from "54f188460b753fae8177ad8c96fa69af5867af31" */ ?>
<?php /*%%SmartyHeaderCode:93980519550c8bf79ca6375-24197853%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '54f188460b753fae8177ad8c96fa69af5867af31' => 
    array (
      0 => '54f188460b753fae8177ad8c96fa69af5867af31',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '93980519550c8bf79ca6375-24197853',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bf79ce6be2_37390277',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bf79ce6be2_37390277')) {function content_50c8bf79ce6be2_37390277($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
    con.find("a").each(function(i, a) {
        var a = jQuery(a);
        console.log(a.attr("href"));
    });
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>