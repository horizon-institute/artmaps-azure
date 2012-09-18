<?php 
require_once("../../../../../wp-config.php");
if(isset($_GET["id"])) {
	$id = $_GET["id"];
}
if(isset($id)) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, "http://artmapscore.cloudapp.net/service/tate/rest/v1/objectsofinterest/$id/metadata");
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($c);
	curl_close($c);
	$o = json_decode($data);
}
?>
<?php get_header(); ?>
<div id="container">
	<div id="artwork" style="width:500px;display:table-cell;vertical-align:top;">
		Artist: <?= $o->artist ?> (<?= $o->artistdate ?>)<br />
		Title: <?= $o->title ?> <br />
		Date: <?= $o->artworkdate ?><br />
		Reference: <?= $o->reference ?><br />
		<?php if(isset($o->imageurl)) {
			?><img src="<?= $o->imageurl ?>" alt="<?= $o->title ?>" style="max-width:500px;" /><?php
		} ?>
	</div>
	<div id="map" style="width:500px;display:table-cell;">
		<iframe src="map.php?id=<?= $id ?>" width="100%" height="400"></iframe>
	</div>
</div>
<div>
	The following thoughts have been posted about this artwork:
</div>
<?php get_footer(); ?>
