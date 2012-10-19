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
    jQuery.ajax("/wp-content/plugins/artmaps/php/artmapssign.php", {
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
