<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:30:47
         compiled from "8abee9d5b57412e858820cf4e5c3bca129838863" */ ?>
<?php /*%%SmartyHeaderCode:193816408450c8bf472729d0-92461706%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8abee9d5b57412e858820cf4e5c3bca129838863' => 
    array (
      0 => '8abee9d5b57412e858820cf4e5c3bca129838863',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '193816408450c8bf472729d0-92461706',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bf472b2be9_38408351',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bf472b2be9_38408351')) {function content_50c8bf472b2be9_38408351($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
        console.log(a.attr("href"));
    });
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>