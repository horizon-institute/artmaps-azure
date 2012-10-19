<?php
if(!class_exists("ArtMapsUserNotFoundException")){
class ArtMapsUserNotFoundException
extends Exception {
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}}

if(!class_exists("ArtMapsUser")) {
class ArtMapsUser {

    private $wpUser;

    public function __construct(WP_User $wpUser) {
        $this->wpUser = $wpUser;
    }

    public static function currentUser() {
        return new ArtMapsUser(wp_get_current_user());
    }

    public static function fromID($id) {
        return ArtMapsUser::from("id", $id);
    }

    public static function fromSlug($slug) {
        return ArtMapsUser::from("slug", $slug);
    }

    public static function fromEmail($email) {
        return ArtMapsUser::from("email", $email);
    }

    public static function fromLogin($login) {
        return ArtMapsUser::from("login", $login);
    }

    private static function from($field, $value) {
        $u = get_user_by($field, $value);
        if($u === false)
            throw new ArtMapsUserNotFoundException(
                    "User with $field '$value' does not exist");
        return new ArtMapsUser($u);
    }

    public function getID() {
        return $this->wpUser->ID;
    }

    public function getLogin() {
        return $this->wpUser->user_login;
    }

    public function getRoles() {
        return $this->wpUser->roles;
    }

    public function getSecondaryBlog(ArtMapsSite $site) {
        $key = "artmaps_blog_config";
        $cfg = get_user_meta($this->getID(), $key, true);
        if($cfg == null || $cfg == "") {
            $b = $site->createSecondaryBlog($this);
            $cfg = array(
                    "IsInternal" => true,
                    "ExternalURL" => "",
                    "ExternalUsername" => "",
                    "ExternalPassword" => "",
                    "InternalURL" => $b->url,
                    "InternalUsername" => $b->username,
                    "InternalPassword" => $b->password
                );
            update_user_meta($this->getID(), $key, $cfg);
        }

        $res = array();
        if($cfg["IsInternal"]) {
            $res["url"] = $cfg["InternalURL"];
            $res["username"] = $cfg["InternalUsername"];
            $res["password"] = $cfg["InternalPassword"];
        } else {
            $res["url"] = $cfg["ExternalURL"];
            $res["username"] = $cfg["ExternalUsername"];
            $res["password"] = $cfg["ExternalPassword"];
        }
        return (object)$res;
    }

    public function hasOpenID() {
        include_once(ABSPATH . "wp-admin/includes/plugin.php");
        if(!is_plugin_active("openid/openid.php"))
            return false;
        global $wpdb;
        $res = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT COUNT(1) FROM "
                        . openid_identity_table()
                        . " WHERE user_id = %s",
                        $this->wpUser->ID));
        return $res != 0;
    }
}}
?>