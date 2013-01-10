<?php /* Smarty version Smarty-3.1.11, created on 2012-12-12 16:42:20
         compiled from "0e19f5f2fcbc1481cc2362a5ab28e8a72c9a806d" */ ?>
<?php /*%%SmartyHeaderCode:96104064250c8b3ec59a868-44273352%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0e19f5f2fcbc1481cc2362a5ab28e8a72c9a806d' => 
    array (
      0 => '0e19f5f2fcbc1481cc2362a5ab28e8a72c9a806d',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '96104064250c8b3ec59a868-44273352',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c8b3ec5d84a4_07647538',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c8b3ec5d84a4_07647538')) {function content_50c8b3ec5d84a4_07647538($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
        + object.ID
        + "\" target=\"_blank\">View Artwork</a>";
con.html(h);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>