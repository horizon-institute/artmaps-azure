<?php
if(!class_exists("ArtMapsCrypto")) {
class ArtMapsCrypto {

    public function encrypt($data, $isNetwork = false) {
        $key = $isNetwork ? $this->getMasterKey() : $this->getSiteKey();
        $v = openssl_encrypt($data, "aes-128-cbc", "Z-Hp8yUsXliCLhhfJnJbz");
        openssl_free_key($key);
        return $v;
    }

    public function decrypt($data, $isNetwork = false) {
        $key = $isNetwork ? $this->getMasterKey() : $this->getSiteKey();
        $v = openssl_decrypt($data, "aes-128-cbc", "Z-Hp8yUsXliCLhhfJnJbz");
        openssl_free_key($key);
        return $v;
    }

    private function getMasterKey() {
        $config = $ArtMapsCore->getNetworkConfiguration();
        $keydata = $config->MasterKey;
        $key = openssl_get_privatekey($keydata);
        return $key;
    }

    private function getSiteKey() {
        global $wpdb;
        $keydata = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT key_as_pem FROM "
                        . $wpdb->get_blog_prefix(1)
                        . self::KeyTableSuffix
                        ." WHERE site_id = %d",
                        get_current_blog_id()));
        $key = openssl_get_privatekey($keydata);
        return $key;
    }
}
}
?>