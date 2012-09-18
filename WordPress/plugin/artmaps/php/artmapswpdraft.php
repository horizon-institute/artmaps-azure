<?php
header("Content-type: text/plain", true);
$username = $_POST["username"];
$password = $_POST["password"];
$url = $_POST["url"];
if(!(strpos($url, "http") == 0))
    $url = "http://" . $url;
if(strrpos($url, "/") != (strlen($url) - 1))
    $url = $url . "/";
$url = $url . "xmlrpc.php";
$content = htmlentities($_POST["content"]) ;
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
   <string>$username</string>
  </value>
 </param>
 <param>
  <value>
   <string>$password</string>
  </value>
 </param>
 <param>
  <value>
   <string>$content</string>
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
curl_setopt($c, CURLOPT_URL, $url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
curl_setopt($c, CURLOPT_POSTFIELDS, $request);
$data = curl_exec($c);
if(strpos($data, "fault") > -1)
    echo "fault";
else
    echo preg_replace("/.*<int>(\d+).*/s", '$1', $data);
?>