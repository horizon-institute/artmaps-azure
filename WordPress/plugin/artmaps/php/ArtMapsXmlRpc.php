<?php
if(!class_exists("ArtMapsXmlRpc")) {
class ArtMapsXmlRpc {

    public function commentTemplate($objectID) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL,
                "http://artmapscore.cloudapp.net/service/tate/rest/v1/objectsofinterest/$objectID/metadata");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $md = json_decode(curl_exec($c));
        curl_close($c);

        if(!$md)
            throw new Exception("Unable to find metadata");

        include("templates/comment.php");

        return $content;
    }

    public function comments($objectID) {
        global $wpdb;
        $name = $wpdb->prefix . "artmaps_artworks_pages";
        $postID = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT post_id FROM $name WHERE artwork_id = %d",
                        $objectID));
        return get_approved_comments($postID);
    }
}
}
?>