<?php
if(!class_exists("ArtMapsCoreServerException")) {
class ArtMapsCoreServerException
extends Exception {
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}}

if(!class_exists("ArtMapsCoreServer")) {
class ArtMapsCoreServer {

    private $site;

    public function __construct(ArtMapsSite $site) {
        $this->site = $site;
    }

    public function fetchObjectMetadata($objectID) {
        $nc = new ArtMapsNetwork();
        $c = curl_init();
        if($c === false)
            throw new ArtMapsCoreServerException("Error initialising Curl");
        $url = $nc->getCoreServerUrl()
                . "/service/"
                . $this->site->getName()
                . "/rest/v1/objectsofinterest/"
                . $objectID
                . "/metadata";
        if(!curl_setopt($c, CURLOPT_URL, $url))
            throw new ArtMapsCoreServerException(curl_error($c));
        if(!curl_setopt($c, CURLOPT_RETURNTRANSFER, 1))
            throw new ArtMapsCoreServerException(curl_error($c));
        $data = curl_exec($c);
        if($data === false)
            throw new ArtMapsCoreServerException(curl_error($c));
        curl_close($c);
        unset($c);
        $jd = json_decode($data);
        if($jd === null)
            throw new ArtMapsCoreServerException(
                    "Error decoding JSON data: " . json_last_error());
        return $jd;
    }
}}
?>