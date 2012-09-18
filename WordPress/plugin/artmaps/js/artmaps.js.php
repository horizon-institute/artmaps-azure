<?php
header("Content-type: application/javascript", true);
require_once("../../../../wp-config.php");
require_once(ABSPATH . "/wp-admin/includes/plugin.php");
if(!isset($ArtMapsCore) || !is_plugin_active_for_network("artmaps/artmaps.php"))
    die("The ArtMaps plugin is not active");

$Config = $ArtMapsCore->getNetworkConfiguration();
$Site = $ArtMapsCore->getCurrentSiteDetails();

$RoleMapping = array(
        "none" => 0,
        "subscriber" => 1,
        "contributor" => 2,
        "editor" => 3,
        "administrator" => 4,
);
$Roles = (object)$RoleMapping;

$UserRole = $Roles->none;
if(is_user_logged_in()) {
    global $current_user;
    get_currentuserinfo();
    $u = get_userdata($current_user->ID);
    foreach ($u->roles as $r)
        if($RoleMapping[strtolower($r)] > $UserRole)
            $UserRole = $RoleMapping[strtolower($r)];
}

function userHasRole($r) {
    global $UserRole;
    return $UserRole >= $r;
}

foreach(array("base", "util", "ui", "map") as $f)
    require_once("artmaps_$f.js.php");
?>

