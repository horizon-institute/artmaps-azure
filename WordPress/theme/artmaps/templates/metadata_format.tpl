var con = jQuery(document.createElement("div"))
        .addClass("artmaps-object-popup");
var h = "";
for(var name in metadata) {
    h += "<b>" + name + "</b>: " + metadata[name] + "<br />";
}
h +=
        "<a href=\"" + ArtMapsConfig.SiteUrl + "/object/"
        + object.ID
        + "\" target=\"_blank\">[View]</a>";
con.html(h);
var suggestions = jQuery(document.createElement("span"))
        .text(object.SuggestionCount + " suggestions");
con.append(suggestions)
        .append(jQuery(document.createElement("br")));
return con;
