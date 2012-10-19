<?php
//TODO: This should be embedded in a theme template file rather than as an iframe.
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
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<link type="text/css" rel=StyleSheet href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/black-tie/jquery-ui.css" />
<link type="text/css" rel=StyleSheet href="../css/artmaps.css" />
<script type="text/javascript" src="http://www.google.com/jsapi?key=AIzaSyBDotOtQIdRgtPB6GJnMwRfUEAoluvrdqk"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBDotOtQIdRgtPB6GJnMwRfUEAoluvrdqk&sensor=true&libraries=places"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://tate.artmaps.wp.horizon.ac.uk/wp-content/themes/artmaps/js/lib/jquery.ba-bbq.min.js"></script>
<script type="text/javascript" src="../js/jquery.xcolor.min.js"></script>
<script type="text/javascript" src="../js/json2.js"></script>
<script type="text/javascript" src="../js/markerclusterer.js"></script>
<script type="text/javascript" src="../js/styledmarker.js"></script>
<script type="text/javascript" src="../js/artmaps.js.php"></script>
<style type="text/css">
.pac-container {
    z-index: 9999 !important;
}
</style>
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
        "zoomControlOptions": {
            "position": google.maps.ControlPosition.LEFT_CENTER
        },
        "panControl": false,
        "mapTypeControl": false
    },
    "clustererConf" : {
        "minimumClusterSize": 2,
        "zoomOnClick": false
    }
};
var autocomplete;
$(function() {
    var map = new ArtMaps.Map.MapObject($("#ArtMaps_Map_MapContainer").get(0), config);
    var ac = new google.maps.places.Autocomplete(document.getElementById("ArtMaps_Location_Search"));
    autocomplete = ac;
    map.registerAutocomplete(ac);
    $(".searchbar-link").click(function() {
        $(".ArtMaps_Popup").remove();
        $("#ArtMaps_Search_Container").css({"display": "block"}).dialog(
                {
                    "dialogClass": "searchbar-popup ArtMaps_Popup",
                    "close" : function () { $("#ArtMaps_Artwork_Search").autocomplete("close"); }
                });
    });
    $("#ArtMaps_Artwork_Search").autocomplete({
        "source" : ArtMaps.Search.ArtworkSearch,
        "minLength" : 3,
        "select": function(event, ui) {
            event.preventDefault();
            if(ui.item.value == -1) return;
            if(ui.item.value == -10) {
                $("#ArtMaps_Artwork_Search").autocomplete("search", ui);
                return;
            }
            window.open("/artwork/" + ui.item.value);
            return;
        }
    });
    $(".map-view-link-button").toggle(
            function() { $(".map-view-link-menu").stop().show(); },
            function() { $(".map-view-link-menu").stop().hide(); });
    $(".map-view-link-menu").find("input").change(function(){
        switch($(this).val()) {
        case "hybrid":
            map.switchMapType(google.maps.MapTypeId.HYBRID);
            break;
        case "roadmap":
            map.switchMapType(google.maps.MapTypeId.ROADMAP);
            break;
        case "satellite":
            map.switchMapType(google.maps.MapTypeId.SATELLITE);
            break;
        case "terrain":
            map.switchMapType(google.maps.MapTypeId.TERRAIN);
            break;
        }
        $(".map-view-link-button").click();
    });
});

function ArtMaps_ShowSearch(option) {
    $(".ArtMaps_Search_Container").css({"display": "none"});
    switch(option) {
    case "location":
        $("#ArtMaps_Location_Search_Container").css({"display": "block"});
        $("#ArtMaps_Location_Search").val($("#ArtMaps_Artwork_Search").val());
        google.maps.event.trigger(jQuery("#ArtMaps_Location_Search").get(0), "focus", {});
        break;
    case "artwork":
        $("#ArtMaps_Artwork_Search_Container").css({"display": "block"});
        $("#ArtMaps_Artwork_Search").val($("#ArtMaps_Location_Search").val());
        $("#ArtMaps_Artwork_Search").autocomplete("search");
        break
    }
}

jQuery("#ArtMaps_Artwork_Search").blur(function(){
    ("#ArtMaps_Artwork_Search").autocomplete("close");
});

function ArtMaps_ArtworkSubmit() {
    jQuery("#ArtMaps_Artwork_Search").autocomplete("search");
}
function ArtMaps_LocationSubmit() {
    google.maps.event.trigger(jQuery("#ArtMaps_Location_Search").get(0), "focus", {});
}
</script>
<div class="artwork-popup">

		<div class="artwork-popup-inner">
		Artwork pop-up
		</div>

</div>
<div class="searchbar-link"><div class="searchbar-link-button">Search</div></div>
<div class="map-view-link">
    <div class="map-view-link-button">View</div>
    <ul class="map-view-link-menu" style="display: none;">
        <li><label><input type="radio" name="maptype" value="hybrid" checked="checked" />Hybrid</label></li>
        <li><label><input type="radio" name="maptype" value="roadmap" />Roadmap</label></li>
        <li><label><input type="radio" name="maptype" value="satellite" />Satellite</label></li>
        <li><label><input type="radio" name="maptype" value="terrain" />Terrain</label></li>
    </ul>
</div>
<div id="ArtMaps_Map_MapContainer">
</div>
<div id="ArtMaps_Search_Container">
    <div id="ArtMaps_Artwork_Search_Container" class="ArtMaps_Search_Container">
        <span>You are searching by keyword</span>
        <br />
        <input id="ArtMaps_Artwork_Search" name="ArtMaps_Artwork_Search" type="text"
                placeholder="Enter a keyword" autocomplete="off" style="display:inline;" />
        <a href="javascript:ArtMaps_ArtworkSubmit();" class="artmaps-search-button">Search</a>
        <br />
        <a href="javascript:ArtMaps_ShowSearch('location');">Search map for locations</a>
    </div>
    <div id="ArtMaps_Location_Search_Container" class="ArtMaps_Search_Container" style="display:none;">
        <span>You are searching the map for locations</span>
        <br />
        <input id="ArtMaps_Location_Search" name="ArtMaps_Location_Search" type="text"
                placeholder="Enter a location" autocomplete="off" style="display:inline;" />
        <a href="javascript:ArtMaps_LocationSubmit();" class="artmaps-search-button">Search</a>
        <br />
        <a href="javascript:ArtMaps_ShowSearch('artwork');">Search by keyword instead</a>
    </div>
</div>
</body>
</html>
