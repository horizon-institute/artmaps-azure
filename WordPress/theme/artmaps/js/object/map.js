/* Namespace: ArtMaps.Map */
ArtMaps.Map = ArtMaps.Map || {};

ArtMaps.Map.MapObject = function(container, config) {

    var self = this;
    var map = new google.maps.Map(container.get(0), config.mapConf);
    var clusterer = new MarkerClusterer(map, [], config.clustererConf);
    
    var hashstate = jQuery.bbq.getState();
    if(hashstate.maptype)
        map.setMapTypeId(hashstate.maptype);

    jQuery.getJSON(ArtMapsConfig.CoreServerPrefix + "objectsofinterest/" + config.objectID,
		function(object) {
            var obj = new ArtMaps.ObjectOfInterest(object);
            self.suggest = function() {
                new ArtMaps.UI.SuggestionMarker(map, obj);                
            };
			var markers = new Array();
			jQuery.each(obj.Locations, function(i, loc) {
                var marker = new ArtMaps.UI.Marker(loc, map);
                markers.push(marker);
                map.putObjectMarker(loc.ID, marker);
			});
			clusterer.addMarkers(markers);
			clusterer.fitMapToMarkers();
		}
	);

    this.switchMapType = function(type) {
        map.setMapTypeId(type);
        var hashstate = jQuery.bbq.getState();
        hashstate.maptype = type;
        jQuery.bbq.pushState(hashstate);
    };
    
    this.getMapType = function() {
        return map.getMapTypeId();
    };
    
    this.suggest = function() { };
    
    this.resize = function() {
        google.maps.event.trigger(map, "resize");
        jQuery.each(clusterer.getMarkers(), function(i, m) {
           m.reset();
        });
    };
    
    this.getCenter = function() {
        return map.getCenter();
    };
    
    this.setCenter = function(c) {
        map.setCenter(c);
    };
};
