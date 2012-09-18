<?php
/*
Plugin Name: ArtMaps
Plugin URI: http://www.horizon.ac.uk/
Version: v0.01
Author: <a href="http://www.horizon.ac.uk/">Horizon Digital Economy Research</a>
Description: Plugin providing ArtMaps functionality to WordPress.
*/
if(!class_exists("ArtMapsCore")) {

require_once("php/ArtMapsCrypto.php");

class ArtMapsCore {

    public $Crypto;

    public function __construct() {
        $Crypto = new ArtMapsCrypto();
    }

    public function getPluginUrlPrefix() {
        return plugin_dir_url(__FILE__);
    }

    public function onPluginActivated() {
        $this->getNetworkConfiguration();
        $this->updateKeyTable();
        global $wp_rewrite;
        if(!isset($wp_rewrite))
            $wp_rewrite = new WP_Rewrite();
        $wp_rewrite->flush_rules();
    }

    public function onEnqueueScripts() {
        wp_enqueue_script("jquery");
        wp_enqueue_script("jquery-ui-core");
    }

    public function queryVars($vars) {
        $vars[] = "artworkid";
        $vars[] = "artworkID";
        $vars[] = "blogUrl";
        $vars[] = "blogUser";
        $vars[] = "blogPass";
        $vars[] = "memorise";
        return $vars;
    }

    public function rewriteRules($wp_rewrite) {
        $rules = array(
            "artwork/(\d+)/?" => 'index.php?page_id=105&artworkid=$matches[1]'
        );

        $wp_rewrite->rules = $rules + $wp_rewrite->rules;
    }

    public function getOoIMetadata($oid) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "http://artmapscore.cloudapp.net/service/tate/rest/v1/objectsofinterest/$oid/metadata");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($c);
        curl_close($c);
        unset($c);
        return json_decode($data);
    }

    public function parseRequest($wp) {
        if(!array_key_exists("artworkid", $wp->query_vars)) return;
        $this->updateArtworkPageMapTable();
	$aid = $wp->query_vars["artworkid"];
	global $wpdb;
        $pid = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT post_id FROM "
                    . $wpdb->get_blog_prefix()
                    . self::ArtworkPageMapTableSuffix
                    ." WHERE artwork_id = %d",
                $aid));
	if(!$pid) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, "http://artmapscore.cloudapp.net/service/tate/rest/v1/objectsofinterest/$aid/metadata");
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($c);
            curl_close($c);
            $o = json_decode($data);
            if(isset($o->artist)) {
            $post = array(
                "comment_status" => "closed",
                "ping_status" => "open",
                "post_title" => $o->title . " by " . $o->artist,
                "post_content" => "",
                "post_status" => "publish",
                "post_author" => 1,
                "post_type" => "page"
            );
            $pid = wp_insert_post($post);
            update_post_meta($pid, "_wp_page_template", "artwork.php");
            $wpdb->insert($wpdb->get_blog_prefix()
                    . self::ArtworkPageMapTableSuffix,
                    array(
                        "artwork_id" => $aid,
                        "post_id" => $pid),
                    array("%d", "%d"));
            }
        }

        $wp->query_vars["page_id"] = $pid;
    }

    const NetworkConfigPrefix = "ArtMapsPluginNetworkConfiguration";

    public function getNetworkConfiguration() {
        $config = array(
                "CoreServerUrl" => "http://artmapscore.cloudapp.net",
                "MasterKey" => ""
            );
        foreach($config as $key => $value) {
            $name = self::NetworkConfigPrefix . $key;
            $config[$key] = get_site_option($name, $value, true);
            update_site_option($name, $config[$key]);
        }
        return (object)$config;
    }

    public function updateNetworkConfiguration($config) {
        foreach($config as $key => $value) {
            $name = self::NetworkConfigPrefix . $key;
            update_site_option($name, $value);
        }
    }

    public function getCurrentSiteDetails() {
        $details = array();
        global $wpdb;
        $details["name"] = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT name FROM "
                        . $wpdb->get_blog_prefix(1)
                        . self::KeyTableSuffix
                        ." WHERE site_id = %d",
                get_current_blog_id()));
        return (object)$details;
    }

    public function signData($data, $network = false) {
        global $ArtMapsCore;
        if($network) {
            $config = $ArtMapsCore->getNetworkConfiguration();
            $keydata = $config->MasterKey;
        } else {
            global $wpdb;
            $keydata = $wpdb->get_var(
                    $wpdb->prepare(
                            "SELECT key_as_pem FROM "
                            . $wpdb->get_blog_prefix(1)
                            . self::KeyTableSuffix
                            ." WHERE site_id = %d",
                            get_current_blog_id()));
        }
        global $current_user;
        get_currentuserinfo();
        $user = get_user_by("id", $current_user->id);
        $data["username"] = $current_user->user_login;
        $data["userLevel"] = implode(",", $user->roles);
        $data["timestamp"] = intval(time() * 1000);
        ksort($data);
        $key = openssl_get_privatekey($keydata);
        openssl_sign(implode($data), $signature, $key, "SHA256");
        openssl_free_key($key);
        $data["signature"] = base64_encode($signature);
        return $data;
    }

    const KeyTableSuffix = "artmaps_keys";

    private function updateKeyTable() {
        global $wpdb;
        $name = $wpdb->prefix . self::KeyTableSuffix;
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

    const ArtworkPageMapTableSuffix = "artmaps_artworks_pages";

    private function updateArtworkPageMapTable() {
        global $wpdb;
        $name = $wpdb->prefix . self::ArtworkPageMapTableSuffix;
        $sql = "
            CREATE TABLE $name (
            artwork_id bigint(20) NOT NULL,
            post_id bigint(2) NOT NULL,
            PRIMARY KEY  (artwork_id)
        );";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql, true);
    }

    public function getArtworkForPage($pageID) {
        global $wpdb;
        $name = $wpdb->prefix . self::ArtworkPageMapTableSuffix;
        return $wpdb->get_var(
                    $wpdb->prepare(
                            "SELECT artwork_id FROM $name WHERE post_id = %d",
                            $pageID));
    }


}}

