<?php
if(!class_exists('ArtMapsBlogNotFoundException')){
class ArtMapsBlogNotFoundException
extends Exception {
    public function __construct($message = '', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}}

if(!class_exists('ArtMapsBlog')) {
class ArtMapsBlog {

    const BlogConfigPrefix = 'ArtMapsPluginBlogConfiguration';

    const CommentTemplateOptionKey = 'CommentTemplate';

    const MetadataFormatJSOptionKey = 'MetadataFormatJS';

    const MetadataTitleJSOptionKey = 'MetadataTitleJS';

    const SearchResultTitleJSOptionKey = 'SearchResultTitleJS';

    const SearchSourceOptionKey = 'SearchSource';

    const MetadataTitleTemplateKey = 'MetadataTitleTemplate';

    const ObjectPageMapTableSuffix = 'artmaps_object_pages';

    const ObjectMetadataFormatJSOptionKey = 'ObjectMetadataFormatJS';

    private $blogID, $remoteID, $name, $key;

    public function __construct($blogID, $remoteID, $name, $key) {
        $this->blogID = $blogID;
        $this->remoteID = $remoteID;
        $this->name = $name;
        $this->key = $key;
    }

    public function getBlogID() {
        return $this->blogID;
    }

    public function getRemoteID() {
        return $this->remoteID;
    }

    public function getName() {
        return $this->name;
    }

    public function getKey() {
        return $this->key;
    }

    public function initialise() {
        global $wpdb;
        $name = $wpdb->get_blog_prefix($this->blogID) . self::ObjectPageMapTableSuffix;
        $sql = "
            CREATE TABLE $name (
            object_id bigint(20) NOT NULL,
            post_id bigint(2) NOT NULL,
            PRIMARY KEY  (object_id)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql, true);
    }

    public function getObjectForPage($pageID) {
        global $wpdb;
        $name = $wpdb->get_blog_prefix($this->blogID) . self::ObjectPageMapTableSuffix;
        return $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT object_id FROM $name WHERE post_id = %d",
                        $pageID));
    }

    public function getPageForObject($objectID) {
        global $wpdb;
        $name = $wpdb->get_blog_prefix($this->blogID) . self::ObjectPageMapTableSuffix;
        $pageID = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT post_id FROM $name WHERE object_id = %d",
                        $objectID));

        if($pageID != null)
            return $pageID;

        require_once('ArtMapsCoreServer.php');
        $core = new ArtMapsCoreServer($this);
        $metadata = $core->fetchObjectMetadata($objectID);
        require_once('ArtMapsTemplating.php');
        $te = new ArtMapsTemplating();
        $title = $te->renderMetadataTitleTemplate($this, $metadata);

        $post = array(
                'comment_status' => 'closed',
                'ping_status' => 'open',
                'post_title' => $title,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
        );
        $pageID = wp_insert_post($post);
        update_post_meta($pageID, "_wp_page_template", "template-object.php");
        $wpdb->insert($name,
                    array(
                            "object_id" => $objectID,
                            "post_id" => $pageID),
                    array("%d", "%d"));
        return $pageID;
    }

    public function getCommentTemplate() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::CommentTemplateOptionKey;
        return get_blog_option($this->getBlogID(), $k, '');
    }

    public function setCommentTemplate($template) {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::CommentTemplateOptionKey;
        update_blog_option($this->getBlogID(), $k, $template);
    }

    public function getMetadataFormatJS() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::MetadataFormatJSOptionKey;
        return get_blog_option($this->getBlogID(), $k, '');
    }

    public function setMetadataFormatJS($js) {
        $k = ArtMapsBlog::BlogConfigPrefix
               . ArtMapsBlog::MetadataFormatJSOptionKey;
        update_blog_option($this->getBlogID(), $k, $js);
    }

    public function getMetadataTitleJS() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::MetadataTitleJSOptionKey;
        return get_blog_option($this->getBlogID(), $k, '');
    }

    public function setMetadataTitleJS($js) {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::MetadataTitleJSOptionKey;
        update_blog_option($this->getBlogID(), $k, $js);
    }

    public function getSearchSource() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::SearchSourceOptionKey;
        return get_blog_option($this->getBlogID(), $k, 'artmaps');
    }

    public function setSearchSource($source) {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::SearchSourceOptionKey;
        update_blog_option($this->getBlogID(), $k, $source);
    }

    public function getSearchResultTitleJS() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::SearchResultTitleJSOptionKey;
        return get_blog_option($this->getBlogID(), $k, '');
    }

    public function setSearchResultTitleJS($js) {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::SearchResultTitleJSOptionKey;
        update_blog_option($this->getBlogID(), $k, $js);
    }

    public function getMetadataTitleTemplate() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::MetadataTitleTemplateKey;
        return get_blog_option($this->getBlogID(), $k, '');
    }

    public function setMetadataTitleTemplate($source) {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::MetadataTitleTemplateKey;
        update_blog_option($this->getBlogID(), $k, $source);
    }

    public function getObjectMetadataFormatJS() {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::ObjectMetadataFormatJSOptionKey;
        return get_blog_option($this->getBlogID(), $k, '');
    }

    public function setObjectMetadataFormatJS($js) {
        $k = ArtMapsBlog::BlogConfigPrefix
                . ArtMapsBlog::ObjectMetadataFormatJSOptionKey;
        update_blog_option($this->getBlogID(), $k, $js);
    }
}}
?>