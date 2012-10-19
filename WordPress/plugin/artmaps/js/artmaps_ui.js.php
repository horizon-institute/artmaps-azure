/* Namespace: ArtMaps.UI */
ArtMaps.UI = ArtMaps.UI || {};

ArtMaps.UI.SystemMarkerColor = "#ff0000";
ArtMaps.UI.UserMarkerColor = "#00EEEE";

ArtMaps.UI.InfoWindow = function(location) {

    var isOpen = false;
    var marker = null;
    var map = null
    var metadata = location.ObjectOfInterest.Metadata;

    var content = $(document.createElement("div"))
            .addClass("ArtMaps_UI_InfoWindow");
        var htmlData = "";
        if(typeof metadata.imageurl != "undefined")
            htmlData += "<img class=\"ArtMaps_UI_InfoWindow_Image\" src=\""
                    + metadata.imageurl + "\" /><br />";
        htmlData +=
                "<b>" + metadata.title + "</b><br />"
                + "by <b>" + metadata.artist + "</b><br />"
                + "<a href=\"<?= get_site_url() ?>/artwork/"
                + location.ObjectOfInterest.ID
                + "\" target=\"_parent\">[Visit artwork's page]</a><br />";

    content.html(htmlData);
    var suggestions = $(document.createElement("span"))
            .text(location.ObjectOfInterest.SuggestionCount + " suggestions");
    content.append(suggestions)
            .append($(document.createElement("br")));
    var confirmed = $(document.createElement("span"))
            .text(location.Confirmations + " confirmations");
    content.append(confirmed)
            .append($(document.createElement("br")));

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
    color = $.xcolor.darken(color, location.Confirmations, 10).getHex();
    var marker = new StyledMarker({
        "position": new google.maps.LatLng(location.Latitude, location.Longitude),
        "styleIcon": new StyledIcon(
                StyledIconTypes.MARKER,
                {"color": color, "starcolor": "000000"})
    });
    marker.location = location;
    location.ObjectOfInterest.runWhenMetadataLoaded(function(metadata) {
        marker.setTitle(metadata.title);
        var iw = new ArtMaps.UI.InfoWindow(location);
        marker.on("click", function() {
            iw.toggle(map, marker);
        });
    });
    return marker;
};
