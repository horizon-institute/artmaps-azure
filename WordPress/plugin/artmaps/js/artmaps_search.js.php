/* Namespace: ArtMaps.Search */
ArtMaps.Search = ArtMaps.Search || {};

ArtMaps.Search.ArtworkSearch = function(query, callback) {
    callback([{"label": "Searching...", "value": "-1"}]);
    var page = 0;
    var term = "";
    var res = new Array();
    if(typeof(query.term) == "string") {
        term = query.term;
    } else {
        term = query.term.item.term;
        page = query.term.item.page;
        res = query.term.item.results;
    }
    if(res.length > 0)
        callback(res);
    $.ajax(
        "http://artmapscore.cloudapp.net/service/tate/rest/v1/external/search?s=tateartwork://" + term + "&p=" + page,
        {
            "dataType": "json",
            "success": function(data) {
                if(data.length == 0) {
                    callback([{"label": "No artworks found", "value": "-1"}]);
                } else {
                    $.each(data, function(i, o) {
                        $.ajax(
                                "http://artmapscore.cloudapp.net/service/tate/rest/v1/objectsofinterest/" + o.ID + "/metadata",
                                {
                                    "dataType": "json",
                                    "success": function(metadata) {
                                        res.unshift({
                                            "label": metadata.title + " by " + metadata.artist,
                                            "value": o.ID
                                        });
                                        callback(res);
                                    }
                                });
                    });  
                    if(page > 0) 
                        res.pop();
                    res.push({
                        "label": "Keep searching...",
                        "value": -10,
                        "term": term,
                        "page": (page + 1),
                        "results" : res
                    });
                    callback(res);
                }
            }
        }
    );
};