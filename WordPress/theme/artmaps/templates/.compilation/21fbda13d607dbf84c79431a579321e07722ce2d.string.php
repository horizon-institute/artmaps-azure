<?php /* Smarty version Smarty-3.1.11, created on 2012-12-11 13:19:58
         compiled from "21fbda13d607dbf84c79431a579321e07722ce2d" */ ?>
<?php /*%%SmartyHeaderCode:185155398550c732fee65556-02021049%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '21fbda13d607dbf84c79431a579321e07722ce2d' => 
    array (
      0 => '21fbda13d607dbf84c79431a579321e07722ce2d',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '185155398550c732fee65556-02021049',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50c732feef22b1_58659611',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c732feef22b1_58659611')) {function content_50c732feef22b1_58659611($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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

return con;<?php }} ?>