<?php /* Smarty version Smarty-3.1.11, created on 2012-12-13 14:47:54
         compiled from "1fdc25d8d08a5d4f4bced6ab4632f9e4fba26036" */ ?>
<?php /*%%SmartyHeaderCode:5348847150c9ea9ae8ca60-49960975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fdc25d8d08a5d4f4bced6ab4632f9e4fba26036' => 
    array (
      0 => '1fdc25d8d08a5d4f4bced6ab4632f9e4fba26036',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '5348847150c9ea9ae8ca60-49960975',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c9ea9aeccfd2_39960548',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9ea9aeccfd2_39960548')) {function content_50c9ea9aeccfd2_39960548($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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
                        "dialogClass": "artmaps-large-image-popup",
                        "modal": true,
                        "draggable": false,
                        "height": jQuery(window).height() - 50,
                        "width": jQuery(window).width() - 50,
                        "open": function() {
                            jQuery(window).resize(resizeHandler);
                        },
                        "close": function() {
                            jQuery(window).unbind("resize", resizeHandler);
                        }
                    });
                });
    con.append(img);
} else {
    var img = jQuery(document.createElement("img"))
            .attr("src", ArtMapsConfig.ThemeDirUrl + "/content/unavailable.jpg")
            .attr("alt", metadata.title);
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