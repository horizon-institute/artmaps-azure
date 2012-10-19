/* Namespace: ArtMaps.Map */
ArtMaps.Map = ArtMaps.Map || {};

ArtMaps.Map.MapObject = function(container, config) {

    var runOnce = new ArtMaps.Util.RunOnce();
    var map = new google.maps.Map(container, config.mapConf);
    var service = new google.maps.places.PlacesService(map);

    var hashstate = jQuery.bbq.getState();
    if(hashstate.zoom) {
        map.setCenter(new google.maps.LatLng(hashstate.lat, hashstate.lng));
        map.setZoom(parseInt(hashstate.zoom));
    } else {
        ArtMaps.Util.browserLocation(
            function(pos) { map.setCenter(pos); },
            function() { });
    } 
    
    var clusterer = new MarkerClusterer(map, [], config.clustererConf);
    clusterer.on("clusteringend", function() {
        runOnce.run(function() {
            $.each(clusterer.getClusters(),
                    function(i, cluster) {
                        if(cluster.getSize() > config.clustererConf.minimumClusterSize)
                            return;
                        $.each(cluster.getMarkers(), function(j, marker) {
                            marker.location.ObjectOfInterest.loadMetadata();
                        });
            });
        }, 750);
    });

    var loadingControl = $(document.createElement("img"))
            .attr("src", "../content/loading/50x50.gif")
            .attr("alt", "")
            .css("display", "none");
    map.controls[google.maps.ControlPosition.LEFT_CENTER].push(loadingControl.get(0));
    var updateCounter = 0;
    map.on("idle", function() {
	var centre = map.getCenter();
	jQuery.bbq.pushState({
                "zoom": map.getZoom(),
                "lat": centre.lat(),
                "lng": centre.lng()
        });
        updateCounter++;
        if(updateCounter > 0)
            loadingControl.css("display", "inline");
        var bounds = map.getBounds();
        $.getJSON("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/search/?"
                + "boundingBox.northEast.latitude=" + ArtMaps.Util.toIntCoord(bounds.getNorthEast().lat())
                + "&boundingBox.southWest.latitude=" + ArtMaps.Util.toIntCoord(bounds.getSouthWest().lat())
                + "&boundingBox.northEast.longitude=" + ArtMaps.Util.toIntCoord(bounds.getNorthEast().lng())
                + "&boundingBox.southWest.longitude=" + ArtMaps.Util.toIntCoord(bounds.getSouthWest().lng()),
            function(objects) {
                var markers = [];
                var l = objects.length;
                for(var i = 0; i < l; i++) {
                    var obj = new ArtMaps.ObjectOfInterest(objects[i]);
                    var ll = obj.Locations.length;
                    for(var j = 0; j < ll; j++) {
                        var loc = obj.Locations[j];
                        if(map.hasObjectMarker(loc.ID)) continue;
                        var marker = new ArtMaps.UI.Marker(loc, map);
                        markers.push(marker);
                        map.putObjectMarker(loc.ID, marker);
                    }
                }
                clusterer.addMarkers(markers);
                updateCounter--;
                if(updateCounter < 1)
                    loadingControl.css("display", "none");
            });
    });

    clusterer.on("click", function(cluster) {
        var markers = cluster.getMarkers();
        if(!markers || !markers.length) return;
        $(".ArtMaps_Popup").remove();
        var firstLoad = false;
        if(!cluster.overlay) {
            firstLoad = true;
            cluster.overlay = $("<div class=\"ArtMaps_UI_ArtworkContainer\"></div>");
        }
        
        var loadArtworks = function () {
            $.each(markers, function(i, marker) {
                var content = $(document.createElement("div"))
                    .addClass("ArtMaps_UI_InfoWindow");
                content.html("<img src=\"../content/loading/25x25.gif\" alt=\"\" />");
                cluster.overlay.append(content);
                marker.location.ObjectOfInterest.runWhenMetadataLoaded(function(metadata){
                    if(typeof metadata.title === "undefined")
                        return;
                        var htmlData =
                                "<b>" + metadata.title + "</b><br />"
                                + "by <b>" + metadata.artist + "</b><br />"
                                + "<a href=\"<?= get_site_url() ?>/artwork/"
                                + marker.location.ObjectOfInterest.ID
                                + "\" target=\"_parent\">[Visit artwork's page]</a><br />";
                        if(typeof metadata.imageurl != "undefined")
                            htmlData += "<img class=\"ArtMaps_UI_InfoWindow_Image\" src=\""
                                    + metadata.imageurl + "\" /><br />";
                        content.html(htmlData);
                    var confirmed = $(document.createElement("span"))
                            .text(marker.location.Confirmations + " confirmations");
                    content.append(confirmed).append($(document.createElement("br")));
                });
                marker.location.ObjectOfInterest.loadMetadata();
            });
        };
                
        cluster.overlay.dialog({
            "autoOpen": true,
            "show": { "effect": "fade", "speed": 1, "complete": firstLoad ? loadArtworks : function() {} },
            "hide": { "effect": "fade", "speed": 1 },
            "resizable": false,
            "dialogClass": "artwork-list-popup ArtMaps_Popup"
        });
    });

    this.switchMapType = function(type) {
        map.setMapTypeId(type);
    }

    this.registerAutocomplete = function(autoComplete) {
        autoComplete.bindTo("bounds", map);
        google.maps.event.addListener(autoComplete, "place_changed", function() {
            var place = autoComplete.getPlace();
            if(place.id) {
                if(place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                }
            }
            $(".ArtMaps_Popup").remove();
        });
    }
};
