<?php
if(!class_exists("ArtMapsXmlRpc")) {
class ArtMapsXmlRpc {

    const ERROR_CORE_SERVER_COMM_FAILURE = 10;

    public function generateCommentTemplate($objectID) {
        $core = new ArtMapsCoreServer(ArtMapsSite::currentSite());
        try {
            $metadata = $core->fetchObjectMetadata($objectID);
            $link = get_site_url() . "/artwork/" . $objectID;
            $tmpl = new ArtMapsTemplating();
            return $tmpl->getCommentTemplate($objectID, $link, $metadata);
        }
        catch(ArtMapsCoreServerException $e) {
            return new IXR_Error(
                    ArtMapsXmlRpc::ERROR_CORE_SERVER_COMM_FAILURE,
                    $e->getMessage());
        }
    }

    public function createDraftComment($objectID) {
        // Slight problem in that when we are calling this via the browser,
        // we want to authenticate with cookies.  If not we want to do standard
        // authentication with un and password.
        $user = ArtMapsUser::currentUser();

        /*
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

        include("../php/templates/comment.php");

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
        else {*/
        //$postID = preg_replace("/.*<int>(\d+).*/s", '$1', $data);
        /*$redirect = "wp-admin/post.php?post=" . $postID . "&action=edit";
         $res = <<<EOT
        {
        "url": "$url/wp-login.php",
        "username": "$un",
        "password": "$pass",
        "redirect": "$redirect"
        }
        EOT;
        echo $res;
        }*/
    }

    /*public function fetchComments($objectID) {
        global $wpdb;
        $name = $wpdb->prefix . "artmaps_artworks_pages";
        $postID = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT post_id FROM $name WHERE artwork_id = %d",
                        $objectID));
        return get_approved_comments($postID);
    }*/

    /*function doPingback($args) {

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
        if ( $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_author_url = %s", $post_ID, $pagelinkedfrom) ) )
            return new IXR_Error( 48, __( 'The pingback has already been registered.' ) );

        // very stupid, but gives time to the 'from' server to publish !
        sleep(1);

        // Let's check the remote site
        $source = wp_remote_fopen( $pagelinkedfrom );
        $doc = new DOMDocument();
        @$doc->loadHTML($source);
        $xpath = new DOMXPath($doc);
        $body = $xpath->query("//div[@id='artmaps-post-body']")->item(0);
        $body->removeChild($xpath->query("//div[@id='artmaps-data-section']")->item(0));
        $lines = explode("\n", trim($body->textContent));

        $excerpt = $lines[0];

        error_log($excerpt);


        $pagelinkedfrom = str_replace('&', '&amp;', $pagelinkedfrom);

        $context = esc_html( $excerpt );
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

        $comment_ID = wp_new_comment($commentdata);
        do_action('pingback_post', $comment_ID);

        return sprintf(__('Pingback from %1$s to %2$s registered. Keep the web talking! :-)'), $pagelinkedfrom, $pagelinkedto);

    }*/

/*

error_log("ArtMaps: Deprecation warning\n" . __FILE__);
header("Content-type: application/json", true);
require_once("../../../../wp-config.php");
require_once(ABSPATH . "/wp-admin/includes/plugin.php");
error_log("ArtMaps: Deprecation warning - please use the XML-RPC interface for signing requests");
if(!isset($ArtMapsCore) || !is_plugin_active_for_network("artmaps/artmaps.php"))
    die("The ArtMaps plugin is not active");
if(!is_user_logged_in()) {
    header("HTTP/1.1 403 Forbidden");
    return;
}
?>
<?= json_encode($ArtMapsCore->signData(
        json_decode(file_get_contents("php://input"), true), false)) ?>

 */

}}
?>