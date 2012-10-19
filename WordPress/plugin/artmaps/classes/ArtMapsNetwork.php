<?php
if(!class_exists("ArtMapsNetwork")) {
class ArtMapsNetwork {

    const NetworkConfigPrefix = "ArtMapsPluginNetworkConfiguration";

    const CoreServerUrlKey = "CoreServerUrl";

    const MasterKeyKey = "";

    public function getCoreServerUrl() {
        $default = "http://artmapscore.cloudapp.net";
        $k = ArtMapsNetwork::NetworkConfigPrefix
                . ArtMapsNetwork::CoreServerUrlKey;
        return get_site_option($k, $default, true);
    }

    public function setCoreServerUrl($url) {
        $k = ArtMapsNetwork::NetworkConfigPrefix
                . ArtMapsNetwork::CoreServerUrlKey;
        update_site_option($k, $url);
    }

    public function getMasterKey() {
        $default = "";
        $k = ArtMapsNetwork::NetworkConfigPrefix
                . ArtMapsNetwork::MasterKeyKey;
        return get_site_option($k, $default, true);
    }

    public function setMasterKey($key) {
        $k = ArtMapsNetwork::NetworkConfigPrefix
                . ArtMapsNetwork::MasterKeyKey;
        update_site_option($k, $key);
    }
}}
?>