if(class_exists("ArtMapsCore") && !isset($ArtMapsCore)) {

    $ArtMapsCore = new ArtMapsCore();

    register_activation_hook(__FILE__,  array($ArtMapsCore, "onPluginActivated"));
    add_filter("query_vars", array(&$ArtMapsCore, "queryVars"));
    add_action("generate_rewrite_rules", array(&$ArtMapsCore, "rewriteRules"));
    add_action("parse_request", array(&$ArtMapsCore, "parseRequest"));
    add_action("wp_enqueue_scripts", array(&$ArtMapsCore, "onEnqueueScripts"));

    if(!function_exists("ArtMapsAdminPage")) {
    function ArtMapsAdminPage() {
        global $ArtMapsCore;
        if(!isset($ArtMapsCore) || !function_exists("add_submenu_page"))
            return;
        require_once("php/artmapsadmin.php");
        add_submenu_page(
                "settings.php",
                "ArtMaps Settings",
                "ArtMaps Settings",
                "manage_sites",
                "artmaps-admin-page",
                array(new ArtMapsAdmin($ArtMapsCore), "display"));
    }}
    add_action("network_admin_menu", "ArtMapsAdminPage");
}

class ArtMaps_Widget_Meta
extends WP_Widget {

    function __construct() {
        $ops = array(
                "classname" => "artmaps_widget_meta",
                "description" => __("Log in/out and admin links"));
        parent::__construct("artmaps_meta", __("ArtMaps Meta"), $ops);
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters("widget_title",
                empty($instance["title"])
                        ? __("Meta")
                        : $instance["title"], $instance,
                $this->id_base);

        echo $before_widget;
        if($title)
            echo $before_title . $title . $after_title;
        ?>
        <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php wp_meta(); ?>
        </ul>
        <?php
        echo $after_widget;
    }

    function update($new, $old) {
    	$instance = $old;
    	$instance["title"] = strip_tags($new["title"]);
    	return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, array("title" => ""));
        $title = strip_tags($instance["title"]);
        ?>
        <p>
            <label for="<?= $this->get_field_id("title") ?>">
                <?php _e("Title:"); ?>
            </label>
            <input class="widefat"
                    id="<?= $this->get_field_id("title") ?>"
                    name="<?= $this->get_field_name("title") ?>"
                    type="text"
                    value="<?= esc_attr($title); ?>" />
        </p>
        <?php
    }

}
add_action("widgets_init", create_function("", "register_widget(\"ArtMaps_Widget_Meta\");"));
?>
