/* Namespace: ArtMaps.UI */
ArtMaps.UI = ArtMaps.UI || {};

ArtMaps.UI.SystemMarkerColor = "#ff0000";
ArtMaps.UI.UserMarkerColor = "#00EEEE";
ArtMaps.UI.SuggestionMarkerColor = "#0CF52F";

ArtMaps.UI.InfoWindow = function(location) {
    var isOpen = false;
    var marker = null;
    var map = null

    var content = jQuery(document.createElement("div"))
            .addClass("ArtMaps_UI_InfoWindow");
        var confirmed = jQuery(document.createElement("span"))
                .text(location.Confirmations + " confirmations");
    content.append(confirmed).append(jQuery(document.createElement("br")));

    <?php if (userHasRole($Roles->subscriber)) { ?>
        var userConfirmed = false;
        for(var i = 0; i < location.Actions.length; i++) {
            if(location.Actions[i].userID == <?= $CoreUserID ?>) {
                userConfirmed = true;
                break;
            }
        }
        if(!userConfirmed) {
        var confirm = jQuery(document.createElement("a"))
            .text("Confirm")
            .attr("href", "#")
            .on("click", function(event) {
                event.preventDefault();
                location.Confirmations++;
                confirmed.text(location.Confirmations + " confirmations");
                confirm.remove();
                ArtMaps.Util.sign({"URI": "confirmation://{\"LocationID\":" + location.ID + "}"},
                    function(signed){
                        jQuery.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + location.ObjectOfInterest.ID + "/actions", {
                            "type": "post",
                            "data": JSON.stringify(signed),
                            "dataType": "json",
                            "contentType": "application/json",
                            "processData": false,
                            "success": function(action) {
                                location.Actions[location.Actions.length] = action;
                            }
                        });
                    }
                );
            })
            .button();
        content.append(confirm);
        } else {
            var confirm = jQuery(document.createElement("p"));
            confirm.text("You agreed with the location");
            content.append(confirm);
        }
    <?php } ?>

    this.setContent(content.get(0));
    
    this.on("closeclick", function() {
        isOpen = false;
    });

    this.open = function(_map, _marker) {
        if(isOpen) return;
        map = _map;
        marker = _marker;
        isOpen = true;
        google.maps.InfoWindow.prototype.open.call(this, map, marker);
    };

    this.close = function() {
        if(!isOpen) return;
        isOpen = false;
        google.maps.InfoWindow.prototype.close.call(this);
    };

    this.toggle = function(map, marker) {
        if(isOpen) this.close();
        else this.open(map, marker);
    }
};
ArtMaps.UI.InfoWindow.prototype = new google.maps.InfoWindow();

ArtMaps.UI.Marker = function(location, map) {
    var color = location.Source == "SystemImport"
            ? ArtMaps.UI.SystemMarkerColor
            : ArtMaps.UI.UserMarkerColor;
    color = jQuery.xcolor.darken(color, location.Confirmations, 10).getHex();
    var marker = new StyledMarker({
        "position": new google.maps.LatLng(location.Latitude, location.Longitude),
        "styleIcon": new StyledIcon(
                StyledIconTypes.MARKER,
                {"color": color, "starcolor": "000000"})
    });
    marker.location = location;
    marker.setTitle(location.Confirmations + " confirmations");
    var iw = new ArtMaps.UI.InfoWindow(location);
    marker.on("click", function() {
        iw.toggle(map, marker);
    });
    return marker;
};

ArtMaps.UI.SuggestionInfoWindow = function(marker, object) {
    var content = jQuery(document.createElement("div"))
            .addClass("ArtMaps_UI_SuggestionInfoWindow");
        content.html("<div>Drag this pin and hit confirm</div>");
        var confirm = jQuery(document.createElement("span"))
                .addClass("ArtMaps_UI_Suggestion_Confirm")
                .text("Confirm");
        confirm.click(function() {
            content.html("<img src=\"/wp-content/plugins/artmaps/content/loading/50x50.gif\" alt=\"\" />");
            var pos = marker.getPosition();
            ArtMaps.Util.sign({
                    "error": 0,
                    "latitude": ArtMaps.Util.toIntCoord(pos.lat()),
                    "longitude": ArtMaps.Util.toIntCoord(pos.lng())
                },
                function(signed){
                    jQuery.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + object.ID + "/locations", {
                        "type": "post",
                        "data": JSON.stringify(signed),
                        "dataType": "json",
                        "contentType": "application/json",
                        "processData": false,
                        "success": function(newLocation) {
                            ArtMaps.Util.sign({"URI": "suggestion://{\"LocationID\":" + newLocation.ID + "}"},
                                function(signed){
                                    jQuery.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + object.ID + "/actions", {
                                        "type": "post",
                                        "data": JSON.stringify(signed),
                                        "dataType": "json",
                                        "contentType": "application/json",
                                        "processData": false,
                                        "success": function(action) {
                                            self.close();
                                            var map = marker.getMap();
                                            marker.setMap(null);
                                            var loc = new ArtMaps.Location(newLocation, object, [action]);
                                            var nmarker = new ArtMaps.UI.Marker(loc, map);
                                            nmarker.setMap(map);
                                        }
                                    });
                                }
                            );
                        }
                    });
                });
        });
        content.append(confirm);
        var cancel = jQuery(document.createElement("span"))
                .addClass("ArtMaps_UI_Suggestion_Cancel")
                .text("Cancel");
        cancel.click(function() { marker.setMap(null); });
        content.append(cancel);
    this.setContent(content.get(0));
        
    this.on("closeclick", function() {
        marker.setMap(null);
    });
};
ArtMaps.UI.SuggestionInfoWindow.prototype = new google.maps.InfoWindow();

ArtMaps.UI.SuggestionMarker = function(map, object) {
    var marker = new StyledMarker({
        "draggable": true,
        "map": map,
        "position": map.getCenter(),
        "styleIcon": new StyledIcon(
                StyledIconTypes.MARKER,
                {"color": ArtMaps.UI.SuggestionMarkerColor, "starcolor": "000000"})
    });
    marker.setTitle("Drag me");
    var iw = new ArtMaps.UI.SuggestionInfoWindow(marker, object);
    iw.open(map, marker);
    return marker;
};
