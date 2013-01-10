<?php /* Smarty version Smarty-3.1.11, created on 2012-12-03 13:26:37
         compiled from "dd238f96ab80635e7e41b9dce4545d99e9cf1926" */ ?>
<?php /*%%SmartyHeaderCode:102098715450bca88da7bbe4-11669531%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd238f96ab80635e7e41b9dce4545d99e9cf1926' => 
    array (
      0 => 'dd238f96ab80635e7e41b9dce4545d99e9cf1926',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '102098715450bca88da7bbe4-11669531',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50bca88dab4b65_18327949',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50bca88dab4b65_18327949')) {function content_50bca88dab4b65_18327949($_smarty_tpl) {?>var con = jQuery(document.createElement("div"))
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
return con;
<?php }} ?>