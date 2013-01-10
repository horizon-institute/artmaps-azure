<?php /* Smarty version Smarty-3.1.11, created on 2012-12-13 15:18:37
         compiled from "8532cdf42ea4f55a143a2041353daa727c8621f5" */ ?>
<?php /*%%SmartyHeaderCode:144961586650c9f1cd069e70-24664018%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8532cdf42ea4f55a143a2041353daa727c8621f5' => 
    array (
      0 => '8532cdf42ea4f55a143a2041353daa727c8621f5',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '144961586650c9f1cd069e70-24664018',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c9f1cd0aa737_39248769',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9f1cd0aa737_39248769')) {function content_50c9f1cd0aa737_39248769($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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
        + "<br />We think this artwork is associated with this location. What do you think?");
con.append(p);

if(metadata.places) {
    var places = jQuery(document.createElement("div"))
        .html(metadata.places);
    places.children().first().remove();
    var ul = jQuery(document.createElement("ul"));
    places.find(".leaf").find("a").each(function(i, a) {
        var li = jQuery(document.createElement("li"));
        li.text(jQuery(a).text());
        ul.append(li);
    });
    con.append(ul);
}

return con;
<?php }} ?>