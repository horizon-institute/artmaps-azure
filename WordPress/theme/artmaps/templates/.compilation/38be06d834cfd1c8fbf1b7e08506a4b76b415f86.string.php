<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:28:15
         compiled from "38be06d834cfd1c8fbf1b7e08506a4b76b415f86" */ ?>
<?php /*%%SmartyHeaderCode:166946839050c8beafcc7667-96974856%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '38be06d834cfd1c8fbf1b7e08506a4b76b415f86' => 
    array (
      0 => '38be06d834cfd1c8fbf1b7e08506a4b76b415f86',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '166946839050c8beafcc7667-96974856',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8beafd074a6_46198518',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8beafd074a6_46198518')) {function content_50c8beafd074a6_46198518($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
    console.log(con.find("a").fragment());
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>