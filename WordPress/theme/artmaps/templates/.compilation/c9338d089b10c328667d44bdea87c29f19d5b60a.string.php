<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 14:20:38
         compiled from "c9338d089b10c328667d44bdea87c29f19d5b60a" */ ?>
<?php /*%%SmartyHeaderCode:174760949250c74136e3f816-11287509%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c9338d089b10c328667d44bdea87c29f19d5b60a' => 
    array (
      0 => 'c9338d089b10c328667d44bdea87c29f19d5b60a',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '174760949250c74136e3f816-11287509',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c74136ebbf58_66273212',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c74136ebbf58_66273212')) {function content_50c74136ebbf58_66273212($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

if(metadata.imageurl) {
    var img = jQuery(document.createElement("img"))
            .attr("src", metadata.imageurl)
            .attr("alt", metadata.title)
            .click(function(e) {
                    var t = jQuery(e.target).clone();
                    t.dialog({
                        "modal": true,
                        "draggable": false,
                        "height": jQuery(window).height() - 50,
                        "width": jQuery(window).width() - 50
                    });
                });
    con.append(img);
}

var p = jQuery(document.createElement("p"));
p.html("Artist: " + metadata.artist + " " + metadata.artistdate
        + "<br />Title: " + metadata.title
        + "<br />Date: " + metadata.artworkdate
        + "<br />" + metadata.description);
con.append(p);

return con;
<?php }} ?>