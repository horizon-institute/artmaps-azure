<?php
if(!class_exists("ArtMapsKeyNotFoundException")){
class ArtMapsKeyNotFoundException
extends Exception {
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}}

if(!class_exists("ArtMapsKeys")) {
class ArtMapsKeys {

    const TableSuffix = "artmaps_keys";

    private $key;

    public function getKey(ArtMapsSite $site) {
        if(isset($this->key))
            return $this->key;
        global $wpdb;
        $this->key = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT key_as_pem FROM "
                            . $wpdb->base_prefix
                            . self::KeyTableSuffix
                            . " WHERE site_id = %d",
                        $site->getID()));
        if($this->key === null) {
            unset($this->key);
            throw new ArtMapsKeyNotFoundException();
        }
        return $this->key;
    }

    public function initialise() {
        global $wpdb;
        $name = $wpdb->base_prefix . self::KeyTableSuffix;
        $sql = "
        CREATE TABLE $name (
        site_id bigint(20) NOT NULL,
        remote_site_id bigint(20) NOT NULL,
        name varchar(200) NOT NULL,
        key_as_pem text NOT NULL,
        PRIMARY KEY  (site_id)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql, true);
    }
}}
?>