<?php
require_once("../../../../wp-config.php");
header("Content-type: text/plain", true);

global $current_user;
get_currentuserinfo();
$cfg = getUsersArtMapsBlog($current_user);

$aid = $_GET["artworkID"];

$url = $cfg["IsInternal"] ? $cfg["InternalURL"] : $cfg["ExternalURL"];
$un = $cfg["IsInternal"] ? $cfg["InternalUsername"] : $cfg["ExternalUsername"];;
$pass = $cfg["IsInternal"] ? $cfg["InternalPassword"] : $cfg["ExternalPassword"];;
$xmlrpc = $url . "/xmlrpc.php";

$md = $ArtMapsCore->getOoIMetadata($aid);
if(!$md) {
    echo "-1";
    return;
}

$content = "";

if(isset($md->imageurl)) {
    $content .= <<<EOT
<img id="ArtMaps_ArtworkTemplate_ArtworkImage"
    src="$md->imageurl" alt="$md->title" style="max-width: 200px; max-height: 200px;" />

EOT;
}
$link = get_site_url() . "/artwork/" . $aid;
$content .= <<<EOT
Artist: $md->artist $md->artistdate
Title: $md->title
Date: $md->artworkdate
Reference: $md->reference
Artwork Website: <a href="$link">$link</a>
EOT;

$request = <<<EOT
<?xml version="1.0" encoding="iso-8859-1"?>
<methodCall>
<methodName>blogger.newPost</methodName>
<params>
 <param>
  <value>
   <string/>
  </value>
 </param>
 <param>
  <value>
   <string/>
  </value>
 </param>
 <param>
  <value>
   <string>$un</string>
  </value>
 </param>
 <param>
  <value>
   <string>$pass</string>
  </value>
 </param>
 <param>
  <value>
   <string><![CDATA[$content]]></string>
  </value>
 </param>
 <param>
  <value>
   <boolean>0</boolean>
  </value>
 </param>
</params>
</methodCall>
EOT;
$c = curl_init();
curl_setopt($c, CURLOPT_URL, $xmlrpc);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
curl_setopt($c, CURLOPT_POSTFIELDS, $request);
$data = curl_exec($c);
if(strpos($data, "fault") > -1) {
    error_log($data);
    echo "-1";
}
else {
    $postID = preg_replace("/.*<int>(\d+).*/s", '$1', $data);
    echo "\"" . $url . "/wp-admin/post.php?post=" . $postID . "&action=edit" . "\"";
}
?>
