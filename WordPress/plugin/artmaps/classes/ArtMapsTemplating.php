<?php
require_once(ABSPATH . "/lib/Smarty-3.1.12/libs/Smarty.class.php");
if(!class_exists("ArtMapsTemplating")) {
class ArtMapsTemplating {

    private $smarty;

    public function __construct() {
        $smarty = new Smarty();
        $dir = plugin_dir_path(__FILE__) . "templates";
        $smarty->setTemplateDir($dir);
        $smarty->setCompileDir("$dir/.compilation");
        $smarty->setCacheDir("$dir/.cache");
        $smarty->setConfigDir("$dir/.configuration");
        $this->smarty = $smarty;
    }

    public function getCommentTemplate($objectID, $link, $metadata) {
        $this->smarty->setCaching(true);
        $this->smarty->assign("objectID", $objectID);
        $this->smarty->assign("link", $link);
        $this->smarty->assign("metadata", $metadata);
        $res = $this->smarty->fetch("comment_template.tpl", $objectID);
        $this->smarty->clearAllAssign();
        return $res;
    }
}}
?>