<?php
require_once("../../../../wp-config.php");
/* Defaults */
$lat = 51.5171;
$lon = 0.1062;
$zoom = 12;
if(isset($_GET["c"])) {
    try {
        $conf = json_decode(preg_replace("/([^\\\\])\\\\/", "\\1", $_GET["c"]));
        $lat = $conf->map->center->latitude / pow(10, 8);
        $lon = $conf->map->center->longitude / pow(10, 8);
        $zoom = $conf->map->center->zoom;
    } catch(Exception $e) { }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ArtMaps | Tate Galleries</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="ArtMaps is a crowd-sourcing project for geographic information about artworks held by the Tate galleries." />
<link type="text/css" rel=StyleSheet href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/black-tie/jquery-ui.css" />
<link type="text/css" rel=StyleSheet href="../css/artmaps.css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBDotOtQIdRgtPB6GJnMwRfUEAoluvrdqk&sensor=true&libraries=places"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/jquery.xcolor.min.js"></script>
<script type="text/javascript" src="../js/json2.js"></script>
<script type="text/javascript" src="../js/markerclusterer.js"></script>
<script type="text/javascript" src="../js/styledmarker.js"></script>
<script type="text/javascript" src="../js/artmaps.js.php"></script>
</head>
<body>
<noscript>
<img src="http://tate.artmaps.wp.horizon.ac.uk/wp-content/plugins/artmaps/content/logo.jpg" alt="ArtMaps" />
</noscript>
<script type="text/javascript">
var ArtMaps_Frame_URL = "<?= urlencode(wp_get_referer()) ?>";
var config = {
    "mapConf": {
        "center": new google.maps.LatLng(<?= $lat ?>, <?= $lon ?>),
        "streetViewControl": false,
        "zoom": <?= $zoom ?>,
        "mapTypeId": google.maps.MapTypeId.HYBRID,
	"mapTypeControlOptions": {
            "mapTypeIds": [
                google.maps.MapTypeId.HYBRID,
                google.maps.MapTypeId.ROADMAP,
                google.maps.MapTypeId.SATELLITE
            ]
        }
    },
    "clustererConf" : {
        "minimumClusterSize": 2
    }
};
$(function() {
    new ArtMaps.Map.MapObject($("#ArtMaps_Map_MapContainer").get(0), config);

});
</script>
<div id="ArtMaps_Map_MapContainer"></div>
<div id="ArtMaps_Share_Container" style="display:none;">
<div id="ArtMaps_Share_Container_Accordion">
    <h3><a href="#">Share</a></h3>
    <div>
        <a id="wordpress" class="ArtMaps_Social_Button" title="Blog on WordPress">
            <img src="../content/social/32/wordpress_32.png" alt="Blog on WordPress" title="Blog on WordPress" /></a>
        <!--<a id="blogger" class="ArtMaps_Social_Button" title="Blog on Blogger.com">
            <img src="../content/social/32/blogger_32.png" alt="Blog on Blogger.com" title="Blog on Blogger.com" /></a>-->
        <a id="facebook" class="ArtMaps_Social_Button" title="Share to Facebook">
            <img src="../content/social/32/facebook_32.png" alt="Share to Facebook" title="Share to Facebook" /></a>
        <a id="twitter" class="ArtMaps_Social_Button" title="Tweet this">
            <img src="../content/social/32/twitter_32.png" alt="Tweet this" title="Tweet this" /></a>
    </div>
    <h3><a href="#">Link</a></h3>
    <div>
        Copy the url below to link directly to the map as currently displayed.<br />
        <input
                type="text" size="50" readonly="readonly"
                id="ArtMaps_Share_Container_Link"
                name="ArtMaps_Share_Container_Link"
                onclick="this.focus();this.select();" />
    </div>
    <h3><a href="#">Embed</a></h3>
    <div>
        Copy the code below to embed the map as currently displayed in a webpage or blog.<br />
        <textarea
                rows="5" cols="40" readonly="readonly"
                id="ArtMaps_Share_Container_Embed"
                name="ArtMaps_Share_Container_Embed"
                onclick="this.focus();this.select();">
        </textarea>
    </div>
</div>
</div>
</body>
</html>
