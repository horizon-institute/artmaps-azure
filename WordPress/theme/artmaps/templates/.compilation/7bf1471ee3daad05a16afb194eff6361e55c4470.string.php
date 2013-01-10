<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 17:29:03
         compiled from "7bf1471ee3daad05a16afb194eff6361e55c4470" */ ?>
<?php /*%%SmartyHeaderCode:205640787850c76d5f9c35c4-37408026%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7bf1471ee3daad05a16afb194eff6361e55c4470' => 
    array (
      0 => '7bf1471ee3daad05a16afb194eff6361e55c4470',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '205640787850c76d5f9c35c4-37408026',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c76d5f9faf07_85380337',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c76d5f9faf07_85380337')) {function content_50c76d5f9faf07_85380337($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

if(metadata.imageurl) {
    var img = jQuery(document.createElement("img"))
            .attr("src", metadata.imageurl)
            .attr("alt", metadata.title)
            .click(function(e) {
                    var t = jQuery(e.target).clone();
                    var resizeHandler = function() {
                        t.dialog({
                        "height": jQuery(window).height() - 50,
                        "width": jQuery(window).width() - 50
                    });
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