/* Namespace: ArtMaps.UI */
ArtMaps.UI = ArtMaps.UI || {};

ArtMaps.UI.SystemMarkerColor = "#ff0000";
ArtMaps.UI.UserMarkerColor = "#00EEEE";

ArtMaps.UI.SuggestionWindow = function(location) {
    this.isOpen = false;

    var marker = null;
    var map = null

    var self = this;
    var metadata = location.ObjectOfInterest.Metadata;
    var content = $(document.createElement("div"))
            .addClass("ArtMaps_UI_InfoWindow");
    var htmlData =
            "<b>" + metadata.title + "</b><br />"
            + "by <b>" + metadata.artist + "</b><br />"
    if(typeof metadata.imageurl != "undefined")
        htmlData += "<img class=\"ArtMaps_UI_InfoWindow_Image\" src=\""
                + metadata.imageurl + "\" /><br />";
    content.html(htmlData);

    <?php if (userHasRole($Roles->subscriber)) { ?>
    var confirm = $(document.createElement("a"))
        .text("Submit this location")
        .attr("href", "#")
        .on("click", function() {
            content.effect("fade", { "callback": function() {
                self.close();
                marker.setMap(null);
            }});
            var pos = marker.getPosition();
            ArtMaps.Util.sign({
                    "error": 0,
                    "latitude": ArtMaps.Util.toIntCoord(pos.lat()),
                    "longitude": ArtMaps.Util.toIntCoord(pos.lng())
                },
                function(signed){
                    $.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + location.ObjectOfInterest.ID + "/locations", {
                        "type": "post",
                        "data": JSON.stringify(signed),
                        "dataType": "json",
                        "contentType": "application/json",
                        "processData": false,
                        "success": function(newLocation) {
                            ArtMaps.Util.sign({"URI": "suggestion://{\"LocationID\":" + newLocation.ID + "}"},
                                function(signed){
                                    $.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + location.ObjectOfInterest.ID + "/actions", {
                                        "type": "post",
                                        "data": JSON.stringify(signed),
                                        "dataType": "json",
                                        "contentType": "application/json",
                                        "processData": false,
                                        "success": function(action) {
                                            self.close();
                                            marker.setMap(null);
                                            var loc = new ArtMaps.Location(newLocation, location.ObjectOfInterest, [action]);
                                            var nmarker = new ArtMaps.UI.Marker(loc, map);
                                            nmarker.setMap(map);
                                        }
                                    });
                                }
                            );
                        }
                    });
                });
        })
        .button();
    content.append(confirm);
    var cancel = $(document.createElement("a"))
        .text("Cancel")
        .attr("href", "#")
        .on("click", function() {
            self.close();
            marker.setMap(null);
         })
        .button();
    content.append(cancel);
    <?php } ?>

    this.setContent(content.get(0));
    this.setOptions({
        "disableAutoPan": true
    });

    this.open = function(mmap, mmarker) {
        map = mmap;
        marker = mmarker;
        google.maps.InfoWindow.prototype.open.call(this, map, marker);
    };
};
ArtMaps.UI.SuggestionWindow.prototype = new google.maps.InfoWindow();

ArtMaps.UI.InfoWindow = function(location) {
    this.isOpen = false;

    var marker = null;
    var map = null

    var self = this;
    var metadata = location.ObjectOfInterest.Metadata;
    var content = $(document.createElement("div"))
            .addClass("ArtMaps_UI_InfoWindow");
    var htmlData =
            "<b>" + metadata.title + "</b><br />"
            + "by <b>" + metadata.artist + "</b><br />"
            + "<a href=\"<?= get_site_url() ?>/artwork/"
            + location.ObjectOfInterest.ID
            + "\" target=\"_parent\">[Visit artwork's page]</a><br />";
    if(typeof metadata.imageurl != "undefined")
        htmlData += "<img class=\"ArtMaps_UI_InfoWindow_Image\" src=\""
                + metadata.imageurl + "\" /><br />";
    content.html(htmlData);

    var confirmed = $(document.createElement("span"))
            .text(location.Confirmations + " confirmations");
    content.append(confirmed).append($(document.createElement("br")));

    <?php if (userHasRole($Roles->subscriber)) { ?>
    var suggest = $(document.createElement("a"))
        .text("Suggest")
        .attr("href", "#")
        .on("click", function() {
            self.close();
            new ArtMaps.UI.SuggestionMarker(location, map);
        })
        .button();
    content.append(suggest);

    var confirm = $(document.createElement("a"))
        .text("Confirm")
        .attr("href", "#")
        .on("click", function() {
            location.Confirmations++;
            confirmed.text(location.Confirmations + " confirmations");
            confirm.remove();
            ArtMaps.Util.sign({"URI": "confirmation://{\"LocationID\":" + location.ID + "}"},
                function(signed){
                    $.ajax("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + location.ObjectOfInterest.ID + "/actions", {
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
    <?php } ?>

    this.setContent(content.get(0));
    this.setOptions({
        "disableAutoPan": true
    });
    this.on("closeclick", function() {
        this.isOpen = false;
    });

    this.open = function(mmap, mmarker) {
        map = mmap;
        marker = mmarker;
        if(this.isOpen) return;
        this.isOpen = true;
        google.maps.InfoWindow.prototype.open.call(this, map, marker);
    };

    this.close = function() {
        if(!this.isOpen) return;
        this.isOpen = false;
        google.maps.InfoWindow.prototype.close.call(this);
    };
};
ArtMaps.UI.InfoWindow.prototype = new google.maps.InfoWindow();

ArtMaps.UI.Marker = function(location, map) {
    var color = location.Source == "SystemImport"
            ? ArtMaps.UI.SystemMarkerColor
            : ArtMaps.UI.UserMarkerColor;
    color = $.xcolor.darken(color, location.Confirmations, 10).getHex();
    var marker = new StyledMarker({
        "position": new google.maps.LatLng(location.Latitude, location.Longitude),
        "styleIcon": new StyledIcon(
                StyledIconTypes.MARKER,
                {"color": color, "starcolor": "000000"})});
    marker.location = location;
    location.ObjectOfInterest.runWhenMetadataLoaded(function(metadata) {
        marker.setTitle(metadata.title);
        var iw = new ArtMaps.UI.InfoWindow(location);
        marker.on("click", function() {
            if(iw.isOpen) iw.close();
            else iw.open(map, marker);
        });
    });
    return marker;
};

ArtMaps.UI.SuggestionMarker = function(location, map) {
    var marker = new StyledMarker({
        "map" : map,
        "draggable" : true,
        "position": new google.maps.LatLng(location.Latitude, location.Longitude),
        "styleIcon": new StyledIcon(
                StyledIconTypes.MARKER,
                {"color": ArtMaps.UI.UserMarkerColor, "starcolor": "000000"})});
    marker.initialLocation = location;
    location.ObjectOfInterest.runWhenMetadataLoaded(function(metadata) {
        marker.setTitle(metadata.title);
        var iw = new ArtMaps.UI.SuggestionWindow(location);
        iw.open(map, marker);
    });
    return marker;
};

ArtMaps.UI.ControlButton = function(text) {

    var div = $(document.createElement("div"))
            .addClass("ArtMaps_UI_ControlButton");

    var wrapper = $(document.createElement("div"))
            .addClass("ArtMaps_UI_ControlButtonWrapper");
    div.append(wrapper);

    var inner = $(document.createElement("div"))
            .addClass("ArtMaps_UI_ControlButtonInner")
            .text(text)
            .attr("title", text);
    wrapper.append(inner);

    return div;
};