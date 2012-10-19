<?php
function error404($msg = "") {
    include(TEMPLATEPATH . '/404.php');
    die($msg);
}

if(!isset($ArtMapsCore))
    error404("The ArtMaps plugin is not loaded");
$PluginUrlPrefix = $ArtMapsCore->getPluginUrlPrefix();

global $current_user;
get_currentuserinfo();

add_filter("show_admin_bar", "__return_false" );
remove_action('wp_head', '_admin_bar_bump_cb');
?>