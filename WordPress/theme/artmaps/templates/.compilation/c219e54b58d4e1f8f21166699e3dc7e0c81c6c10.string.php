<?php /* Smarty version Smarty-3.1.11, created on 2012-12-13 15:15:12
         compiled from "c219e54b58d4e1f8f21166699e3dc7e0c81c6c10" */ ?>
<?php /*%%SmartyHeaderCode:123758536250c9f1009c1195-78845511%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c219e54b58d4e1f8f21166699e3dc7e0c81c6c10' => 
    array (
      0 => 'c219e54b58d4e1f8f21166699e3dc7e0c81c6c10',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '123758536250c9f1009c1195-78845511',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c9f100a13de7_35404405',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9f100a13de7_35404405')) {function content_50c9f100a13de7_35404405($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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
        + "<br />Date: " + metadata.artworkdate);
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