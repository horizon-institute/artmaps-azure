/* Namespace: ArtMaps.Map */
ArtMaps.Map = ArtMaps.Map || {};

ArtMaps.Map.MapObject = function(container, config) {

    var runOnce = new ArtMaps.Util.RunOnce();

    config.map.draggable = false;
    config.map.scrollwheel = false;
    var map = new google.maps.Map(container, config.map);
    google.maps.event.addListenerOnce(map, "click", function(e) {
        e.stop();
        map.setOptions({"draggable":true, "scrollwheel":true});
    });

    var clusterer = new MarkerClusterer(map, [], config.clusterer);

    this.getGoogleMap = function() {
        return map;
    }

    var obj;

    var optimalBounds = null;

    this.getObject = function () { return obj; };

    jQuery.getJSON("<?= $Config->CoreServerUrl ?>/service/<?= $Site->name ?>/rest/v1/objectsofinterest/" + config.artworkID,
		function(object) {
			var north = 0;
			var south = 0;
			var east = 0;
			var west = 0;
			var markers = [];
			obj = new ArtMaps.ObjectOfInterest(object);
			var l = obj.Locations.length;
			var first = false;
			for(var i = 0; i < l; i++) {
				var loc = obj.Locations[i];
				if(!first) {
					north = loc.Latitude;
					south = loc.Latitude;
					east = loc.Longitude;
					west = loc.Longitude;
					first = true;
				} else {
					if(loc.Latitude > north) north = loc.Latitude;
					if(loc.Latitude < south) south = loc.Latitude;
					if(loc.Longitude > east) east = loc.Longitude;
					if(loc.Longitude < west) west = loc.Longitude;
				}
				var marker = new ArtMaps.UI.Marker(loc, map);
				markers.push(marker);
				map.putObjectMarker(loc.ID, marker);
				optimalBounds = new google.maps.LatLngBounds(
	                    new google.maps.LatLng(south, west),
	                    new google.maps.LatLng(north, east));
				map.fitBounds(optimalBounds);
			    if(map.getZoom() > 14)
                    map.setZoom(14);
			}
			clusterer.addMarkers(markers);
		}
	);

    clusterer.on("click", function(e) {
        var mks = e.getMarkers();
        if(!mks || !mks.length)
            return;
        var north = 0;
        var south = 0;
        var east = 0;
        var west = 0;
        var l = mks.length;
        var first = false;
        for(var i = 0; i < l; i++) {
            var mk = mks[i];
            if(!first) {
                north = mk.location.Latitude;
                south = mk.location.Latitude;
                east = mk.location.Longitude;
                west = mk.location.Longitude;
                first = true;
            } else {
                if(mk.location.Latitude > north) north = mk.location.Latitude;
                if(mk.location.Latitude < south) south = mk.location.Latitude;
                if(mk.location.Longitude > east) east = mk.location.Longitude;
                if(mk.location.Longitude < west) west = mk.location.Longitude;
            }
        }
        map.fitBounds(new google.maps.LatLngBounds(
                new google.maps.LatLng(south, west),
                new google.maps.LatLng(north, east)));
    });

    this.switchMapType = function(type) {
        map.setMapTypeId(type);
    }
};
