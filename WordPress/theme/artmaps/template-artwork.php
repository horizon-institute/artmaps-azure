<?php
/*
 * Template Name: ArtMaps Artwork Template
 */
require_once("template-common.php");

$aid = get_query_var("artworkid");
if(!isset($aid) || !$aid)
    $aid = $ArtMapsCore->getArtworkForPage($post->ID);
if(!isset($aid) || !$aid)
    error404("Unable to determine the artwork ID");

$o = $ArtMapsCore->getOoIMetadata($aid);
if(!$o)
    error404("Unable to fetch metadata for artwork");

foreach(array(
        "jquery", "jquery-ui-core", "jquery-ui-button", "jquery-ui-dialog",
        "google-maps", "jquery-xcolor", "json2", "markerclusterer", "styledmarker")
    as $script)
        wp_enqueue_script($script) ;
wp_enqueue_style("artmaps");

get_header();
?>
<script type="text/javascript">
<?php require(get_stylesheet_directory() . "/js/artmaps.js.php"); ?>
jQuery(document).ready(function($) {

    if(!navigator.userAgent.match(/iPhone/i) && !navigator.userAgent.match(/iPod/i)) {
        $("#wp-loginform").attr("target", "_blank");
    }

    var config = {
        	"artworkID" : <?= $aid ?>,
            "map": {
                "center": new google.maps.LatLng(51.5171, 0.1062),
                "zoom": 14,
                "mapTypeId": google.maps.MapTypeId.HYBRID,
                "streetViewControl": false,
                "panControl": false,
                "mapTypeControl": false,
                "zoomControlOptions": {
                    "position": google.maps.ControlPosition.LEFT_CENTER
                }
            },
            "clusterer" : {
                "minimumClusterSize": 2,
                "zoomOnClick": false
            }
        };
    var map = new ArtMaps.Map.MapObject($("#ArtMaps_ArtworkTemplate_MapContainer").get(0), config);

    var navLink = $("#artmaps_meta-2");
    var navText = navLink.children("h3").first();
    <?php if (is_user_logged_in()) { ?>
        navLink.click(function() {
            if(window.confirm("Are you sure you wish to log out?")) {
                document.location.href =
                    "<?= str_replace("&amp;", "&", wp_logout_url(get_permalink())); ?>";
            }
        });
        navText.addClass("artmaps-logout-link");
        $(".ArtMaps_ArtWorkTemplate_Suggest_Action").click(function (){
            new ArtMaps.UI.SuggestionMarker(map.getGoogleMap(), map.getObject());
        });
        $(".ArtMaps_ArtWorkTemplate_Comment_Action").click(function (){
            $.getJSON(
                    "<?= $PluginUrlPrefix ?>api/blogtowp.php",
                    { "artworkID": <?= $aid ?> },
                    function(response) {
                        if(response == "-1") {
                            window.alert("An error occurred, please check your details and try again");
                            return;
                        }
                        $("#wp-userlogin").val(response.username);
                        $("#wp-userpass").val(response.password);
                        $("#wp-redirectto").val(response.redirect);
                        var lf = $("#wp-loginform");
                        lf.attr("action", response.url);
                        lf.submit();
                    });
        });
    <?php } else { ?>
        navLink.click(function() { document.location.href =
            "<?= str_replace("&amp;", "&", wp_login_url(get_permalink())); ?>"; });
        navText.addClass("artmaps-login-link");
    <?php } ?>

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
</script>
<div id=ArtMaps_ArtworkTemplate_ArtworkContainer>
    <?php if(isset($o->imageurl)) { ?>
    <img id="ArtMaps_ArtworkTemplate_ArtworkImage"
            src="<?= $o->imageurl ?>"
            alt="<?= $o->title ?>" />
    <?php } ?>
    <p>
        Artist: <?= $o->artist ?> <?= $o->artistdate ?><br />
        Title: <?= $o->title ?><br />
        Date: <?= $o->artworkdate ?><br />
	<a href="http://www.tate.org.uk/art/artworks/<?= $o->reference ?>" target="_blank">View on Tate online</a><br />
    </p>
</div>
<div id="ArtMaps_ArtWorkTemplate_CommentsContainer">
    <?php comments_template("/comments.php", true); ?>
</div>
<hr />
<div id="ArtMaps_ArtWorkTemplate_ActionsContainer">
    <?php if(is_user_logged_in()) { ?>
    <span class="ArtMaps_ArtWorkTemplate_Comment_Action">Comment on this artwork</span>
    <span class="ArtMaps_ArtWorkTemplate_Suggest_Action">Suggest a location</span>
    <?php } ?>
    <span class="ArtMaps_ArtWorkTemplate_View_Action map-view-link-button" style="position: relative;">Change Map View</span><br />
    <ul class="map-view-link-menu" style="display: none;">
        <li><label><input type="radio" name="maptype" value="hybrid" checked="checked" />Hybrid</label></li>
        <li><label><input type="radio" name="maptype" value="roadmap" />Roadmap</label></li>
        <li><label><input type="radio" name="maptype" value="satellite" />Satellite</label></li>
        <li><label><input type="radio" name="maptype" value="terrain" />Terrain</label></li>
    </ul>
</div>
<div id="ArtMaps_ArtworkTemplate_MapContainer"></div>
<div class="artmaps-map-key">
    <span><img src="/wp-content/themes/artmaps/content/red-pin.jpg" alt="" />Original Location</span>
    <span><img src="/wp-content/themes/artmaps/content/blue-pin.jpg" alt="" />Suggested Location</span>
    <span><img src="/wp-content/themes/artmaps/content/green-pin.jpg" alt="" />Your Active Location</span>
</div>
<div id="secondary" class="widget-area" role="complementary">
    <aside id="artmaps_meta-2" class="widget artmaps_widget_meta">
        <h3 class="widget-title"><?= is_user_logged_in() ? "Logout" : "Login" ?></h3>
    </aside>
</div>
<div style="display:none;">
<form id="wp-loginform" name="loginform" action="#" method="post">
<input id="wp-userlogin" type="hidden" name="log" />
<input id="wp-userpass" type="hidden" name="pwd" />
<input id="wp-redirectto" type="hidden" name="redirect_to" value="" />
<input type="hidden" name="rememberme" value="forever" />
<input type="hidden" name="wp-submit" value="Log In" />
<input type="hidden" name="testcookie" value="1" />
</form>
</div>
<?php get_footer(); ?>
