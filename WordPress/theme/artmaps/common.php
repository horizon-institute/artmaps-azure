<?php
require_once("wp-config.php");
function error404($msg = "") {
    include(TEMPLATEPATH . '/404.php');
    die($msg);
}
if(!isset($ArtMapsCore))
    error404("The ArtMaps plugin is not loaded");
$PluginUrlPrefix = $ArtMapsCore->getPluginUrlPrefix();
?>