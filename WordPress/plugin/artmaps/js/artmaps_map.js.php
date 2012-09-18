/* Namespace: ArtMaps.Map */
ArtMaps.Map = ArtMaps.Map || {};

ArtMaps.Map.MapObject = function(container, config) {

    var runOnce = new ArtMaps.Util.RunOnce();
    var map = new google.maps.Map(container, config.mapConf);
    var loadingControl = $(document.createElement("img"))
            .attr("src", "../content/loading/50x50.gif")
            .attr("alt", "")
            .css("display", "none");
    var updateCounter = 0;
    
    var placeSearchBox = document.createElement("div");
    var jqPlaceSearchBox = $(placeSearchBox);
    jqPlaceSearchBox.css({"background-color" : "white", "max-height" : "400px", "overflow" : "auto"});
    var placeSearchField = document.createElement("input");
    var jqPlaceSearchField = $(placeSearchField);
    var extraResultsBox = $(document.createElement("div"));
    var extraResults = $(document.createElement("ul"));
    extraResultsBox.append(extraResults);
    
    jqPlaceSearchField.attr({ "type" : "text", "placeholder" : "Search by location" });
    var autoComplete = new google.maps.places.Autocomplete(placeSearchField);
    var service = new google.maps.places.PlacesService(map);
    autoComplete.bindTo("bounds", map);
    google.maps.event.addListener(autoComplete, "place_changed", function() {
        var place = autoComplete.getPlace();
        if(place.id) {
            if(place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
            }
        } else {
            /*var request = {
                    "location" : map.getCenter(),
                    "radius" : 50000,
                    "name" : place.name
            };
            service.search(request, function (results, status, pages) {
                if(status != google.maps.places.PlacesServiceStatus.OK)
                    return;
                for(var i = 0; i < results.length; i++) {
                    var li = $(document.createElement("li"));
                    li.text(results[i].name);
                    extraResults.append(li);
                }
                if(pages.hasNextPage)
                    pages.nextPage();
            });*/
        }
    });

    function getLink() {
        var url = "<?= "http://" . $_SERVER["HTTP_HOST"] . "/wp-content/plugins/artmaps/php/artmapsembed.php?c=" ?>";
        var center = map.getCenter();
        var lat = ArtMaps.Util.toIntCoord(center.lat());
        var lon = ArtMaps.Util.toIntCoord(center.lng());
        var conf = {"map":{"center":{"latitude":lat,"longitude":lon,"zoom":map.getZoom()}}};
        return url + encodeURIComponent(JSON.stringify(conf));
    }

    $(".ArtMaps_Social_Button").button();
    $(".ArtMaps_Social_Button").on("click", function(e){
        var target = $(this).attr("id");
        var link = getLink();
        switch(target)
        {
        case "twitter":
            $.get("artmapsurlshorten.php", {"url": link}, function(data) {
                var l = "https://twitter.com/intent/tweet?url=" + encodeURIComponent(data);
                window.open(l, "_blank");
            });
            break;
        case "facebook":
            //$.get("artmapsurlshorten.php", {"url": link}, function(data) {
                var l = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(link);
                window.open(l, "_blank");
            //});
            break;
        case "wordpress":
            var url = "tate.artmaps.wp.horizon.ac.uk";
            var f = $(document.createElement("div"));
            f.html("Site: http://<input type=\"text\" id=\"site\" />/<br />" +
            		"Username: <input type=\"text\" id=\"username\" /><br />" +
            		"Password: <input type=\"text\" id=\"password\" /><br />" +
            		"<a id=\"blogthis\">Blog this</a>");
            var b = f.children("#blogthis");
            b.button();
            b.on("click", function() {
               f.dialog("close");
               var url = f.children("#site").val();
               var args = {
                   "username": f.children("#username").val(),
                   "password": f.children("#password").val(),
                   "url": url,
                   "content": "<iframe src=\"" + link + "\" width=\"640\" height=\"480\"></iframe>"
               };
               $.ajax("../php/artmapswpdraft.php", {
                   "type": "post",
                   "data": args,
                   "success": function(data) {
                       if(data == "fault") {
                           window.alert("An error occurred, please check you settings and try again");
                           return;
                       }
                       var l = "http://" + url + "/wp-admin/post.php?post=" + data + "&action=edit";
                       window.open(l, "_blank");
                   }
               });
                });
                f.dialog({
                    "title": "Enter your WordPress account details",
                    "autoOpen": true,
                    "show": "fade",
                    "hide": "fade",
                    "width": 640,
                    "modal": true
                });
            break;
        default: break;
        }
     });
    var embedTemplate = "<iframe src=\"$link\" width=\"$width\" height=\"$height\"></iframe>";
    var shareEmbed = $("#ArtMaps_Share_Container_Embed");
    var shareLink = $("#ArtMaps_Share_Container_Link");
    var shareDialog = $("#ArtMaps_Share_Container").first()
            .css("display", "block");
    var shareAccordion = shareDialog.children("#ArtMaps_Share_Container_Accordion").first();

    shareDialog.detach();

    var clusterer = new MarkerClusterer(map, [], config.clustererConf);
    clusterer.on("clusteringend", function() {
        runOnce.run(function() {
            $.each(clusterer.getClusters(),
                    function(i, cluster) {
                        if(cluster.getSize() > config.clustererConf.minimumClusterSize) return;
                        $.each(cluster.getMarkers(), function(j, marker) {
                            marker.location.ObjectOfInterest.loadMetadata();
                        });
            });
        }, 750);
    });

    this.update = function() {
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
    };
    map.on("idle", this.update);

    <?php if(!is_user_logged_in()) { ?>
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
            new ArtMaps.UI.ControlButton("Login").click(function() {
                if(ArtMaps.Util.isEmbedded())
                    window.open("<?= wp_login_url() ?>?redirect_to=" + ArtMaps_Frame_URL, "_parent");
                else
                    window.open("<?= wp_login_url("/wp-content/plugins/artmaps/php/artmapsembed.php")?>", "_self");
            }).get(0));
    <?php } ?>
    
    map.controls[google.maps.ControlPosition.RIGHT_TOP].push(placeSearchField);
    //jqPlaceSearchBox.append(jqPlaceSearchField).append(extraResultsBox);

    /*map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
            new ArtMaps.UI.ControlButton("Share").click(function(){
                var link = getLink();

                function getEmbedText(link, width, height) {
                    return embedTemplate
                            .replace("$link", link)
                            .replace("$width", width)
                            .replace("$height", height);
                }
                
                shareLink.val(link);
                shareEmbed.val(getEmbedText(link, 320, 220));
                shareDialog.dialog({
                    "title": "Share this map",
                    "autoOpen": true,
                    "show": "fade",
                    "hide": "fade",
                    "width": 320,

                });
                shareAccordion.accordion();

            }).get(0)
    );*/

    if(ArtMaps.Util.isEmbedded()) {
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
            new ArtMaps.UI.ControlButton("Expand").click(function() {
                window.open(window.location.href, "_parent");
            }).get(0));
    }

    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(loadingControl.get(0));

    clusterer.on("click", function(e) {
        var zs = new google.maps.MaxZoomService();
        zs.getMaxZoomAtLatLng(map.getCenter(),
            function(result) {
                if(map.getZoom() < result.zoom)
                    return;
                var mks = e.getMarkers();
                if(!mks || !mks.length)
                    return;
                var overlay = $("<div class=\"ArtMaps_UI_ArtworkContainer\"></div>");
                if(e.overlay) overlay = e.overlay;
                e.overlay = overlay;
                function run() {
                    $.each(e.getMarkers(), function(i, marker) {
                        marker.location.ObjectOfInterest.runWhenMetadataLoaded(function(metadata){
                            var content = $(document.createElement("div"))
                                    .addClass("ArtMaps_UI_InfoWindow");
                            if(typeof metadata.title === "undefined") {
                                return;
                                console.log(metadata);
                            }

                            var htmlData =
                                    "<b>" + metadata.title + "</b><br />"
                                    + "by <b>" + metadata.artist + "</b><br />"
                                    + "<a href=\"http://www.tate.org.uk/art/artworks/"
                                    + metadata.reference
                                    + "\" target=\"_blank\">[Visit Web Page]</a><br />";
                            if(typeof metadata.imageurl != "undefined")
                                htmlData += "<img class=\"ArtMaps_UI_InfoWindow_Image\" src=\""
                                        + metadata.imageurl + "\" /><br />";
                            content.html(htmlData);

                            var confirmed = $(document.createElement("span"))
                                    .text(marker.location.Confirmations + " confirmations");
                            content.append(confirmed).append($(document.createElement("br")));
                            overlay.append(content);
                            <?php if (userHasRole($Roles->subscriber)) { ?>
                            var suggest = $(document.createElement("a"))
                                .text("Suggest")
                                .attr("href", "#")
                                .on("click", function() {
                                    overlay.dialog("close");
                                    new ArtMaps.UI.SuggestionMarker(marker.location, map);
                                })
                                .button();
                            content.append(suggest);

                            var confirm = $(document.createElement("a"))
                                .text("Confirm")
                                .attr("href", "#")
                                .on("click", function() {
                                    marker.location.Confirmations++;
                                    confirmed.text(marker.location.Confirmations + " confirmations");
                                    confirm.remove();
                                    ArtMaps.Util.sign({"URI": "confirmation://{\"LocationID\":" + marker.location.ID + "}"},
                                        function(signed){
                                            $.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + marker.location.ObjectOfInterest.ID + "/actions", {
                                                "type": "post",
                                                "data": JSON.stringify(signed),
                                                "dataType": "json",
                                                "contentType": "application/json",
                                                "processData": false,
                                                "success": function(action) {
                                                    marker.location.Actions[marker.location.Actions.length] = action;
                                                }
                                            });
                                        }
                                    );
                                })
                                .button();
                            content.append(confirm);
                            <?php } ?>
                        });
                        marker.location.ObjectOfInterest.loadMetadata();
                    });
                };
                overlay.dialog({
                    "autoOpen": true,
                    "show": { "effect": "fade", "speed": "fast", "complete": function() { setTimeout(run, 1000); } },
                    "hide": { "effect": "fade", "speed": "fast" },
                    "height" : 400,
                    "width" : 640
                });
            });
    });
};
