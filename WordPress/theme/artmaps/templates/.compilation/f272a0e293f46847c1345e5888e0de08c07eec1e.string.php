<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 14:03:11
         compiled from "f272a0e293f46847c1345e5888e0de08c07eec1e" */ ?>
<?php /*%%SmartyHeaderCode:141574533950c73d1f7d67f3-57597109%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f272a0e293f46847c1345e5888e0de08c07eec1e' => 
    array (
      0 => 'f272a0e293f46847c1345e5888e0de08c07eec1e',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '141574533950c73d1f7d67f3-57597109',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c73d1f817541_80236838',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c73d1f817541_80236838')) {function content_50c73d1f817541_80236838($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

con.addClass("tate-artwork");

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
        + "<br />" + metadata.description);
con.append(p);

return con;
<?php }} ?>