<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 17:29:54
         compiled from "89f6c0e6ba3d36db1e4a682eca947a45aaa6eb94" */ ?>
<?php /*%%SmartyHeaderCode:206030072350c76d923816a4-87164011%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '89f6c0e6ba3d36db1e4a682eca947a45aaa6eb94' => 
    array (
      0 => '89f6c0e6ba3d36db1e4a682eca947a45aaa6eb94',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '206030072350c76d923816a4-87164011',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c76d923bcb10_24399208',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c76d923bcb10_24399208')) {function content_50c76d923bcb10_24399208($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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
                    };
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