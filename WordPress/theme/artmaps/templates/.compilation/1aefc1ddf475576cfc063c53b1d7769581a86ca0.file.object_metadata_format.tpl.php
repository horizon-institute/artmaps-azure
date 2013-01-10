<?php /* Smarty version Smarty-3.1.11, created on 2012-12-05 11:19:16
         compiled from "/var/www/artmaps/wp-content/themes/artmaps/templates/object_metadata_format.tpl" */ ?>
<?php /*%%SmartyHeaderCode:153815825350bf2db499f664-13398876%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1aefc1ddf475576cfc063c53b1d7769581a86ca0' => 
    array (
      0 => '/var/www/artmaps/wp-content/themes/artmaps/templates/object_metadata_format.tpl',
      1 => 1354706006,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '153815825350bf2db499f664-13398876',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50bf2db49d9c37_79677511',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50bf2db49d9c37_79677511')) {function content_50bf2db49d9c37_79677511($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

if(metadata.imageurl) {
    var img = jQuery(document.createElement("img"))
            .attr("src", metadata.imageurl)
            .attr("alt", metadata.title);
    con.append(img);
}

var p = jQuery(document.createElement("p"));
p.html("Artist: " + metadata.artist + " " + metadata.artistdate
        + "<br />Title: " + metadata.title
        + "<br />Date: " + metadata.artworkdate
        + "<br /><a href=\"http://www.tate.org.uk/art/artworks/"
        + metadata.reference + "\" target=\"_blank\">View on Tate Online</a>");
con.append(p);

return con;<?php }} ?>