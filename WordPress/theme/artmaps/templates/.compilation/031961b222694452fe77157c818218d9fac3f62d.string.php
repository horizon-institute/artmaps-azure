<?php /* Smarty version Smarty-3.1.11, created on 2012-12-13 15:12:33
         compiled from "031961b222694452fe77157c818218d9fac3f62d" */ ?>
<?php /*%%SmartyHeaderCode:171356801050c9f0615e9c89-32494670%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '031961b222694452fe77157c818218d9fac3f62d' => 
    array (
      0 => '031961b222694452fe77157c818218d9fac3f62d',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '171356801050c9f0615e9c89-32494670',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c9f06162aa70_97369080',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9f06162aa70_97369080')) {function content_50c9f06162aa70_97369080($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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
    places = places.find(".leaf").find("a").text().each(function(i, t) {
        console.log(t);
    });
    con.append(places);
}

return con;
<?php }} ?>