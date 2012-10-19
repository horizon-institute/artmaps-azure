<?php
$content = "<div id=\"artmaps-post-body\"><div id=\"artmaps-data-section\">";
if(isset($md->imageurl)) {
    $content .= "<img class=\"artmaps-data\" src=\"$md->imageurl\" alt=\"$md->title\" style=\"max-width: 200px; max-height: 200px;\" /><br />";
}
$link = get_site_url() . "/artwork/" . $objectID;
$content .= "<span class=\"artmaps-data\">Artist: $md->artist $md->artistdate</span><br />"
        . "<span class=\"artmaps-data\">Title: $md->title</span><br />"
        . "<span class=\"artmaps-data\">Date: $md->artworkdate</span><br />"
        . "<span class=\"artmaps-data\">Reference: $md->reference</span><br />"
        . "<a class=\"artmaps-data\" href=\"$link\">View the artwork on ArtMaps</a>"
        . "</div><div></div></div>";
?>