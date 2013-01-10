<?php /* Smarty version Smarty-3.1.11, created on 2012-12-13 14:44:12
         compiled from "b3d762c4879d917c813fa804fe6a7e5d2dc6d421" */ ?>
<?php /*%%SmartyHeaderCode:123252389050c9e9bc97c227-63776695%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b3d762c4879d917c813fa804fe6a7e5d2dc6d421' => 
    array (
      0 => 'b3d762c4879d917c813fa804fe6a7e5d2dc6d421',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '123252389050c9e9bc97c227-63776695',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c9e9bc9bf5f9_31255323',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9e9bc9bf5f9_31255323')) {function content_50c9e9bc9bf5f9_31255323($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
h += "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID + "#maptype=" + map.getMapType()
        + "\" style=\"padding:0px;\" target=\"_blank\">" 
        + "<img src=\"" + 
        ((typeof metadata.imageurl != "undefined") ? metadata.imageurl : ArtMapsConfig.ThemeDirUrl + "/content/unavailable.jpg")
        + "\" /></a>";
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
        var href = a.attr("href");
        a.attr("href", 
            jQuery.param.fragment(href,
                    { "maptype": map.getMapType() }));
    });
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>