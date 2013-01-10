<?php /* Smarty version Smarty-3.1.11, created on 2012-12-05 11:22:57
         compiled from "3e70f647b481c43ddc0ebee91055fb73471b0d53" */ ?>
<?php /*%%SmartyHeaderCode:83628064450bf2e91574b89-05980596%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e70f647b481c43ddc0ebee91055fb73471b0d53' => 
    array (
      0 => '3e70f647b481c43ddc0ebee91055fb73471b0d53',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '83628064450bf2e91574b89-05980596',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50bf2e915ac856_34373711',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50bf2e915ac856_34373711')) {function content_50bf2e915ac856_34373711($_smarty_tpl) {?>var con = jQuery(document.createElement("div"));

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