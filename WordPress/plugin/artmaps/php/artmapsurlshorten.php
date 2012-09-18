<?php
header("Content-type: text/plain", true);
$url = urlencode($_GET["url"]);
$url = "https://api-ssl.bitly.com/v3/shorten?apiKey=R_066f44e2573d729acb4659f968ff5eff&login=dominicjprice&longUrl=" . $url;
$c = curl_init();
curl_setopt($c, CURLOPT_URL, $url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($c);
curl_close($c);
$o = json_decode($data);
echo $o->data->url;
?>