<?php /* Smarty version Smarty-3.1.11, created on 2012-11-28 12:17:33
         compiled from "ebbe06c3af585d62260cda81293efbb04645ef99" */ ?>
<?php /*%%SmartyHeaderCode:91684657650b600ddc8efa6-12587208%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ebbe06c3af585d62260cda81293efbb04645ef99' => 
    array (
      0 => 'ebbe06c3af585d62260cda81293efbb04645ef99',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '91684657650b600ddc8efa6-12587208',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50b600ddcbf302_70092524',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50b600ddcbf302_70092524')) {function content_50b600ddcbf302_70092524($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
if(typeof metadata.imageurl != "undefined")
    h += "<img src=\"" + metadata.imageurl + "\" /><br />";
h +=
        "<b>" + metadata.title + "</b><br />"
        + "by <b>" + metadata.artist + "</b><br />"
        + "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" target=\"_blank\">[View]</a>";
con.html(h);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
var confirmed = jQuery(document.createElement("span"))
        .text(location.Confirmations + " confirmations");
con.append(confirmed)
        .append(jQuery(document.createElement("br")));
return con;
<?php }} ?>