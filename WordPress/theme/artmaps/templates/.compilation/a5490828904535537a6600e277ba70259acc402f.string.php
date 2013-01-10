<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 14:08:18
         compiled from "a5490828904535537a6600e277ba70259acc402f" */ ?>
<?php /*%%SmartyHeaderCode:56170615650c73e5234bd91-58266199%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a5490828904535537a6600e277ba70259acc402f' => 
    array (
      0 => 'a5490828904535537a6600e277ba70259acc402f',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '56170615650c73e5234bd91-58266199',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c73e5237c887_72063773',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c73e5237c887_72063773')) {function content_50c73e5237c887_72063773($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

if(metadata.imageurl) {
    var img = jQuery(document.createElement("img"))
            .attr("src", metadata.imageurl)
            .attr("alt", metadata.title)
            .addClass("tate-artwork");
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