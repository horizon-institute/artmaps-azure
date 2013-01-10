<?php
add_filter('show_admin_bar', '__return_false');
remove_action('wp_head', '_admin_bar_bump_cb');

foreach(array(
                'google-jsapi', 'google-maps', 'jquery', 'jquery-ui-complete',
                'jquery-bbq', 'jquery-xcolor', 'json2', 'markerclusterer',
                'styledmarker', 'artmaps-map')
        as $script)
    wp_enqueue_script($script);
foreach(array('jquery-theme', 'artmaps-template-map') as $style)
    wp_enqueue_style($style);

$network = new ArtMapsNetwork();
$blog = $network->getCurrentBlog();
$core = new ArtMapsCoreServer($blog);
wp_localize_script('artmaps-map', 'ArtMapsConfig',
        array(
                'CoreServerPrefix' => $core->getPrefix(),
                'SiteUrl' => get_site_url(),
                'ThemeDirUrl' => get_stylesheet_directory_uri(),
                'IpInfoDbApiKey' => $network->getIpInfoDbApiKey(),
                'SearchSource' => $blog->getSearchSource()
        ));

add_filter("body_class", function($classes) {
    $classes = array("artmaps-artmap");
    return $classes;
}, 99);

global $ArtmapsPageTitle;
$ArtmapsPageTitle = "The Art Map";
get_header();
?>
<script type="text/javascript">
var config = {
    "mapConf": {
        "center": new google.maps.LatLng(51.5171, 0.1062),
        "streetViewControl": false,
        "zoom": 12,
        "mapTypeId": google.maps.MapTypeId.SATELLITE,
        "zoomControlOptions": {
            "position": google.maps.ControlPosition.LEFT_CENTER
        },
        "panControl": false,
        "mapTypeControl": false
    },
    "clustererConf" : {
        "gridSize": 150,
        "minimumClusterSize": 2,
        "zoomOnClick": false,
        "imageSizes": [56],
        "styles": [{
            "url": "http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/images/m2.png",
            "height": 56,
            "width": 56
        }]
    }
};
jQuery(function($) {
    var map = new ArtMaps.Map.MapObject($("#artmaps-mapcontainer"), config);
    map.registerAutocomplete(new google.maps.places.Autocomplete($("#artmaps-search-location-input").get(0)));

    var searchInput = $("#artmaps-search-keyword-input");
    $(".artmaps-search-link").click(function() {
        $(".artmaps-popup").remove();
        $("#artmaps-search-dialog").tabs({
                "activate": function(e, ui) {
                    artmapsSwitchSearch(ui.newTab.children("a").attr("href"));
                }
            }).dialog(
                {
                    "dialogClass": "artmaps-searchbar-popup artmaps-popup",
                    "close" : function () {
                        searchInput.autocomplete("close");
                        $(".pac-container").css("display", "none");
                    }
                });
    });
    searchInput.autocomplete({
        "source" : ArtMaps.Search.objectSearch,
        "minLength" : 3,
        "select": function(event, ui) {
            event.preventDefault();
            if(ui.item.value == -1) return;
            if(ui.item.value == -10) {
                searchInput.autocomplete("search", ui);
                return;
            }
            window.open("<?= get_site_url() ?>/object/" + ui.item.value);
            return;
        }
    });

    $(".artmaps-mapview-link-button").toggle(
            function() { $(".artmaps-mapview-menu").stop().show(); },
            function() { $(".artmaps-mapview-menu").stop().hide(); });
    $(".artmaps-mapview-menu").find("input").change(function(){
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
        $(".artmaps-mapview-link-button").click();
    });

    <?php $templateEngine = new ArtMapsTemplating(); ?>
    ArtMaps.UI.formatMetadata = function(object, metadata, location) {
        <?= $templateEngine->renderMetadataFormatJS($blog) ?>
    }

    ArtMaps.UI.getTitleFromMetadata = function(metadata) {
        <?= $templateEngine->renderMetadataTitleJS($blog) ?>
    }

    ArtMaps.Search.formatResultTitle = function(metadata) {
        <?= $templateEngine->renderSearchResultTitleJS($blog) ?>
    }
});

function artmapsSwitchSearch(option) {
    switch(option) {
    case "#artmaps-search-dialog-location":
        jQuery("#artmaps-search-keyword-input").autocomplete("close");
        jQuery("#artmaps-search-keyword-input").autocomplete("disable");
        jQuery("#artmaps-search-location-input").val(jQuery("#artmaps-search-keyword-input").val());
        google.maps.event.trigger(jQuery("#artmaps-search-location-input").get(0), "focus", {});
        break;
    case "#artmaps-search-dialog-keyword":
        jQuery("#artmaps-search-keyword-input").autocomplete("enable");
        jQuery("#artmaps-search-keyword-input").val(jQuery("#artmaps-search-location-input").val());
        jQuery("#artmaps-search-keyword-input").autocomplete("search");
        break
    }
}
</script>
<div id="artmaps-mapcontainer"></div>
<div class="artmaps-search-link"><div class="artmaps-search-link-button">Search</div></div>
<div class="artmaps-mapview-link">
    <div class="artmaps-mapview-link-button">View</div>
    <ul class="artmaps-mapview-menu" style="display: none;">
        <li><label><input type="radio" name="maptype" value="hybrid" />Hybrid</label></li>
        <li><label><input type="radio" name="maptype" value="roadmap" />Roadmap</label></li>
        <li><label><input type="radio" name="maptype" value="satellite" checked="checked" />Satellite</label></li>
        <li><label><input type="radio" name="maptype" value="terrain" />Terrain</label></li>
    </ul>
</div>
<div id="artmaps-search-dialog">
    <ul>
        <li><a href="#artmaps-search-dialog-keyword"><span>Keyword</span></a></li>
        <li><a href="#artmaps-search-dialog-location"><span>Location</span></a></li>
    </ul>
    <div id="artmaps-search-dialog-keyword">
        <input id="artmaps-search-keyword-input" name="artmaps-search-keyword-input" type="text"
                placeholder="Enter a keyword" autocomplete="off" style="display:inline;" />
        <a href="javascript:jQuery('#artmaps-search-keyword-input').autocomplete('search');" class="artmaps-search-button">Search</a>
        <br />
        <span class="artmaps-searching-by">You are searching by keyword</span>
    </div>
    <div id="artmaps-search-dialog-location">
        <input id="artmaps-search-location-input" name="artmaps-search-location-input" type="text"
                placeholder="Enter a location" autocomplete="off" style="display:inline;" />
        <a href="javascript:google.maps.event.trigger(jQuery(this).get(0), 'focus', {});" class="artmaps-search-button">Search</a>
        <br />
        <span class="artmaps-searching-by">You are searching the map for locations</span>
    </div>
</div>
<?php get_footer(); ?>
