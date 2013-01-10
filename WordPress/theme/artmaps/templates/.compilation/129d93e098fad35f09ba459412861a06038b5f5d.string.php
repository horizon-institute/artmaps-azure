<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 17:23:11
         compiled from "129d93e098fad35f09ba459412861a06038b5f5d" */ ?>
<?php /*%%SmartyHeaderCode:164603776950c8bd7f590136-65895855%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '129d93e098fad35f09ba459412861a06038b5f5d' => 
    array (
      0 => '129d93e098fad35f09ba459412861a06038b5f5d',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '164603776950c8bd7f590136-65895855',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8bd7f5c7736_79697063',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8bd7f5c7736_79697063')) {function content_50c8bd7f5c7736_79697063($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
    console.log(con.find("a").querystring());
});
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;

<?php }} ?>