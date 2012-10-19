<?php
/*
Plugin Name: ArtMaps
Plugin URI: http://www.horizon.ac.uk/
Version: v0.01
Author: <a href="http://www.horizon.ac.uk/">Horizon Digital Economy Research</a>
Description: Plugin providing ArtMaps functionality to WordPress.
*/
if(!class_exists("ArtMapsCore")) {
//TODO: Clean up all the mess!
require_once("php/ArtMapsCrypto.php");
require_once("php/ArtMapsXmlRpc.php");

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
            update_post_meta($pid, "_wp_page_template", "template-artwork.php");
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

function intercept($args) {

	error_log("intercepted");

    global $wp_xmlrpc_server;

    global $wpdb;

    do_action('xmlrpc_call', 'pingback.ping');

    $wp_xmlrpc_server->escape($args);

    $pagelinkedfrom = $args[0];
    $pagelinkedto   = $args[1];

    $title = '';

    $pagelinkedfrom = str_replace('&amp;', '&', $pagelinkedfrom);
    $pagelinkedto = str_replace('&amp;', '&', $pagelinkedto);
    $pagelinkedto = str_replace('&', '&amp;', $pagelinkedto);

    // Check if the page linked to is in our site
    $pos1 = strpos($pagelinkedto, str_replace(array('http://www.','http://','https://www.','https://'), '', get_option('home')));
    if ( !$pos1 )
        return new IXR_Error(0, __('Is there no link to us?'));

    // let's find which post is linked to
    // FIXME: does url_to_postid() cover all these cases already?
    //        if so, then let's use it and drop the old code.
    $urltest = parse_url($pagelinkedto);
    if ( $post_ID = url_to_postid($pagelinkedto) ) {
        $way = 'url_to_postid()';
    } elseif ( preg_match('#p/[0-9]{1,}#', $urltest['path'], $match) ) {
        // the path defines the post_ID (archives/p/XXXX)
        $blah = explode('/', $match[0]);
        $post_ID = (int) $blah[1];
        $way = 'from the path';
    } elseif ( preg_match('#p=[0-9]{1,}#', $urltest['query'], $match) ) {
        // the querystring defines the post_ID (?p=XXXX)
        $blah = explode('=', $match[0]);
        $post_ID = (int) $blah[1];
        $way = 'from the querystring';
    } elseif ( isset($urltest['fragment']) ) {
        // an #anchor is there, it's either...
        if ( intval($urltest['fragment']) ) {
            // ...an integer #XXXX (simplest case)
            $post_ID = (int) $urltest['fragment'];
            $way = 'from the fragment (numeric)';
        } elseif ( preg_match('/post-[0-9]+/',$urltest['fragment']) ) {
            // ...a post id in the form 'post-###'
            $post_ID = preg_replace('/[^0-9]+/', '', $urltest['fragment']);
            $way = 'from the fragment (post-###)';
        } elseif ( is_string($urltest['fragment']) ) {
            // ...or a string #title, a little more complicated
            $title = preg_replace('/[^a-z0-9]/i', '.', $urltest['fragment']);
            $sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title RLIKE %s", like_escape( $title ) );
            if (! ($post_ID = $wpdb->get_var($sql)) ) {
                // returning unknown error '0' is better than die()ing
                return new IXR_Error(0, '');
            }
            $way = 'from the fragment (title)';
        }
    } else {
        // TODO: Attempt to extract a post ID from the given URL
        return new IXR_Error(33, __('The specified target URL cannot be used as a target. It either doesn&#8217;t exist, or it is not a pingback-enabled resource.'));
    }
    $post_ID = (int) $post_ID;

    $post = get_post($post_ID);

    if ( !$post ) // Post_ID not found
        return new IXR_Error(33, __('The specified target URL cannot be used as a target. It either doesn&#8217;t exist, or it is not a pingback-enabled resource.'));

    if ( $post_ID == url_to_postid($pagelinkedfrom) )
        return new IXR_Error(0, __('The source URL and the target URL cannot both point to the same resource.'));

    // Check if pings are on
    if ( !pings_open($post) )
        return new IXR_Error(33, __('The specified target URL cannot be used as a target. It either doesn&#8217;t exist, or it is not a pingback-enabled resource.'));

    // Let's check that the remote site didn't already pingback this entry
    /*
    if ( $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_author_url = %s", $post_ID, $pagelinkedfrom) ) )
        return new IXR_Error( 48, __( 'The pingback has already been registered.' ) );
    */
    // very stupid, but gives time to the 'from' server to publish !
    sleep(1);

    // Let's check the remote site
    $source = wp_remote_fopen( $pagelinkedfrom );
    $doc = new DOMDocument();
    @$doc->loadHTML($source);
    $xpath = new DOMXPath($doc);

    $excerpt = "";
    $mbody = $xpath->query("//div[@id='post_template']");
    if($mbody->length > 0) {
        error_log("Is mobile");
        $body = $mbody->item(0);
        $body->removeChild($xpath->query("//div[@id='artmaps-post-body']")->item(0));
        $images = $xpath->query("//div[@id='post_media']//img", $body);
    } else {
        error_log("Not mobile");
        $body = $xpath->query("//div[@id='artmaps-post-body']")->item(0);
        $body->removeChild($xpath->query("//div[@id='artmaps-data-section']")->item(0));
        $images = $xpath->query("//div[@id='artmaps-post-body']//img", $body);
    }

    $lines = explode("\n", trim($body->textContent));
    $excerpt = "<div class=\"artmaps-comment-text\">" . $lines[0] . "</div>";

    if($images->length > 0) {
        $excerpt = "<div class=\"artmaps-comment-image\">" . $images->item(0)->C14N() . "</div>" . $excerpt;
    }

    $pagelinkedfrom = str_replace('&', '&amp;', $pagelinkedfrom);

    $context = $excerpt;
    $pagelinkedfrom = $wpdb->escape( $pagelinkedfrom );

    $comment_post_ID = (int) $post_ID;
    $comment_author = $xpath->query("//title")->item(0)->textContent;
    $comment_author_email = '';
    $wp_xmlrpc_server->escape($comment_author);
    $comment_author_url = $pagelinkedfrom;
    $comment_content = $context;
    $wp_xmlrpc_server->escape($comment_content);
    $comment_type = 'pingback';

    $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_author_email', 'comment_content', 'comment_type');

    $comment_ID = wp_insert_comment($commentdata);



    do_action('pingback_post', $comment_ID);

    return sprintf(__('Pingback from %1$s to %2$s registered. Keep the web talking! :-)'), $pagelinkedfrom, $pagelinkedto);

};
function interceptwrap($args) {
    try {
        error_log("ArtMaps: Begin processing pingback");
        $r = intercept($args);
        error_log(print_r($r, true));
        return $r;
    }
    catch(Exception $e) {
        error_log("ArtMaps: Error processing pingback - " . $e->getMessage());
        throw $e;
    }
}
add_action("widgets_init", create_function("", "register_widget(\"ArtMaps_Widget_Meta\");"));
add_filter("xmlrpc_methods", function($methods) {
    $svc = new ArtMapsXmlRpc();
    $methods["artmaps.commentTemplate"] = array($svc, "commentTemplate");
    $methods["artmaps.comments"] = array($svc, "comments");
    $methods["pingback.ping"] = "interceptwrap";
    return $methods;
});

add_action("login_head", function() {
    echo "<link type=\"text/css\" rel=\"StyleSheet\" href=\"wp-content/plugins/artmaps/css/login.css\" />";
});
