<?php
/*
 * Template Name: ArtMaps Artwork Template
 */
require_once("common.php");
$aid = get_query_var("artworkid");
if(!isset($aid) || !$aid)
    $aid = $ArtMapsCore->getArtworkForPage($post->ID);
if(!isset($aid) || !$aid)
    error404("Unable to determine the artwork ID");
$o = $ArtMapsCore->getOoIMetadata($aid);
if(!$o)
    error404("Unable to fetch metadata for artwork");
global $current_user;
get_currentuserinfo();
get_header();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $("#ArtMaps_ArtworkTemplate_ArtworkWPBlogForm").submit(function() {
        $.getJSON(
                "<?= $PluginUrlPrefix ?>api/blogtowp.php",
                $(this).serialize(),
                function(response) {
                    if(response == "-1") {
                        window.alert("An error occurred, please check your details and try again");
                        return;
                    }
                    window.open(response, "_blank");
                });
        return false;
    });
});
</script>
<div>
    <div id=ArtMaps_ArtworkTemplate_ArtworkContainer>
        <?php if(isset($o->imageurl)) { ?>
        <img id="ArtMaps_ArtworkTemplate_ArtworkImage"
                src="<?= $o->imageurl ?>" alt="<?= $o->title ?>" />
        <?php } ?>
        Artist: <?= $o->artist ?> <?= $o->artistdate ?><br />
        Title: <?= $o->title ?><br />
        Date: <?= $o->artworkdate ?><br />
        Reference: <?= $o->reference ?><br />
    </div>
    <div id="ArtMaps_ArtworkTemplate_MapContainer">
        <iframe id="ArtMaps_ArtworkTemplate_MapFrame"
                src="<?= $PluginUrlPrefix ?>php/artwork/map.php?id=<?= $aid ?>">
        </iframe>
    </div>
</div>
<div>
    <?php
        while (have_posts()) {
            the_post();
            comments_template("", true);
        }
    ?>
</div>
<form id="ArtMaps_ArtworkTemplate_ArtworkWPBlogForm" autocomplete="off">
    <input type="hidden" name="artworkID" value="<?= $aid ?>" />
    <input type="submit" value="Leave a comment" />
</form>
<?php get_footer(); ?>
