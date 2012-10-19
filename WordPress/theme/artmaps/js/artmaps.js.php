<?php
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
$CoreUserID = -1;
if(is_user_logged_in()) {
    global $current_user;
    get_currentuserinfo();
    $u = get_userdata($current_user->ID);
    foreach ($u->roles as $r)
        if($RoleMapping[strtolower($r)] > $UserRole)
            $UserRole = $RoleMapping[strtolower($r)];

    $un = $current_user->get("user_login");
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, "http://artmapscore.cloudapp.net/service/tate/rest/v1/users/search?URI=tate://$un");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $d = curl_exec($c);
    if($d != "-1") {
        $d = json_decode($d);
        $CoreUserID = $d->ID;
    }
    curl_close($c);
    unset($c);
}

function userHasRole($r) {
    global $UserRole;
    return $UserRole >= $r;
}

foreach(array("base", "util", "ui", "map") as $f)
    require_once("artmaps_$f.js.php");
?>

