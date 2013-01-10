<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:32:41
         compiled from "53f633f2837d176d4f59ba8574bd319548f7cacc" */ ?>
<?php /*%%SmartyHeaderCode:58078073750c8bfb9787ad5-85501456%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53f633f2837d176d4f59ba8574bd319548f7cacc' => 
    array (
      0 => '53f633f2837d176d4f59ba8574bd319548f7cacc',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '58078073750c8bfb9787ad5-85501456',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bfb97c7da5_03709440',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bfb97c7da5_03709440')) {function content_50c8bfb97c7da5_03709440($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
        var href = a.attr("href");
        console.log(jQuery.param.fragment(href));
    });
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>