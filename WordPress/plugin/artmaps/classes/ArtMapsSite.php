<?php
if(!class_exists("ArtMapsSiteNotFoundException")){
class ArtMapsSiteNotFoundException
extends Exception {
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
}

if(!class_exists("ArtMapsSite")) {
class ArtMapsSite {

    private $id;

    private $name;

    private function __construct($id) {
        $this->id = $id;
    }

    public static function currentSite() {
        return new ArtMapsSite(get_current_blog_id());
    }

    public function getID() {
        return $this->id;
    }

    public function getName() {
        if(isset($this->name))
            return $this->name;
        global $wpdb;
        $this->name = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT name FROM "
                            . $wpdb->base_prefix
                            . ArtMapsKeys::TableSuffix
                            . " WHERE site_id = %d",
                        $this->id));
        if($this->name === null) {
            unset($this->name);
            throw new ArtMapsSiteNotFoundException();
        }
        return $this->name;
    }

    public function createSecondaryBlog(ArtMapsUser $user) {
        //TODO: Generate a secondary blog at a configured Wordpress instance
        $res = array(
                "username" => uniqid($user->getLogin(), true),
                "password" => uniqid("", true),
                "url" => "http://$un.am.wp.horizon.ac.uk"
            );
        return (object)$res;
    }
}}
?>