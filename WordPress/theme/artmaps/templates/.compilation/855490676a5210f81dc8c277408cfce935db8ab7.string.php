<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:34:03
         compiled from "855490676a5210f81dc8c277408cfce935db8ab7" */ ?>
<?php /*%%SmartyHeaderCode:183388822750c8c00bcf2737-19104042%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '855490676a5210f81dc8c277408cfce935db8ab7' => 
    array (
      0 => '855490676a5210f81dc8c277408cfce935db8ab7',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '183388822750c8c00bcf2737-19104042',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8c00bd35039_31348291',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8c00bd35039_31348291')) {function content_50c8c00bd35039_31348291($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
if(typeof metadata.imageurl != "undefined")
    h += "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID + "#maptype=" + map.getMapType()
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