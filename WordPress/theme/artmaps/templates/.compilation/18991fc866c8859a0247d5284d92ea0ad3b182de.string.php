<?php /* Smarty version Smarty-3.1.11, created on 2012-12-13 15:13:27
         compiled from "18991fc866c8859a0247d5284d92ea0ad3b182de" */ ?>
<?php /*%%SmartyHeaderCode:59365330350c9f097dee282-10596038%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '18991fc866c8859a0247d5284d92ea0ad3b182de' => 
    array (
      0 => '18991fc866c8859a0247d5284d92ea0ad3b182de',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '59365330350c9f097dee282-10596038',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c9f097e2ec47_12501386',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9f097e2ec47_12501386')) {function content_50c9f097e2ec47_12501386($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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
    places = places.find(".leaf").find("a").each(function(i, a) {
        console.log(jQuery(a).text());
    });
    con.append(places);
}

return con;
<?php }} ?>