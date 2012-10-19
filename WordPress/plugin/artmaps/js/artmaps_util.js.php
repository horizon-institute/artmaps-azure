/* Namespace: ArtMaps.Util */
ArtMaps.Util = ArtMaps.Util || {};

ArtMaps.Util.RunOnce = function() {
    var queued = false;
    this.run = function(handler, timeout) {
        if(queued) return;
        queued = true;
        window.setTimeout(function() {
            handler();
            queued = false;
        }, timeout);
    };
};

ArtMaps.Util.browserLocation = function(success, failure) {
    var fallback = function() {
        if(google.loader.ClientLocation) {
            success(new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude));
        } else {
            jQuery.ajax({
                "type": "GET",
                "url": "http://api.ipinfodb.com/v3/ip-city/?key=1b8dea30557cab4a1926ab73ced2eff730a3ca975d193c25ea9907be8a432326&format=json",
                "async": false,
                "dataType": "jsonp",
                "success": function(data) {
                    success(new google.maps.LatLng(data.latitude, data.longitude));
                },
                "error": function() { failure(); }
            });
        }
    }
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
                function(pos) {
                    success(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
                },
                function(err) { fallback(); }
        );
    } else { fallback(); }
}

ArtMaps.Util.toIntCoord = function(f) {
    return parseInt(f * Math.pow(10, 8));
};

ArtMaps.Util.toFloatCoord = function(i) {
    return parseFloat(i) / Math.pow(10, 8);
};

ArtMaps.Util.isEmbedded = function() {
    return (window !== window.parent) ? true : false;
};

ArtMaps.Util.sign = function(data, success, error) {
    $.ajax("../php/artmapssign.php", {
        "type": "post",
        "data": JSON.stringify(data),
        "dataType": "json",
        "contentType": "application/json",
        "processData": false,
        "success": success,
        "error": function(jqXHR, textStatus, errorThrown) {
            if(typeof error !== "undefined")
                error(jqXHR, textStatus, errorThrown);
        }
    });
};

ArtMaps.Util.actionArraySort = function(a, b) {
    return a.timestamp - b.timestamp;
};
